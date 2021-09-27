<?php
/**
 * Test for lightning_get_theme_options
 *
 * @package vektor-inc/lightning
 */

/**
 * Lightning_Get_THeme_Options_Test
 */
class Lightning_Get_THeme_Options_Test extends WP_UnitTestCase {

	/**
	 * Check lightning_get_theme_options()
	 *
	 * @return void
	 */
	public function test_lightning_get_theme_options() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_get_theme_options()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

		$test_array = array(
			array(
				'lightning_theme_options' => null,
				'target_options_key'      => 'color_key',
				'correct'                 => '#337ab7',
			),
		);
		foreach ( $test_array as $key => $value ) {

			update_option( 'lightning_theme_options', $value['lightning_theme_options'] );

			$return = lightning_get_theme_options();
			$return = $return[ $value['target_options_key'] ];
			print 'return  :' . esc_html( $return ) . PHP_EOL;
			print 'correct :' . esc_html( $value['correct'] ) . PHP_EOL;
			$this->assertEquals( $return, $value['correct'] );

		}
	}
}
