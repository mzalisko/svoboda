<?php
/**
 * CPT `order` — зберігає замовлення (паперова/електронна книга).
 *
 * Навмисно НЕ WooCommerce: єдине замовлення = 1 запис order з мета-полями.
 * Мета-поля (не ACF, бо це внутрішні службові дані, не контент для редагування):
 * - _svoboda_status         : new | paid | failed
 * - _svoboda_type           : paper | ebook
 * - _svoboda_np_city        : місто Нової Пошти
 * - _svoboda_np_warehouse   : відділення Нової Пошти
 * - _svoboda_monobank_invoice_id : ID інвойсу Monobank
 * - _svoboda_customer_name / _phone / _email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function svoboda_register_order_cpt() {
	register_post_type(
		'order',
		array(
			'label'        => 'Замовлення',
			'labels'       => array(
				'name'          => 'Замовлення',
				'singular_name' => 'Замовлення',
				'add_new_item'  => 'Додати замовлення',
				'edit_item'     => 'Редагувати замовлення',
				'search_items'  => 'Пошук замовлень',
			),
			'public'       => false,
			'show_ui'      => true,
			'show_in_menu' => true,
			'menu_icon'    => 'dashicons-cart',
			'supports'     => array( 'title' ),
			'capability_type' => 'post',
			'map_meta_cap' => true,
			'show_in_rest' => false, // внутрішні дані, не публічний REST-контент
		)
	);

	register_post_meta(
		'order',
		'_svoboda_status',
		array(
			'type'         => 'string',
			'single'       => true,
			'show_in_rest' => false,
			'default'      => 'new',
		)
	);

	foreach ( array( '_svoboda_type', '_svoboda_np_city', '_svoboda_np_warehouse', '_svoboda_monobank_invoice_id', '_svoboda_customer_name', '_svoboda_customer_phone', '_svoboda_customer_email' ) as $meta_key ) {
		register_post_meta(
			'order',
			$meta_key,
			array(
				'type'         => 'string',
				'single'       => true,
				'show_in_rest' => false,
			)
		);
	}
}
add_action( 'init', 'svoboda_register_order_cpt' );

/**
 * Показувати статус/тип замовлення колонкою у списку в адмінці —
 * без цього довелось би відкривати кожен запис окремо, щоб побачити статус оплати.
 */
function svoboda_order_admin_columns( $columns ) {
	$columns['order_status'] = 'Статус';
	$columns['order_type']   = 'Тип';
	return $columns;
}
add_filter( 'manage_order_posts_columns', 'svoboda_order_admin_columns' );

function svoboda_order_admin_column_content( $column, $post_id ) {
	if ( 'order_status' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_svoboda_status', true ) ?: 'new' );
	}
	if ( 'order_type' === $column ) {
		echo esc_html( get_post_meta( $post_id, '_svoboda_type', true ) );
	}
}
add_action( 'manage_order_posts_custom_column', 'svoboda_order_admin_column_content', 10, 2 );

/**
 * Створити замовлення. Викликається з обробника форми (Contact Form 7 hook
 * `wpcf7_before_send_mail` або WPForms `wpforms_process_complete`) —
 * конкретна інтеграція форми додається окремо після вибору плагіна.
 *
 * @param array $data { type, np_city, np_warehouse, customer_name, customer_phone, customer_email }
 * @return int Post ID.
 */
function svoboda_create_order( array $data ) {
	$order_id = wp_insert_post(
		array(
			'post_type'   => 'order',
			'post_status' => 'publish',
			'post_title'  => sprintf( 'Замовлення #%s — %s', time(), $data['customer_name'] ?? '' ),
		)
	);

	if ( is_wp_error( $order_id ) || ! $order_id ) {
		return 0;
	}

	update_post_meta( $order_id, '_svoboda_status', 'new' );
	foreach ( array( 'type', 'qty_paper', 'qty_ebook', 'payment', 'promo', 'np_city', 'np_warehouse', 'customer_name', 'customer_phone', 'customer_email' ) as $field ) {
		if ( isset( $data[ $field ] ) ) {
			update_post_meta( $order_id, '_svoboda_' . $field, sanitize_text_field( $data[ $field ] ) );
		}
	}

	return $order_id;
}
