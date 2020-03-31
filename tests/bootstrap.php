<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Lightning
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?" . PHP_EOL;
	exit( 1 );
}

require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	$theme_name = getenv( 'LIGHTNING_THEME_NAME' );
	if ( ! $theme_name ) {
		$theme_name = 'lightning';
	}
	register_theme_directory( dirname( __FILE__ ) . '/../../' );
	search_theme_directories();
	switch_theme( $theme_name );
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
