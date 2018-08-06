<?php

/*
$ vagrant ssh
$ cd $(wp theme path --dir lightning)
$ bash bin/install-wp-tests.sh wordpress_test root 'WordPress' localhost latest
$ phpunit
*/

class LightningDeactivePluginTest extends WP_UnitTestCase {
function test_lightning_deactivate_plugin() {

	print PHP_EOL;
	print '------------------------------------' . PHP_EOL;
	print 'is_lightning_deactivate_plugin' . PHP_EOL;
	print '------------------------------------' . PHP_EOL;

	/* まずは Advanced Unit を有効化する
	/*--------------------------------*/
	$plugin_path    = 'lightning-advanced-unit/lightning_advanced_unit.php';
	$active_plugins = get_option( 'active_plugins' );
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	if ( ! is_plugin_active( $plugin_path ) ) {
		$active_plugins[] = 'lightning-advanced-unit/lightning_advanced_unit.php';
	}
	update_option( 'active_plugins', $active_plugins );

	lightning_deactivate_plugin( $plugin_path );

	$return = is_plugin_active( $plugin_path );

	$this->assertEquals( false, $return );

}
