<?php
/**
 * LTG_G3_Slider の自動再生の停止・再生ボタンのテスト
 *
 * スライド枚数（0 / 1 / 2 枚）ごとのマークアップ出力、
 * lightning_top_slide_autoplay_toggle_display フィルターでの非表示、
 * および JS へ渡す設定値（enqueue と localize のペイロード）を検証する.
 *
 * @package vektor-inc/lightning
 */

/**
 * LTG_G3_Slider_Autoplay_Toggle_Test
 */
class LTG_G3_Slider_Autoplay_Toggle_Test extends WP_UnitTestCase {

	/**
	 * 各テスト後にオプションと登録済みスクリプトを片付ける
	 */
	public function tearDown(): void {
		delete_option( 'lightning_theme_options' );
		// localize したデータは登録が残っていると次のケースに追記されるため必ず解除する.
		wp_dequeue_script( 'ltg-g3-slider' );
		wp_deregister_script( 'ltg-g3-slider' );
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
	 * add_slide_script() を実行して localize されたデータ文字列を取得するヘルパー
	 *
	 * @return string wp_localize_script() が出力する `var ltgG3SliderOpt = {...};` の文字列.
	 */
	private function get_localized_data() {
		// 前のケースの登録が残っているとデータが追記されるので毎回解除してから実行する.
		wp_dequeue_script( 'ltg-g3-slider' );
		wp_deregister_script( 'ltg-g3-slider' );

		LTG_G3_Slider::add_slide_script();

		return (string) wp_scripts()->get_data( 'ltg-g3-slider', 'data' );
	}

	/**
	 * LTG_G3_Slider::get_slide_html() がスライド枚数に応じてボタンを出力するか
	 *
	 * ボタンの出力位置（.swiper-wrapper より前・.swiper-pagination の外）も同時に検証する.
	 * 止める手段は止めたい対象より前に無いと、動き続けるスライド内のリンクを
	 * 通り抜けないと到達できなくなる.
	 *
	 * @return void
	 */
	public function test_get_slide_html() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::get_slide_html()' . PHP_EOL;
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
			$toggle_position = strpos( $html, 'class="ltg-slide-autoplay-toggle' );
			$actual          = false !== $toggle_position;

			print PHP_EOL . $case['test_condition_name'] . ' : actual = ' . var_export( $actual, true ) . PHP_EOL;

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );

			if ( ! $case['expected'] ) {
				continue;
			}

			// 出力される場合は位置も検証する.
			$wrapper_position    = strpos( $html, 'class="swiper-wrapper ltg-slide-inner"' );
			$pagination_position = strpos( $html, 'class="swiper-pagination' );

			// ボタン → .swiper-wrapper → .swiper-pagination の順.
			$this->assertLessThan( $wrapper_position, $toggle_position, $case['test_condition_name'] . '（ボタンは .swiper-wrapper より前）' );
			$this->assertLessThan( $pagination_position, $wrapper_position, $case['test_condition_name'] . '（.swiper-pagination は .swiper-wrapper より後）' );

			// .ltg-slide の最初の子要素として出力される.
			$this->assertStringContainsString(
				'ltg-slide"><button type="button" class="ltg-slide-autoplay-toggle',
				$html,
				$case['test_condition_name'] . '（ボタンは .ltg-slide の最初の子要素）'
			);
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
				'conditions'          => array( 'marker' => '<button type="button" class="ltg-slide-autoplay-toggle swiper-no-swiping" hidden>' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => 'ボタンを押したまま指が動いてもスワイプに化けないよう swiper-no-swiping を付ける',
				'conditions'          => array( 'marker' => 'swiper-no-swiping' ),
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
			$actual_in_markup = false !== strpos( $html, 'class="ltg-slide-autoplay-toggle' );

			if ( $filter_callback ) {
				remove_filter( 'lightning_top_slide_autoplay_toggle_display', $filter_callback );
			}

			print PHP_EOL . $case['test_condition_name'] . ' : actual = ' . var_export( $actual, true ) . PHP_EOL;

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
			$this->assertSame( $case['expected'], $actual_in_markup, $case['test_condition_name'] . '（マークアップ）' );
		}
	}

	/**
	 * LTG_G3_Slider::swiper_paras() が既定値と引数をマージした配列を返すか
	 *
	 * @return void
	 */
	public function test_swiper_paras() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::swiper_paras()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テストの配列（引数と、戻り値の特定キーに対する期待値）.
		$test_cases = array(
			array(
				'test_condition_name' => '引数なしの場合 => 既定値の loop = true',
				'conditions'          => array(
					'paras' => '',
					'key'   => 'loop',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '引数なしの場合 => 既定値の slidesPerView = 1',
				'conditions'          => array(
					'paras' => '',
					'key'   => 'slidesPerView',
				),
				'expected'            => 1,
			),
			array(
				'test_condition_name' => '引数なしの場合 => 既定値の autoplay.delay = 2000',
				'conditions'          => array(
					'paras' => '',
					'key'   => 'autoplay',
				),
				'expected'            => array( 'delay' => 2000 ),
			),
			array(
				'test_condition_name' => 'loop = false を渡した場合 => 既定値を上書きする',
				'conditions'          => array(
					'paras' => array( 'loop' => false ),
					'key'   => 'loop',
				),
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'speed を渡した場合 => 既定値に無いキーが追加される',
				'conditions'          => array(
					'paras' => array( 'speed' => 1500 ),
					'key'   => 'speed',
				),
				'expected'            => 1500,
			),
			array(
				'test_condition_name' => '一部だけ渡した場合 => 渡していない既定値は残る',
				'conditions'          => array(
					'paras' => array( 'speed' => 1500 ),
					'key'   => 'navigation',
				),
				'expected'            => array(
					'nextEl' => '.swiper-button-next',
					'prevEl' => '.swiper-button-prev',
				),
			),
		);

		foreach ( $test_cases as $case ) {

			$paras  = LTG_G3_Slider::swiper_paras( $case['conditions']['paras'] );
			$actual = $paras[ $case['conditions']['key'] ];

			print PHP_EOL . $case['test_condition_name'] . ' : actual = ' . var_export( $actual, true ) . PHP_EOL;

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * LTG_G3_Slider::swiper_paras_json() が配列版と同じ内容の JSON を返すか
	 *
	 * @return void
	 */
	public function test_swiper_paras_json() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::swiper_paras_json()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テストの配列（引数と、JSON 文字列に含まれるべき断片）.
		$test_cases = array(
			array(
				'test_condition_name' => '引数なしの場合 => 既定値の loop が真偽値で入る',
				'conditions'          => array( 'paras' => '' ),
				'expected'            => '"loop":true',
			),
			array(
				'test_condition_name' => '引数なしの場合 => 既定値の delay が数値で入る',
				'conditions'          => array( 'paras' => '' ),
				'expected'            => '"delay":2000',
			),
			array(
				'test_condition_name' => 'speed を渡した場合 => 数値で入る',
				'conditions'          => array( 'paras' => array( 'speed' => 1500 ) ),
				'expected'            => '"speed":1500',
			),
		);

		foreach ( $test_cases as $case ) {

			$json = LTG_G3_Slider::swiper_paras_json( $case['conditions']['paras'] );

			print PHP_EOL . $case['test_condition_name'] . ' : json = ' . $json . PHP_EOL;

			$this->assertStringContainsString( $case['expected'], $json, $case['test_condition_name'] );
		}
	}

	/**
	 * LTG_G3_Slider::add_slide_script() が JS へ渡す設定値
	 *
	 * 期待値を `"delay":4000` のように JSON の断片で書いているのは、
	 * `"delay":"4000"` のような文字列化を検出するため.
	 * wp_localize_script() はトップレベルのスカラーだけを文字列へキャストするので、
	 * ネストした params の型が保たれることがこの機能の前提になっている.
	 * esc_attr() などのエスケープ関数を通す実装に戻すと、この期待値は満たせなくなる.
	 *
	 * @return void
	 */
	public function test_add_slide_script() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::add_slide_script()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テストの配列（オプションの条件と、localize されたデータに含まれる／含まれないべき断片）.
		$test_cases = array(
			array(
				'test_condition_name' => '待ち時間 4000 の場合 => 文字列ではなく数値で渡る',
				'conditions'          => array(
					'options' => array( 'top_slide_time' => 4000 ),
					'marker'  => '"delay":4000',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '待ち時間 4000 の場合 => 文字列の "4000" では渡らない',
				'conditions'          => array(
					'options' => array( 'top_slide_time' => 4000 ),
					'marker'  => '"delay":"4000"',
				),
				'expected'            => false,
			),
			array(
				'test_condition_name' => '待ち時間が数値文字列の場合 => 数値へ変換して渡る',
				'conditions'          => array(
					'options' => array( 'top_slide_time' => '2500' ),
					'marker'  => '"delay":2500',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '待ち時間が未入力の場合 => 既定値の 4000 に戻す',
				'conditions'          => array(
					'options' => array( 'top_slide_time' => '' ),
					'marker'  => '"delay":4000',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '待ち時間が負数の場合 => 即時発火でスライドが暴走しないよう既定値に戻す',
				'conditions'          => array(
					'options' => array( 'top_slide_time' => '-5000' ),
					'marker'  => '"delay":4000',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '待ち時間が負数の場合 => 負数のまま渡らない',
				'conditions'          => array(
					'options' => array( 'top_slide_time' => '-5000' ),
					'marker'  => '"delay":-5000',
				),
				'expected'            => false,
			),
			array(
				'test_condition_name' => '待ち時間が 0 の場合 => 既定値に戻す',
				'conditions'          => array(
					'options' => array( 'top_slide_time' => '0' ),
					'marker'  => '"delay":4000',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '切り替え時間 2000 の場合 => 文字列ではなく数値で渡る',
				'conditions'          => array(
					'options' => array( 'top_slide_speed' => '2000' ),
					'marker'  => '"speed":2000',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '切り替え時間 2000 の場合 => 文字列の "2000" では渡らない',
				'conditions'          => array(
					'options' => array( 'top_slide_speed' => '2000' ),
					'marker'  => '"speed":"2000"',
				),
				'expected'            => false,
			),
			array(
				'test_condition_name' => 'スライド2枚の場合 => loop が真偽値の true で渡る',
				'conditions'          => array(
					'options' => array(
						'top_slide_image_1' => 'http://example.com/slide1.jpg',
						'top_slide_image_2' => 'http://example.com/slide2.jpg',
					),
					'marker'  => '"loop":true',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => 'スライド1枚の場合 => 行き先が無いので loop が false で渡る',
				'conditions'          => array(
					'options' => array(
						'top_slide_image_1' => 'http://example.com/slide1.jpg',
						'top_slide_image_2' => '',
						'top_slide_image_3' => '',
					),
					'marker'  => '"loop":false',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '効果が未入力の場合 => slide にフォールバックする',
				'conditions'          => array(
					'options' => array( 'top_slide_effect' => '' ),
					'marker'  => '"effect":"slide"',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '接頭辞が既定の場合 => セレクタが .lightning_swiper になる',
				'conditions'          => array(
					'options' => array( 'top_slide_prefix' => 'lightning_' ),
					'marker'  => '"selector":".lightning_swiper"',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '接頭辞が既定の場合 => 旧実装と同じグローバル変数名を渡す',
				'conditions'          => array(
					'options' => array( 'top_slide_prefix' => 'lightning_' ),
					'marker'  => '"instance":"lightning_swiper"',
				),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '接頭辞にクラス名に使えない文字が含まれる場合 => 除去した値でセレクタを組む',
				'conditions'          => array(
					'options' => array( 'top_slide_prefix' => '</script><b>_' ),
					'marker'  => '"selector":".scriptb_swiper"',
				),
				'expected'            => true,
			),
			array(
				/*
				 * WP_Scripts::localize() が html_entity_decode() をかけるため、
				 * esc_html() / esc_attr() でエスケープしても元の文字へ戻ってしまう.
				 * クラス名として使える文字だけに限定していれば < は残らない.
				 */
				'test_condition_name' => '接頭辞にクラス名に使えない文字が含まれる場合 => データに < が残らない',
				'conditions'          => array(
					'options' => array( 'top_slide_prefix' => '</script><b>_' ),
					'marker'  => '<',
				),
				'expected'            => false,
			),
		);

		foreach ( $test_cases as $case ) {

			// オプション値を設定.
			$this->set_slider_options( $case['conditions']['options'] );

			$data   = $this->get_localized_data();
			$actual = false !== strpos( $data, $case['conditions']['marker'] );

			print PHP_EOL . $case['test_condition_name'] . ' : actual = ' . var_export( $actual, true ) . PHP_EOL;

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] . PHP_EOL . 'data = ' . $data );
		}

		// 登録内容（読み込むファイル・依存・enqueue 済みか）も検証する.
		$this->set_slider_options();
		$this->get_localized_data();

		$script = wp_scripts()->query( 'ltg-g3-slider', 'registered' );

		print PHP_EOL . 'src = ' . $script->src . PHP_EOL;
		print 'deps = ' . implode( ', ', $script->deps ) . PHP_EOL;

		// インラインスクリプトではなく独立した JS ファイルを読み込む.
		$this->assertStringContainsString( '/inc/ltg-g3-slider/package/js/ltg-g3-slider.js', $script->src, '独立した JS ファイルを読み込む' );
		// Swiper 本体より後に読み込まれる必要がある.
		$this->assertContains( 'vk-swiper-script', $script->deps, 'vk-swiper-script に依存している' );
		// enqueue されている.
		$this->assertTrue( wp_script_is( 'ltg-g3-slider', 'enqueued' ), 'ltg-g3-slider が enqueue されている' );
	}
}
