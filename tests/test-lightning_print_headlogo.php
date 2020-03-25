<?php
/**
 * Class lightning_print_headlogo test
 *
 * @package VK_Component_Button
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
class LightningPrintHeadLogoTest extends WP_UnitTestCase {

	function test_lightning_print_headlogo() {
		$test_array = array(
			// 画像未登録
			array(
				'lightning_theme_options' => array(),
				'blogname'                => 'test_title',
				'correct'                 => 'test_title',
			),
			// 画像配列はあるけど空
			array(
				'lightning_theme_options' => array(
					'head_logo' => ''
				),
				'blogname'                => 'test_title',
				'correct'                 => 'test_title',
			),
			// 画像登録済み
			array(
				'lightning_theme_options' => array(
					'head_logo' => 'https://a.jp/a.jpg'
				),
				'blogname'                => 'test_title',
				'correct'                 => '<img src="https://a.jp/a.jpg" alt="test_title" />',
			),
		);
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_print_headlogo()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $value ) {
			delete_option( 'lightning_theme_options' );
			delete_option( 'blogname' );
			update_option( 'lightning_theme_options', $value['lightning_theme_options'] );
			update_option( 'blogname', $value['blogname'] );

			$result = lightning_get_print_headlogo();
			print 'return  :' . $result . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $result );
		}
	}

}
