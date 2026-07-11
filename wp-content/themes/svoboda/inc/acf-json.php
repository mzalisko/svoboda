<?php
/**
 * Local JSON: ACF field groups редагуються через адмінку (edit.php?post_type=acf-field-group)
 * і одночасно версіонуються як JSON у git — стандартний production-патерн ACF/SCF.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter(
	'acf/settings/save_json',
	function () {
		return SVOBODA_THEME_DIR . '/acf-json';
	}
);

add_filter(
	'acf/settings/load_json',
	function ( $paths ) {
		unset( $paths[0] );
		$paths[] = SVOBODA_THEME_DIR . '/acf-json';
		return $paths;
	}
);
