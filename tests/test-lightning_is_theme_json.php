<?php
/**
 * Class Lightning_Rename_Theme_Json_Test
 *
 * @package vektor-inc/lightning
 */

/**
 * Lightning_Rename_Theme_Json_Test.
 */
class Lightning_Rename_Theme_Json_Test extends WP_UnitTestCase {

	function test_llightning_rename_theme_json() {
		$test_array = array(
			// Lightning を初めてインストールする場合.
			// lightning_theme_options 自体まだ存在しない.
			array(
				'lightning_theme_options' => null,
				'expected'                => true,
			),
			// 既存の Lightning のサイト（まだ theme.json を有効化していない）.
			// まだ lightning_theme_options はあるが lightning_theme_options[theme_json] は存在しない.
			array(
				'lightning_theme_options' => array(
					'sample' => true,
				),
				'expected'                => false,
			),
			// 手動で theme.json を有効化した場合.
			array(
				'lightning_theme_options' => array(
					'theme_json' => true,
				),
				'expected'                => true,
			),
			// 手動で theme.json を無効化した場合.
			array(
				'lightning_theme_options' => array(
					'theme_json' => false,
				),
				'expected'                => false,
			),
		);
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_rename_theme_json()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $value ) {

			if ( ! empty( $value['lightning_theme_options'] ) ) {
				update_option( 'lightning_theme_options', $value['lightning_theme_options'] );
			} else {
				delete_option( 'lightning_theme_options' );
			}
			if ( $value['expected'] ) {
				$expected_rename = 'theme.json';
			} else {
				$expected_rename = '_theme.json';
			}

			$this->assertEquals( $value['expected'], lightning_is_theme_json() );

			$actual = lightning_rename_theme_json();

			// 対象の theme.json ファイルが存在するかどうか.
			// $actual = is_readable( get_parent_theme_file_path( $file ) );

			print 'return  :' . esc_attr( $actual ) . PHP_EOL;
			print 'expected :' . esc_attr( $expected_rename ) . PHP_EOL;
			$this->assertEquals( $expected_rename, $actual );
		}
	}

}
