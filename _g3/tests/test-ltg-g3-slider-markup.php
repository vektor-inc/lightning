<?php
/**
 * LTG_G3_Slider::get_slide_html() が出力するクラス名のテスト
 *
 * Swiper 側で廃止されたクラス名（swiper-container / swiper-pagination-white /
 * swiper-button-white）が出力に残っていないこと、および現行の Swiper・
 * Lightning 独自クラスが消えていないことを検証する.
 *
 * @package vektor-inc/lightning
 */

class LTG_G3_Slider_Markup_Test extends WP_UnitTestCase {

	/**
	 * テスト終了ごとにスライダーのオプションを削除する.
	 */
	public function tearDown(): void {
		delete_option( 'lightning_theme_options' );
		wp_cache_flush();
		parent::tearDown();
	}

	/**
	 * スライダーのオプションを既定値にマージして保存する.
	 *
	 * @param array $overrides 既定値に上書きするオプション.
	 * @return void
	 */
	private function set_slider_options( $overrides = array() ) {
		$defaults = lightning_g3_slider_default_options();
		$options  = array_merge( $defaults, $overrides );
		update_option( 'lightning_theme_options', $options );
		wp_cache_flush();
	}

	/**
	 * LTG_G3_Slider::get_slide_html() のテスト.
	 *
	 * @return void
	 */
	public function test_get_slide_html() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::get_slide_html()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// Swiper 側に定義が存在しない（廃止済み）クラス名. どのケースでも出力されてはいけない.
		$dead_classes = array(
			'swiper-container',
			'swiper-pagination-white',
			'swiper-button-white',
		);

		$test_cases = array(
			array(
				'test_condition_name' => 'スライド画像2枚（既定値）の場合 => 廃止クラスなし・現行クラスとページネーション・矢印あり',
				'conditions'          => array(
					'options' => array(),
				),
				'expected'            => array(
					// 出力されていなければならない文字列.
					'contains'     => array(
						'class="lightning_swiper swiper ltg-slide"',
						'class="swiper-wrapper ltg-slide-inner"',
						'class="swiper-pagination"',
						'class="ltg-slide-button-next swiper-button-next"',
						'class="ltg-slide-button-prev swiper-button-prev"',
					),
					// 出力されていてはいけない文字列.
					'not_contains' => $dead_classes,
					// 空文字列を期待するかどうか.
					'is_empty'     => false,
				),
			),
			array(
				'test_condition_name' => 'スライド画像1枚の場合 => 廃止クラスなし・ページネーションと矢印は出力しない',
				'conditions'          => array(
					'options' => array(
						'top_slide_image_2' => '',
					),
				),
				'expected'            => array(
					'contains'     => array(
						'class="lightning_swiper swiper ltg-slide"',
						'class="swiper-wrapper ltg-slide-inner"',
					),
					'not_contains' => array(
						'swiper-container',
						'swiper-pagination-white',
						'swiper-button-white',
						// 1枚のときはページネーション・矢印自体を出力しない仕様.
						'swiper-pagination',
						'swiper-button-next',
						'swiper-button-prev',
					),
					'is_empty'     => false,
				),
			),
			array(
				'test_condition_name' => '接頭辞を変更した場合 => 接頭辞付きクラスは残り swiper-container は出力されない',
				'conditions'          => array(
					'options' => array(
						'top_slide_prefix' => 'my_prefix_',
					),
				),
				'expected'            => array(
					'contains'     => array(
						'class="my_prefix_swiper swiper ltg-slide"',
					),
					'not_contains' => $dead_classes,
					'is_empty'     => false,
				),
			),
			array(
				'test_condition_name' => 'スライド画像が1枚もない場合（境界値） => 空文字列',
				'conditions'          => array(
					'options' => array(
						'top_slide_image_1' => '',
						'top_slide_image_2' => '',
						'top_slide_image_3' => '',
					),
				),
				'expected'            => array(
					'contains'     => array(),
					'not_contains' => $dead_classes,
					'is_empty'     => true,
				),
			),
		);

		foreach ( $test_cases as $case ) {

			// 条件のオプションを設定する.
			$this->set_slider_options( $case['conditions']['options'] );

			// テスト対象メソッドを実行する.
			$html = LTG_G3_Slider::get_slide_html();

			print PHP_EOL . $case['test_condition_name'] . PHP_EOL;

			// 空文字列を期待するケース.
			if ( $case['expected']['is_empty'] ) {
				print '  出力: "' . $html . '"' . PHP_EOL;
				$this->assertSame( '', $html, $case['test_condition_name'] );
			}

			// 出力されていなければならない文字列を検証する.
			foreach ( $case['expected']['contains'] as $needle ) {
				print '  contains: ' . $needle . PHP_EOL;
				$this->assertStringContainsString( $needle, $html, $case['test_condition_name'] . ' / ' . $needle );
			}

			// 出力されていてはいけない文字列を検証する.
			foreach ( $case['expected']['not_contains'] as $needle ) {
				print '  not contains: ' . $needle . PHP_EOL;
				$this->assertStringNotContainsString( $needle, $html, $case['test_condition_name'] . ' / ' . $needle );
			}

			// オプションを削除して次のケースに影響させない.
			delete_option( 'lightning_theme_options' );
			wp_cache_flush();
		}
	}
}
