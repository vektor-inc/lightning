<?php
/**
 * Lightning_Is_G3_Test
 *
 * @package vektor-inc/lightning
 */

/**
 * Class Lightning_Is_G3_Test
 */
class Lightning_Is_G3_Test extends WP_UnitTestCase {

	public function test_lightning_is_g3() {
		$test_array = array(
			array(
				'name'    => 'theme_options : null / theme_generation : null => G3',
				'options' => array(
					'lightning_theme_options'    => null,
					'lightning_theme_generation' => null,
				),
				'expected' => true,
			),
			array(
				'name'    => 'theme_options : null / theme_generation : g3 => G3',
				'options' => array(
					'lightning_theme_options'    => null,
					'lightning_theme_generation' => 'g3',
				),
				'expected' => true,
			),
			array(
				'name'    => 'theme_options : null / theme_generation : g2 => G2',
				'options' => array(
					'lightning_theme_options'    => null,
					'lightning_theme_generation' => 'g2',
				),
				'expected' => false,
			),
			array(
				'name'    => 'theme_skin :origin2 / theme_generation :null => G2',
				'options' => array(
					'lightning_design_skin'      => 'origin2',
					'lightning_theme_generation' => null,
				),
				'expected' => false,
			),
			array(
				'name'    => 'theme_skin :origin3 / theme_generation :null => G3',
				'options' => array(
					'lightning_design_skin'      => 'origin3',
					'lightning_theme_generation' => null,
				),
				'expected' => true,
			),
			array(
				'name'    => 'theme_json 有効 / theme_generation : null => G3',
				'options' => array(
					'lightning_theme_options'    => array( 'theme_json' => true ),
					'lightning_theme_generation' => null,
				),
				'expected' => true,
			),
		);

		foreach ( $test_array as $value ) {
			if ( isset( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_name => $option_value ) {
					update_option( $option_name, $option_value );
				}
			}

			$actual = lightning_is_g3();
			$this->assertEquals( $value['expected'], $actual, $value['name'] );

			if ( isset( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_name => $option_value ) {
					delete_option( $option_name );
				}
			}
		}
	}
}
