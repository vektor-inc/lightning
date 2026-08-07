<?php
/**
 * LTG_G3_Slider の自動再生の停止・再生ボタンのテスト
 *
 * スライド枚数（0 / 1 / 2 枚）ごとのマークアップ出力と、
 * lightning_top_slide_autoplay_toggle_display フィルターでの非表示を検証する.
 *
 * @package vektor-inc/lightning
 */

/**
 * LTG_G3_Slider_Autoplay_Toggle_Test
 */
class LTG_G3_Slider_Autoplay_Toggle_Test extends WP_UnitTestCase {

	/**
	 * 各テスト後にオプションを片付ける
	 */
	public function tearDown(): void {
		delete_option( 'lightning_theme_options' );
		wp_cache_flush();
		parent::tearDown();
	}

	/**
	 * スライダーのオプションを設定するヘルパー
	 *
	 * @param array $overrides 既定値に上書きするオプション.
	 */
	private function set_slider_options( $overrides = array() ) {
		$defaults = lightning_g3_slider_default_options();
		$options  = array_merge( $defaults, $overrides );
		update_option( 'lightning_theme_options', $options );
		wp_cache_flush();
	}

	/**
	 * LTG_G3_Slider::get_slide_html() がスライド枚数に応じてボタンを出力するか
	 *
	 * @return void
	 */
	public function test_get_slide_html() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::get_slide_html() autoplay toggle' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テストの配列（スライド枚数の条件と、ボタンが出力されるかの期待値）.
		$test_cases = array(
			array(
				'test_condition_name' => 'スライド画像が0枚の場合 => スライダー自体が出力されないのでボタンも出ない',
				'conditions'          => array(
					'options' => array(
						'top_slide_image_1' => '',
						'top_slide_image_2' => '',
						'top_slide_image_3' => '',
					),
				),
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'スライド画像が1枚の場合 => loop = false で視覚的な動きが起きないのでボタンは出ない',
				'conditions'          => array(
					'options' => array(
						'top_slide_image_1' => 'http://example.com/slide1.jpg',
						'top_slide_image_2' => '',
						'top_slide_image_3' => '',
					),
				),
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'スライド画像が2枚の場合 => 矢印・ドットと同じ条件なのでボタンが出る',
				'conditions'          => array(
					'options' => array(
						'top_slide_image_1' => 'http://example.com/slide1.jpg',
						'top_slide_image_2' => 'http://example.com/slide2.jpg',
						'top_slide_image_3' => '',
					),
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => 'スライド画像が3枚の場合 => ボタンが出る',
				'conditions'          => array(
					'options' => array(
						'top_slide_image_1' => 'http://example.com/slide1.jpg',
						'top_slide_image_2' => 'http://example.com/slide2.jpg',
						'top_slide_image_3' => 'http://example.com/slide3.jpg',
					),
				),
				'expected'            => true,
			),
		);

		foreach ( $test_cases as $case ) {

			// オプション値を設定.
			$this->set_slider_options( $case['conditions']['options'] );

			$html = LTG_G3_Slider::get_slide_html();

			// ボタンが出力されているか.
			$actual = false !== strpos( $html, 'class="ltg-slide-autoplay-toggle"' );

			print PHP_EOL . $case['test_condition_name'] . ' : actual = ' . var_export( $actual, true ) . PHP_EOL;

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * LTG_G3_Slider::get_autoplay_toggle_html() の出力内容
	 *
	 * @return void
	 */
	public function test_get_autoplay_toggle_html() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::get_autoplay_toggle_html()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$html = LTG_G3_Slider::get_autoplay_toggle_html();

		print PHP_EOL . $html . PHP_EOL;

		// テストの配列（出力に含まれる／含まれないべきマーカー）.
		$test_cases = array(
			array(
				'test_condition_name' => 'div ではなく button 要素で出力される',
				'conditions'          => array( 'marker' => '<button type="button" class="ltg-slide-autoplay-toggle" hidden>' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '停止用セットが表示状態で出力される（JS が動かない場合の既定）',
				'conditions'          => array( 'marker' => '<span class="ltg-slide-autoplay-toggle-stop">' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '再生用セットは hidden で出力される',
				'conditions'          => array( 'marker' => '<span class="ltg-slide-autoplay-toggle-start" hidden>' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => 'アクセシブルネームとして停止側の文言が入る',
				'conditions'          => array( 'marker' => '<span class="screen-reader-text">Stop automatic slide show</span>' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => 'アクセシブルネームとして再生側の文言が入る',
				'conditions'          => array( 'marker' => '<span class="screen-reader-text">Start automatic slide show</span>' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => 'SVG は支援技術から隠す',
				'conditions'          => array( 'marker' => 'aria-hidden="true" focusable="false"' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '二重読み上げの原因になる aria-label は付けない',
				'conditions'          => array( 'marker' => 'aria-label' ),
				'expected'            => false,
			),
			array(
				'test_condition_name' => '状態が判別できなくなる aria-pressed は付けない',
				'conditions'          => array( 'marker' => 'aria-pressed' ),
				'expected'            => false,
			),
			array(
				'test_condition_name' => '二重読み上げの原因になる title 属性は付けない',
				'conditions'          => array( 'marker' => 'title=' ),
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'ライブリージョンは追加しない',
				'conditions'          => array( 'marker' => 'aria-live' ),
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'aria-controls は付けない',
				'conditions'          => array( 'marker' => 'aria-controls' ),
				'expected'            => false,
			),
		);

		foreach ( $test_cases as $case ) {
			$actual = false !== strpos( $html, $case['conditions']['marker'] );
			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * LTG_G3_Slider::is_autoplay_toggle_display() がフィルターを反映するか
	 *
	 * @return void
	 */
	public function test_is_autoplay_toggle_display() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::is_autoplay_toggle_display()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テストの配列（フィルターの戻り値と、ボタンを出力するかの期待値）.
		$test_cases = array(
			array(
				'test_condition_name' => 'フィルター未指定の場合 => 既定で表示する',
				'conditions'          => array( 'filter_return' => null ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => 'フィルターが true を返す場合 => 表示する',
				'conditions'          => array( 'filter_return' => true ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => 'フィルターが false を返す場合 => 表示しない',
				'conditions'          => array( 'filter_return' => false ),
				'expected'            => false,
			),
		);

		// スライド2枚（ボタンが出る条件）を設定.
		$this->set_slider_options(
			array(
				'top_slide_image_1' => 'http://example.com/slide1.jpg',
				'top_slide_image_2' => 'http://example.com/slide2.jpg',
				'top_slide_image_3' => '',
			)
		);

		foreach ( $test_cases as $case ) {

			$filter_callback = null;

			if ( null !== $case['conditions']['filter_return'] ) {
				$filter_return   = $case['conditions']['filter_return'];
				$filter_callback = function () use ( $filter_return ) {
					return $filter_return;
				};
				add_filter( 'lightning_top_slide_autoplay_toggle_display', $filter_callback );
			}

			$actual = LTG_G3_Slider::is_autoplay_toggle_display();

			// フィルターが有効な間にマークアップ側も確認する.
			$html             = LTG_G3_Slider::get_slide_html();
			$actual_in_markup = false !== strpos( $html, 'class="ltg-slide-autoplay-toggle"' );

			if ( $filter_callback ) {
				remove_filter( 'lightning_top_slide_autoplay_toggle_display', $filter_callback );
			}

			print PHP_EOL . $case['test_condition_name'] . ' : actual = ' . var_export( $actual, true ) . PHP_EOL;

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
			$this->assertSame( $case['expected'], $actual_in_markup, $case['test_condition_name'] . '（マークアップ）' );
		}
	}

	/**
	 * ボタンが .swiper-wrapper より前・.swiper-pagination の外に出力されるか
	 *
	 * 止める手段は止めたい対象より前に置く必要がある.
	 * また Swiper はページネーションの innerHTML を再レンダリングごとに書き換えるため、
	 * その中に入れた要素は消える.
	 *
	 * @return void
	 */
	public function test_get_slide_html_toggle_position() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::get_slide_html() toggle position' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->set_slider_options(
			array(
				'top_slide_image_1' => 'http://example.com/slide1.jpg',
				'top_slide_image_2' => 'http://example.com/slide2.jpg',
				'top_slide_image_3' => '',
			)
		);

		$html = LTG_G3_Slider::get_slide_html();

		$toggle_position     = strpos( $html, 'class="ltg-slide-autoplay-toggle"' );
		$wrapper_position    = strpos( $html, 'class="swiper-wrapper ltg-slide-inner"' );
		$pagination_position = strpos( $html, 'class="swiper-pagination' );

		print PHP_EOL . 'toggle = ' . var_export( $toggle_position, true ) . ' / wrapper = ' . var_export( $wrapper_position, true ) . ' / pagination = ' . var_export( $pagination_position, true ) . PHP_EOL;

		// ボタン → .swiper-wrapper → .swiper-pagination の順で出力される.
		$this->assertLessThan( $wrapper_position, $toggle_position, 'ボタンは .swiper-wrapper より前に出力される' );
		$this->assertLessThan( $pagination_position, $wrapper_position, '.swiper-pagination は .swiper-wrapper より後に出力される' );

		// .ltg-slide の最初の子要素として出力される.
		$this->assertStringContainsString( 'ltg-slide"><button type="button" class="ltg-slide-autoplay-toggle"', $html, 'ボタンは .ltg-slide の最初の子要素' );
	}
}
