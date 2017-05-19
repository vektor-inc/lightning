<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Bill_Vektor
 */

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';
require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	register_theme_directory( dirname( __FILE__ ) . '/../../' );
	switch_theme('bill-vektor');
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
