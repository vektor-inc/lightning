<?php
/**
 * Class Lightning_Is_Theme_Json_Test
 *
 * @package vektor-inc/lightning
 */

/**
 * Lightning_Is_Theme_Json_Test.
 */
class Lightning_Is_Theme_Json_Test extends WP_UnitTestCase {

	function test_lightning_is_theme_json() {
		$test_array = array(
			// Lightning を初めてインストールする場合.
			array(
				'lightning_theme_options' => null,
				'expected'                => true,
			),
			// 手動で theme.json を有効化した場合.
			array(
				'lightning_theme_options' => array(
					'theme_json' => true,
				),
				'expected'                => true,
			),
			// 既存の Lightning のサイト（まだ theme.json を有効化していない）.
			array(
				'lightning_theme_options' => array(
					'sample' => true,
				),
				'expected'                => false,
			),
			// theme.json を使わない指定.
			array(
				'lightning_theme_options' => array(
					'theme_json' => false,
				),
				'expected'                => false,
			),
		);
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_is_theme_json()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $value ) {

			if ( ! empty( $value['lightning_theme_options'] ) ) {
				update_option( 'lightning_theme_options', $value['lightning_theme_options'] );
			} else {
				delete_option( 'lightning_theme_options' );
			}

			$actual = lightning_is_theme_json();
			print 'return  :' . $actual . PHP_EOL;
			print 'expected :' . $value['expected'] . PHP_EOL;
			$this->assertEquals( $value['expected'], $actual );
		}
	}

}
