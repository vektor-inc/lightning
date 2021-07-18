<?php
/**
 * Class Lightning_Is_G3_Test test
 *
 * @package Lightning_Is_G3_Test
 *
 * cd /app
 * bash setup-phpunit.sh
 * source ~/.bashrc
 * cd $(wp theme path --dir lightning)
 * phpunit
 */

/**
 * Sample test case.
 */
class Lightning_Is_G3_Test extends WP_UnitTestCase {

	function test_lightning_is_g3() {
		$test_array = array(
			array(
				'lightning_theme_options'    => null,
				'lightning_theme_generation' => null,
				'correct'                    => true,
			),
			array(
				'lightning_theme_options'    => null,
				'lightning_theme_generation' => 'g3',
				'correct'                    => true,
			),
			array(
				'lightning_theme_options'    => null,
				'lightning_theme_generation' => 'g2',
				'correct'                    => false,
			),
			// array(
			// 	'lightning_design_skin'      => 'origin2',
			// 	'lightning_theme_generation' => null,
			// 	'correct'                    => false,
			// ),
			// array(
			// 	'lightning_design_skin'      => 'origin3',
			// 	'lightning_theme_generation' => null,
			// 	'correct'                    => true,
			// ),
		);
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_is_g3()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		delete_option( 'lightning_theme_generation' );

		foreach ( $test_array as $key => $value ) {

			if ( isset( $value['lightning_theme_options'] ) && $value['lightning_theme_options'] === null ) {
				delete_option( 'lightning_theme_options' );
			} else {
				update_option( 'lightning_theme_options', $value['lightning_theme_options'] );
			}

			if ( $value['lightning_theme_generation'] === null ) {
				delete_option( 'lightning_theme_generation' );
			} else {
				update_option( 'lightning_theme_generation', $value['lightning_theme_generation'] );
			}

			// if ( $value['lightning_design_skin'] === null ) {
			// 	delete_option( 'lightning_design_skin' );
			// } else {
			// 	update_option( 'lightning_design_skin', $value['lightning_design_skin'] );
			// }

			$result = lightning_is_g3();
			print 'return  :' . $result . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $result );
		}
	}

}
