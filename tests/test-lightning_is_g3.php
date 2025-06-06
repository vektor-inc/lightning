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
				'name'    => '新規サイト => G3',
				'options' => array(
					'fresh_site' => '1',
				),
				'expected' => true,
			),
			// Generation 指定あり
			array(
				'name'    => 'theme_generation : g3 => G3',
				'options' => array(
					'fresh_site' => '0',
					'lightning_theme_generation' => 'g3',
				),
				'expected' => true,
			),
			array(
				'name'    => 'スキンが Origin2 でも G3 指定優先',
				'options' => array(
					'fresh_site' => '0',
					'lightning_theme_generation' => 'g3',
					'lightning_design_skin'      => 'origin2',
				),
				'expected' => true,
			),
			array(
				'name'    => 'theme_generation : g2 => G2',
				'options' => array(
					'fresh_site' => '0',
					'lightning_theme_generation' => 'g2',
				),
				'expected' => false,
			),
			array(
				'name'    => 'スキンが Origin3 でも G2 指定優先',
				'options' => array(
					'fresh_site' => '0',
					'lightning_theme_generation' => 'g2',
					'lightning_design_skin'      => 'origin3',
				),
				'expected' => false,
			),
			// Generation 未指定
			array(
				'name'    => 'lightning_design_skin : origin3 / theme_generation : null => G3',
				'options' => array(
					'fresh_site' => '0',
					'lightning_design_skin'      => 'origin3',
					'lightning_theme_generation' => null,
				),
				'expected' => true,
			),
			array(
				'name'    => 'lightning_design_skin : origin2 => G2',
				'options' => array(
					'fresh_site' => '0',
					'lightning_design_skin'      => 'origin2',
				),
				'expected' => false,
			),
			array(
				'name'    => 'lightning_design_skin : origin2 / theme_generation : null => G2',
				'options' => array(
					'fresh_site' => '0',
					'lightning_design_skin'      => 'origin2',
					'lightning_theme_generation' => null,
				),
				'expected' => false,
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_is_g3()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $value ) {

			// 初期化
			delete_option( 'fresh_site' );
			delete_option( 'lightning_theme_generation' );
			delete_option( 'lightning_design_skin' );

			// テストのループ中に保存した option 値がキャッシュされて誤動作するので強制的に一度クリアする
			wp_cache_flush();

			if ( isset( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_name => $option_value ) {
					update_option( $option_name, $option_value );
				}
			}

			$actual = lightning_is_g3();
			$this->assertEquals( $value['expected'], $actual, $value['name'] );

		}
	}
}
