<?php
/**
 * REST-проксі до публічного API Нової Пошти.
 *
 * Ключ API НІКОЛИ не потрапляє у фронтенд — читається лише server-side
 * з константи NOVAPOSHTA_API_KEY (wp-config.php) або, якщо не задана,
 * з ACF Options (менш безпечно, лишено як fallback для локальної розробки).
 *
 * Відповіді кешуються через Transient API на кілька годин, щоб не бити
 * по лімітах публічного API Нової Пошти при кожному введенні символу в полі міста.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const SVOBODA_NP_API_URL      = 'https://api.novaposhta.ua/v2.0/json/';
const SVOBODA_NP_CACHE_TTL    = 6 * HOUR_IN_SECONDS;

/**
 * Дістати ключ API. Пріоритет: константа в wp-config.php > ACF Options.
 */
function svoboda_get_novaposhta_api_key() {
	if ( defined( 'NOVAPOSHTA_API_KEY' ) && NOVAPOSHTA_API_KEY ) {
		return NOVAPOSHTA_API_KEY;
	}
	if ( function_exists( 'get_field' ) ) {
		return get_field( 'novaposhta_api_key', 'option' );
	}
	return '';
}

function svoboda_register_novaposhta_routes() {
	register_rest_route(
		'svoboda/v1',
		'/novaposhta/cities',
		array(
			'methods'             => 'GET',
			'callback'            => 'svoboda_novaposhta_cities_callback',
			'permission_callback' => '__return_true',
			'args'                => array(
				'query' => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);

	register_rest_route(
		'svoboda/v1',
		'/novaposhta/warehouses',
		array(
			'methods'             => 'GET',
			'callback'            => 'svoboda_novaposhta_warehouses_callback',
			'permission_callback' => '__return_true',
			'args'                => array(
				'city_ref' => array(
					'required'          => true,
					'sanitize_callback' => 'sanitize_text_field',
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'svoboda_register_novaposhta_routes' );

/**
 * Спільна функція виклику Нової Пошти з кешуванням через transient.
 */
function svoboda_novaposhta_request( $model_name, $called_method, $method_properties, $cache_key ) {
	$cached = get_transient( $cache_key );
	if ( false !== $cached ) {
		return $cached;
	}

	$api_key = svoboda_get_novaposhta_api_key();
	if ( ! $api_key ) {
		return new WP_Error( 'np_no_api_key', 'API-ключ Нової Пошти не налаштовано.', array( 'status' => 500 ) );
	}

	$response = wp_remote_post(
		SVOBODA_NP_API_URL,
		array(
			'timeout' => 8,
			'body'    => wp_json_encode(
				array(
					'apiKey'            => $api_key,
					'modelName'         => $model_name,
					'calledMethod'      => $called_method,
					'methodProperties'  => $method_properties,
				)
			),
			'headers' => array( 'Content-Type' => 'application/json' ),
		)
	);

	if ( is_wp_error( $response ) ) {
		return $response;
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( empty( $body['success'] ) ) {
		return new WP_Error( 'np_api_error', 'Помилка запиту до Нової Пошти.', array( 'status' => 502 ) );
	}

	$data = $body['data'] ?? array();
	set_transient( $cache_key, $data, SVOBODA_NP_CACHE_TTL );

	return $data;
}

function svoboda_novaposhta_cities_callback( WP_REST_Request $request ) {
	$query     = $request->get_param( 'query' );
	$cache_key = 'svoboda_np_cities_' . md5( strtolower( $query ) );

	$result = svoboda_novaposhta_request(
		'Address',
		'getCities',
		array( 'FindByString' => $query ),
		$cache_key
	);

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return rest_ensure_response( $result );
}

function svoboda_novaposhta_warehouses_callback( WP_REST_Request $request ) {
	$city_ref  = $request->get_param( 'city_ref' );
	$cache_key = 'svoboda_np_warehouses_' . md5( $city_ref );

	$result = svoboda_novaposhta_request(
		'Address',
		'getWarehouses',
		array( 'CityRef' => $city_ref ),
		$cache_key
	);

	if ( is_wp_error( $result ) ) {
		return $result;
	}

	return rest_ensure_response( $result );
}
