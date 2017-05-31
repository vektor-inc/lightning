<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Lightning
 */

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';
require_once $_tests_dir . '/includes/functions.php';

function _manually_load_plugin() {
	register_theme_directory( dirname( __FILE__ ) . '/../../' );
	$a = wp_get_theme();
	print '<pre style="text-align:left">';print_r($a);print '</pre>';
	switch_theme('lightning');
	echo '━━━━━━━━━━━━━━━━━━━━'."<br>\n";
		$a = wp_get_theme();
	print '<pre style="text-align:left">';print_r($a);print '</pre>';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $_tests_dir . '/includes/bootstrap.php';
