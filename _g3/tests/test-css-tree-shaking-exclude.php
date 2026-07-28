<?php
/**
 * CSS ツリーシェイキングの除外設定に関するテスト
 *
 * スライダーの矢印スタイル（.swiper-navigation-icon）は Swiper の JS が
 * 実行時に注入するクラスに対する指定のため、サーバー出力の HTML には存在しない。
 * そのため除外登録が無いとツリーシェイキングでセレクタごと削除されてしまう。
 * ここではその除外登録と、実際にシェイキングを通した際の挙動を検証する。
 *
 * @package vektor-inc/lightning
 */

use VektorInc\VK_CSS_Optimize\CSS_tree_shaking;

class CSS_Tree_Shaking_Exclude_Test extends WP_UnitTestCase {

	/**
	 * Lightning のスライダーが出力するマークアップ相当の最小 HTML.
	 *
	 * class-ltg-g3-slider.php の get_slide_html() が出力する構造に合わせている。
	 * swiper-navigation-icon は JS が後から注入するクラスのため、
	 * サーバー出力を模した以下の HTML には意図的に含めていない。
	 *
	 * @var string
	 */
	const SLIDER_HTML = '<html><body>'
		. '<div class="swiper swiper-container ltg-slide">'
		. '<div class="swiper-wrapper ltg-slide-inner">'
		. '<div class="swiper-slide item"></div>'
		. '</div>'
		. '<div class="swiper-pagination swiper-pagination-white"></div>'
		. '<div class="ltg-slide-button-next swiper-button-next swiper-button-white"></div>'
		. '<div class="ltg-slide-button-prev swiper-button-prev swiper-button-white"></div>'
		. '</div>'
		. '</body></html>';

	/**
	 * ツリーシェイキング対象となる CSS（simple_minify 済み相当）.
	 *
	 * @var string
	 */
	const SLIDER_CSS = '.ltg-slide .swiper-button-next::after{font-size:1.5em}'
		. '.ltg-slide .swiper-button-next .swiper-navigation-icon{height:1.5em;width:auto}'
		. '.ltg-slide .swiper-button-prev .swiper-navigation-icon{height:1.5em;width:auto}'
		. '.ltg-slide .ltg-slide-not-in-html{color:red}';

	/**
	 * テストの前処理.
	 *
	 * class-css-tree-shaking.php は PSR-4 のファイル命名規則に沿っていないため
	 * composer のオートロードでは読み込まれず、VkCssOptimize 内で必要になった時に
	 * require_once される。テストからは直接使うためここで読み込んでおく。
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		if ( ! class_exists( CSS_tree_shaking::class ) ) {
			require_once __DIR__ . '/../../vendor/vektor-inc/vk-css-optimize/src/class-css-tree-shaking.php';
		}
	}

	/**
	 * CSS_tree_shaking が内部に持つ解析結果キャッシュを初期化する.
	 *
	 * extended_minify() は $cmplist['parse'] が立っていると HTML 解析と
	 * css_tree_shaking_exclude フィルタの適用をスキップするため、
	 * フィルタの有無を切り替えて検証するにはリセットが必須。
	 *
	 * @return void
	 */
	private function reset_tree_shaking_cache() {
		$property = new ReflectionProperty( CSS_tree_shaking::class, 'cmplist' );
		$property->setAccessible( true );
		$property->setValue( null, null );
	}

	/**
	 * 除外クラス一覧に必要なクラスが登録されているかのテスト.
	 *
	 * 誰かが除外リストの行を消した場合に検知できるようにするための安価なガード。
	 *
	 * @return void
	 */
	public function test_lightning_css_tree_shaking_exclude_class() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_css_tree_shaking_exclude_class()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// 入力となる初期値（vk-css-optimize が渡す構造に合わせる）.
		$inidata = array(
			'id'    => array(),
			'class' => array( 'dummy-initial-class' ),
			'tag'   => array( 'html' ),
		);

		$actual = lightning_css_tree_shaking_exclude_class( $inidata );

		// テストの配列.
		$test_cases = array(
			array(
				'test_condition_name' => 'Swiper が JS で注入する矢印アイコンのクラスが除外登録されている => swiper-navigation-icon を含む',
				'conditions'          => array( 'class_name' => 'swiper-navigation-icon' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '既存の除外クラスが維持されている => customize-partial-edit-shortcut を含む',
				'conditions'          => array( 'class_name' => 'customize-partial-edit-shortcut' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '呼び出し元から渡された既存要素が失われていない => dummy-initial-class を含む',
				'conditions'          => array( 'class_name' => 'dummy-initial-class' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '登録していないクラスは含まれない => not-registered-class を含まない',
				'conditions'          => array( 'class_name' => 'not-registered-class' ),
				'expected'            => false,
			),
		);

		foreach ( $test_cases as $case ) {
			// 除外リストに対象クラスが含まれるかを判定.
			$result = in_array( $case['conditions']['class_name'], $actual['class'], true );
			print $case['conditions']['class_name'] . ' : ' . var_export( $result, true ) . ' / correct = ' . var_export( $case['expected'], true ) . PHP_EOL;
			$this->assertSame( $case['expected'], $result, $case['test_condition_name'] );
		}

		// id / tag のキーが壊れていないことも確認する.
		$this->assertArrayHasKey( 'id', $actual, '返り値に id キーが維持されている' );
		$this->assertArrayHasKey( 'tag', $actual, '返り値に tag キーが維持されている' );
	}

	/**
	 * 実際にツリーシェイキングを通した際にスライダー矢印のスタイルが残るかのテスト.
	 *
	 * 同梱の vk-css-optimize はツリーシェイキングを強制無効化しているが、
	 * CSS_tree_shaking を直接呼べばシェイキング自体の挙動は検証できる。
	 *
	 * @return void
	 */
	public function test_extended_minify() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'CSS_tree_shaking::extended_minify() ( swiper-navigation-icon )' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// テストの配列.
		// expected は「そのルールがシェイキング後に残るか」を CSS ルール単位で示す.
		$test_cases = array(
			array(
				'test_condition_name' => '除外フィルタが有効な場合 => JS 注入クラスの矢印スタイルが残る',
				'conditions'          => array( 'exclude_filter' => true ),
				'expected'            => array(
					'.ltg-slide .swiper-button-next .swiper-navigation-icon{height:1.5em;width:auto}' => true,
					'.ltg-slide .swiper-button-prev .swiper-navigation-icon{height:1.5em;width:auto}' => true,
					'.ltg-slide .swiper-button-next::after{font-size:1.5em}'                          => true,
					'.ltg-slide .ltg-slide-not-in-html{color:red}'                                    => false,
				),
			),
			array(
				'test_condition_name' => '除外フィルタを解除した場合（陰性の対照） => JS 注入クラスの矢印スタイルが削除される',
				'conditions'          => array( 'exclude_filter' => false ),
				'expected'            => array(
					'.ltg-slide .swiper-button-next .swiper-navigation-icon{height:1.5em;width:auto}' => false,
					'.ltg-slide .swiper-button-prev .swiper-navigation-icon{height:1.5em;width:auto}' => false,
					'.ltg-slide .swiper-button-next::after{font-size:1.5em}'                          => true,
					'.ltg-slide .ltg-slide-not-in-html{color:red}'                                    => false,
				),
			),
		);

		foreach ( $test_cases as $case ) {

			// 除外フィルタの有無を切り替える.
			if ( ! $case['conditions']['exclude_filter'] ) {
				remove_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );
			}

			// 前のケースの解析結果が残っていると条件が反映されないためリセットする.
			$this->reset_tree_shaking_cache();

			// ツリーシェイキング実行.
			$actual = CSS_tree_shaking::extended_minify( self::SLIDER_CSS, self::SLIDER_HTML );

			// 除外フィルタを元に戻す.
			if ( ! $case['conditions']['exclude_filter'] ) {
				add_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );
			}

			// 次のテストに解析結果を持ち越さないようリセットする.
			$this->reset_tree_shaking_cache();

			print PHP_EOL . $case['test_condition_name'] . PHP_EOL;
			print 'result css : ' . $actual . PHP_EOL;

			// 期待値テスト（ルール単位で残存・削除を判定）.
			foreach ( $case['expected'] as $rule => $should_remain ) {
				if ( $should_remain ) {
					$this->assertStringContainsString( $rule, $actual, $case['test_condition_name'] . ' / 残るべきルール : ' . $rule );
				} else {
					$this->assertStringNotContainsString( $rule, $actual, $case['test_condition_name'] . ' / 削除されるべきルール : ' . $rule );
				}
			}
		}
	}
}
