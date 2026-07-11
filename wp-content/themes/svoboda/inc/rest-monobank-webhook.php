<?php
/**
 * Monobank Acquiring: створення інвойсу + webhook підтвердження оплати.
 *
 * Токен Merchant API читається виключно server-side з константи
 * MONOBANK_TOKEN (wp-config.php) — ніколи не передається у JS.
 *
 * Безпека webhook: Monobank підписує тіло запиту приватним ключем, публічний
 * ключ для перевірки підпису віддає ендпоінт /api/merchant/pubkey.
 * Без цієї перевірки будь-хто міг би POST-нути на webhook і підробити "оплачено".
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SVOBODA_MONOBANK_API_URL = 'https://api.monobank.ua/api/merchant/invoice/create';
const SVOBODA_MONOBANK_PUBKEY_TRANSIENT = 'svoboda_monobank_pubkey';

function svoboda_get_monobank_token() {
	return defined( 'MONOBANK_TOKEN' ) ? MONOBANK_TOKEN : '';
}

function svoboda_register_monobank_routes() {
	register_rest_route(
		'svoboda/v1',
		'/order/create-invoice',
		array(
			'methods'             => 'POST',
			'callback'            => 'svoboda_create_invoice_callback',
			'permission_callback' => '__return_true',
		)
	);

	register_rest_route(
		'svoboda/v1',
		'/monobank/webhook',
		array(
			'methods'             => 'POST',
			'callback'            => 'svoboda_monobank_webhook_callback',
			'permission_callback' => '__return_true',
		)
	);
}
add_action( 'rest_api_init', 'svoboda_register_monobank_routes' );

/**
 * Крок 1: користувач надіслав форму замовлення → створюємо CPT `order`
 * зі статусом "new" → створюємо інвойс у Monobank → повертаємо pageUrl
 * для редіректу фронтенду на сторінку оплати.
 */
function svoboda_create_invoice_callback( WP_REST_Request $request ) {
	$params = $request->get_json_params();

	// Нова схема (модалка): qty_paper/qty_ebook — можна замовити обидві версії
	// одночасно з кількістю. Стара схема ('type') підтримується як 1 шт.
	$qty_paper = max( 0, min( 99, (int) ( $params['qty_paper'] ?? 0 ) ) );
	$qty_ebook = max( 0, min( 99, (int) ( $params['qty_ebook'] ?? 0 ) ) );

	$legacy_type = sanitize_text_field( $params['type'] ?? '' );
	if ( ! $qty_paper && ! $qty_ebook && in_array( $legacy_type, array( 'paper', 'ebook' ), true ) ) {
		$qty_paper = 'paper' === $legacy_type ? 1 : 0;
		$qty_ebook = 'ebook' === $legacy_type ? 1 : 0;
	}

	if ( ! $qty_paper && ! $qty_ebook ) {
		return new WP_Error( 'invalid_type', 'Невірний тип замовлення.', array( 'status' => 400 ) );
	}

	$front_id    = get_option( 'page_on_front' );
	$price_paper = (int) ( function_exists( 'get_field' ) ? get_field( 'price_paper', $front_id ) : 0 ) ?: 450;
	$price_ebook = (int) ( function_exists( 'get_field' ) ? get_field( 'price_ebook', $front_id ) : 0 ) ?: 290;

	// Сума рахується ВИКЛЮЧНО на сервері з ACF-цін — клієнтські ціни не приймаємо.
	$price_uah = $qty_paper * $price_paper + $qty_ebook * $price_ebook;

	$type = $qty_paper ? 'paper' : 'ebook'; // домінантний тип для існуючих фільтрів

	$order_id = svoboda_create_order(
		array(
			'type'           => $type,
			'qty_paper'      => (string) $qty_paper,
			'qty_ebook'      => (string) $qty_ebook,
			'payment'        => $params['payment'] ?? 'online',
			'promo'          => $params['promo'] ?? '',
			'np_city'        => $params['np_city'] ?? '',
			'np_warehouse'   => $params['np_warehouse'] ?? '',
			'customer_name'  => $params['customer_name'] ?? '',
			'customer_phone' => $params['customer_phone'] ?? '',
			'customer_email' => $params['customer_email'] ?? '',
		)
	);

	if ( ! $order_id ) {
		return new WP_Error( 'order_create_failed', 'Не вдалося створити замовлення.', array( 'status' => 500 ) );
	}

	$token = svoboda_get_monobank_token();
	if ( ! $token ) {
		return new WP_Error( 'no_monobank_token', 'Токен Monobank не налаштовано.', array( 'status' => 500 ) );
	}

	$response = wp_remote_post(
		SVOBODA_MONOBANK_API_URL,
		array(
			'timeout' => 10,
			'headers' => array(
				'X-Token'      => $token,
				'Content-Type' => 'application/json',
			),
			'body'    => wp_json_encode(
				array(
					'amount'      => $price_uah * 100, // копійки
					'ccy'         => 980, // UAH
					'redirectUrl' => home_url( '/dyakuyemo/' ),
					'webHookUrl'  => rest_url( 'svoboda/v1/monobank/webhook' ),
					'reference'   => (string) $order_id,
				)
			),
		)
	);

	if ( is_wp_error( $response ) ) {
		update_post_meta( $order_id, '_svoboda_status', 'failed' );
		return $response;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $body['invoiceId'] ) ) {
		update_post_meta( $order_id, '_svoboda_status', 'failed' );
		return new WP_Error( 'monobank_error', 'Помилка створення інвойсу Monobank.', array( 'status' => 502 ) );
	}

	update_post_meta( $order_id, '_svoboda_monobank_invoice_id', sanitize_text_field( $body['invoiceId'] ) );

	return rest_ensure_response(
		array(
			'orderId' => $order_id,
			'pageUrl' => $body['pageUrl'],
		)
	);
}

/**
 * Крок 2: Monobank шле webhook на статус оплати. ОБОВ'ЯЗКОВО перевіряємо
 * підпис (X-Sign, ECDSA/base64 публічним ключем із /pubkey), інакше будь-хто
 * може підробити запит "оплачено" й отримати книгу безкоштовно.
 */
function svoboda_monobank_webhook_callback( WP_REST_Request $request ) {
	$signature = $request->get_header( 'X-Sign' );
	$raw_body  = $request->get_body();

	if ( ! $signature || ! svoboda_verify_monobank_signature( $raw_body, $signature ) ) {
		return new WP_Error( 'invalid_signature', 'Невірний підпис webhook.', array( 'status' => 403 ) );
	}

	$payload    = json_decode( $raw_body, true );
	$invoice_id = sanitize_text_field( $payload['invoiceId'] ?? '' );
	$status     = sanitize_text_field( $payload['status'] ?? '' );

	if ( ! $invoice_id ) {
		return new WP_Error( 'missing_invoice_id', 'Відсутній invoiceId.', array( 'status' => 400 ) );
	}

	$orders = get_posts(
		array(
			'post_type'   => 'order',
			'meta_key'    => '_svoboda_monobank_invoice_id',
			'meta_value'  => $invoice_id,
			'numberposts' => 1,
		)
	);

	if ( empty( $orders ) ) {
		return new WP_Error( 'order_not_found', 'Замовлення не знайдено.', array( 'status' => 404 ) );
	}

	$order_id  = $orders[0]->ID;
	$new_status = 'success' === $status ? 'paid' : ( in_array( $status, array( 'failure', 'expired' ), true ) ? 'failed' : 'new' );
	update_post_meta( $order_id, '_svoboda_status', $new_status );

	return rest_ensure_response( array( 'ok' => true ) );
}

/**
 * Перевірка підпису webhook публічним ключем Monobank.
 * Публічний ключ кешується (він змінюється рідко) і оновлюється раз на добу.
 */
function svoboda_verify_monobank_signature( $raw_body, $signature_base64 ) {
	$pubkey_pem = get_transient( SVOBODA_MONOBANK_PUBKEY_TRANSIENT );

	if ( ! $pubkey_pem ) {
		$token = svoboda_get_monobank_token();
		$resp  = wp_remote_get(
			'https://api.monobank.ua/api/merchant/pubkey',
			array(
				'headers' => array( 'X-Token' => $token ),
				'timeout' => 8,
			)
		);
		if ( is_wp_error( $resp ) ) {
			return false;
		}
		$body       = json_decode( wp_remote_retrieve_body( $resp ), true );
		$pubkey_pem = base64_decode( $body['key'] ?? '' );
		if ( ! $pubkey_pem ) {
			return false;
		}
		set_transient( SVOBODA_MONOBANK_PUBKEY_TRANSIENT, $pubkey_pem, DAY_IN_SECONDS );
	}

	$signature = base64_decode( $signature_base64 );
	if ( false === $signature ) {
		return false;
	}

	// Monobank підписує ECDSA з SHA256.
	$result = openssl_verify( $raw_body, $signature, $pubkey_pem, OPENSSL_ALGO_SHA256 );

	return 1 === $result;
}
