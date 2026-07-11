<?php
/**
 * Bootstrap теми "Свобода".
 *
 * Тільки: enqueue, підключення inc/-модулів, базовий theme setup.
 * Вся бізнес-логіка (ACF-поля, CPT, REST) винесена в inc/, щоб цей файл
 * лишався коротким і читабельним.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SVOBODA_THEME_VERSION', '0.1.0' );
define( 'SVOBODA_THEME_DIR', get_template_directory() );
define( 'SVOBODA_THEME_URI', get_template_directory_uri() );

/**
 * Базове налаштування теми.
 */
function svoboda_theme_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'custom-logo' );

	// Тема односторінкова (one-page landing) — класичне меню не критичне,
	// але лишаємо на випадок якорних посилань у футері/шапці.
	register_nav_menus(
		array(
			'primary' => __( 'Головне меню (якорні посилання)', 'svoboda' ),
		)
	);
}
add_action( 'after_setup_theme', 'svoboda_theme_setup' );

/**
 * Підключення стилів і скриптів.
 */
function svoboda_enqueue_assets() {
	// Self-hosted Cormorant Garamond рекомендовано покласти в assets/fonts
	// і підключити тут через @font-face в style.css (див. TODO в assets/css/style.css).
	wp_enqueue_style(
		'svoboda-style',
		SVOBODA_THEME_URI . '/assets/css/style.css',
		array(),
		SVOBODA_THEME_VERSION
	);

	wp_enqueue_script(
		'svoboda-animations',
		SVOBODA_THEME_URI . '/assets/js/animations.js',
		array(),
		SVOBODA_THEME_VERSION,
		true
	);

	// Дані, потрібні JS без хардкоду (напр. поріг IntersectionObserver, rest nonce).
	wp_localize_script(
		'svoboda-animations',
		'svobodaData',
		array(
			'restUrl' => esc_url_raw( rest_url( 'svoboda/v1/' ) ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'svoboda_enqueue_assets' );

/**
 * Модулі теми.
 */
require_once SVOBODA_THEME_DIR . '/inc/acf-json.php';
require_once SVOBODA_THEME_DIR . '/inc/acf-fields.php';
require_once SVOBODA_THEME_DIR . '/inc/cpt-order.php';
require_once SVOBODA_THEME_DIR . '/inc/rest-novaposhta.php';
require_once SVOBODA_THEME_DIR . '/inc/rest-monobank-webhook.php';
