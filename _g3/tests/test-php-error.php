<?php
/**
 * Test for PHP Error
 *
 * @package Lightning G3
 */

require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/vendor/autoload.php';

use vktestphperror\VkTestPHPError;

/**
 * PHP_Fatal_Error_Test
 */
class PHP_Fatal_Error_Test extends WP_UnitTestCase {

	/**
	 * Check Fatal Error
	 *
	 * @return void
	 */

	public function test_php_faral_error() {

		$vk_test_php_error = new VkTestPHPError();

		$vk_test_php_error->test_title;

		$test_array = $vk_test_php_error->get_test_array();

		foreach ( $test_array as $value ) {
			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}

			// tree shaking を有効化した状態になるように.
			$vk_css_tree_shaking_array = array();
			lightning_css_tree_shaking_array( $vk_css_tree_shaking_array );

			// Move to test page.
			$this->go_to( $value['target_url'] );

			print PHP_EOL;
			print '-------------------' . PHP_EOL;
			print esc_url( $value['target_url'] ) . PHP_EOL;
			print '-------------------' . PHP_EOL;
			print PHP_EOL;
			require get_theme_file_path( 'index.php' );

			// PHPのエラーが発生するかどうかのチェックなので本当は不要.
			$this->assertEquals( true, true );

			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					delete_option( $option_key );
				}
			}
		}
	}
}
