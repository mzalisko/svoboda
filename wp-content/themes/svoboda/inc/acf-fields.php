<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function svoboda_acf_options_page() {
	if ( ! function_exists( 'acf_add_options_page' ) ) {
		return;
	}

	acf_add_options_page(
		array(
			'page_title' => 'Налаштування сайту',
			'menu_title' => 'Налаштування сайту',
			'menu_slug'  => 'svoboda-options',
			'capability' => 'manage_options',
			'redirect'   => false,
		)
	);
}
add_action( 'acf/init', 'svoboda_acf_options_page' );
