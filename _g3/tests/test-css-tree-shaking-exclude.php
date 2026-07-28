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

/**
 * Class CSS_Tree_Shaking_Exclude_Test
 */
class CSS_Tree_Shaking_Exclude_Test extends WP_UnitTestCase {

	/**
	 * class-css-tree-shaking.php の相対パス.
	 *
	 * PSR-4 のファイル命名規則に沿っていないため composer のオートロードでは
	 * 読み込まれない。ライブラリ側のファイル配置に依存するため、存在しない場合は
	 * fatal にせずスキップする。
	 *
	 * @var string
	 */
	const TREE_SHAKING_FILE = '/../../vendor/vektor-inc/vk-css-optimize/src/class-css-tree-shaking.php';

	/**
	 * ビルド済み CSS の格納ディレクトリ.
	 *
	 * @var string
	 */
	const BUILT_CSS_DIR = '/../assets/css/';

	/**
	 * Lightning のスライダーが出力するマークアップ相当の最小 HTML.
	 *
	 * class-ltg-g3-slider.php の get_slide_html() が出力する構造に合わせている
	 * （swiper が 2 つ並ぶのは 1 つ目が接頭辞と結合されるための実装どおりの姿）。
	 * swiper-navigation-icon は JS が後から注入するクラスのため、
	 * サーバー出力を模した以下の HTML には意図的に含めていない。
	 *
	 * @var string
	 */
	const SLIDER_HTML = '<html><body>'
		. '<div class="swiper swiper swiper-container ltg-slide">'
		. '<div class="swiper-wrapper ltg-slide-inner">'
		. '<div class="swiper-slide item-1"></div>'
		. '</div>'
		. '<div class="swiper-pagination swiper-pagination-white"></div>'
		. '<div class="ltg-slide-button-next swiper-button-next swiper-button-white"></div>'
		. '<div class="ltg-slide-button-prev swiper-button-prev swiper-button-white"></div>'
		. '</div>'
		. '</body></html>';

	/**
	 * スライダーを出力していないページ相当の HTML.
	 *
	 * 除外登録は「そのクラスを無条件に残す」のではなく
	 * 「そのクラスだけは HTML に無くても許容する」という指定のため、
	 * 祖先セレクタが HTML に無ければルールは削除される。
	 *
	 * @var string
	 */
	const NO_SLIDER_HTML = '<html><body>'
		. '<div class="site-body"><main class="entry-content"></main></div>'
		. '</body></html>';

	/**
	 * ツリーシェイキング対象となる CSS（simple_minify 済み相当）.
	 *
	 * 矢印スタイルと SP 非表示指定は、_g3/assets/css/style.css の実際のビルド出力を
	 * そのまま持ってきている（矢印スタイルはカンマ結合形で出力される）。
	 *
	 * @var string
	 */
	const SLIDER_CSS = '.ltg-slide .swiper-button-next::after{font-size:1.5em}'
		. '.ltg-slide .swiper-button-next .swiper-navigation-icon,.ltg-slide .swiper-button-prev .swiper-navigation-icon{height:1.5em;width:auto}'
		. '@media (max-width:575.98px){.ltg-slide .swiper-button-next,.ltg-slide .swiper-button-prev{display:none}}'
		. '.ltg-slide .ltg-slide-not-in-html{color:red}';

	/**
	 * テストの前処理.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		// 他テストの解析結果を持ち込まないよう無条件にリセットする.
		// vendor の読み込みは CSS_tree_shaking を実際に使うテスト側で行う
		// （ここで markTestSkipped すると vendor 非依存のテストまで巻き込まれるため）.
		$this->reset_tree_shaking_cache();
	}

	/**
	 * テストの後処理.
	 *
	 * extended_minify() が例外を投げた場合ループ内のリセットが実行されず
	 * static が汚染されたまま残るため、ここでも無条件にリセットする.
	 * 同じ理由で除外フィルタも無条件に復帰させる.
	 *
	 * @return void
	 */
	public function tear_down() {
		$this->reset_tree_shaking_cache();

		// 陰性の対照で外したまま後続テストへ漏れないよう、重複登録を避けつつ戻す.
		if ( ! has_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' ) ) {
			add_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );
		}

		parent::tear_down();
	}

	/**
	 * CSS_tree_shaking クラスを読み込む.
	 *
	 * PSR-4 のファイル命名規則に沿っていないため composer のオートロードでは
	 * 読み込まれない。ライブラリ側のファイル配置に依存するため、
	 * 存在しない場合は fatal にせず呼び出し元のテストのみスキップする.
	 *
	 * @return void
	 */
	private function require_tree_shaking_class() {
		if ( class_exists( CSS_tree_shaking::class ) ) {
			return;
		}
		$file = __DIR__ . self::TREE_SHAKING_FILE;
		if ( ! file_exists( $file ) ) {
			$this->markTestSkipped( 'vk-css-optimize の class-css-tree-shaking.php が見つからないためスキップします : ' . $file );
		}
		require_once $file;
	}

	/**
	 * CSS から SP 用メディアクエリ（max-width:575.98px）の中身だけを抜き出す.
	 *
	 * ビルド済み CSS は 1 行に minify されており、単純な正規表現では
	 * メディアクエリの内側かどうかを判定できないため、波括弧を数えて切り出す.
	 *
	 * @param string $css 対象の CSS 文字列.
	 * @return string マッチしたメディアクエリ内の CSS を連結した文字列.
	 */
	private function get_sp_media_css( $css ) {
		$result = '';

		// メディアクエリの開始位置をすべて拾う.
		if ( ! preg_match_all( '/@media\s*\(\s*max-width:\s*575\.98px\s*\)\s*\{/', $css, $matches, PREG_OFFSET_CAPTURE ) ) {
			return $result;
		}

		foreach ( $matches[0] as $match ) {
			// 開き波括弧の次の位置から、対応する閉じ波括弧までを走査する.
			$start = $match[1] + strlen( $match[0] );
			$depth = 1;
			$end   = $start;
			$len   = strlen( $css );

			while ( $end < $len && $depth > 0 ) {
				if ( '{' === $css[ $end ] ) {
					++$depth;
				} elseif ( '}' === $css[ $end ] ) {
					--$depth;
					if ( 0 === $depth ) {
						break;
					}
				}
				++$end;
			}

			$result .= substr( $css, $start, $end - $start );
		}

		return $result;
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
		if ( ! class_exists( CSS_tree_shaking::class ) ) {
			return;
		}
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
	 * ビルド済み CSS にスライダー矢印のルールが存在するかのテスト.
	 *
	 * 除外登録だけをテストしていると _slide.scss のルール自体が
	 * 消された場合に検知できないため、ビルド成果物にルールが
	 * 実在することをここで担保する。
	 *
	 * 本 PR の仕様変更の核は以下の 3 つで、どれか 1 つでも欠けると
	 * 矢印の表示が壊れるため、3 つすべてを検証する。
	 *
	 * 1. .swiper-navigation-icon の height ( Swiper v12 以降の SVG 矢印 )
	 * 2. ::after の font-size ( Swiper v11 のアイコンフォント矢印のフォールバック )
	 * 3. SP 幅での矢印非表示 ( ボタン自体に display: none )
	 *
	 * @return void
	 */
	public function test_built_css_has_swiper_navigation_icon() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'built css ( swiper navigation )' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// 検証するルールの定義（キーは expected 配列のキーと対応する）.
		// scope が 'sp_media' のものは @media (max-width:575.98px) の中身のみを対象にする.
		//
		// 注意 : パターン中の 1.5em は _g3/assets/_scss/components/_slide.scss の
		// 指定値と二重管理になっている。SCSS 側の値を変更した場合は
		// このパターンの数値も合わせて修正すること（修正しないとテストが落ちる）.
		$rule_patterns = array(
			'v12以降のSVG矢印の高さ ( .swiper-navigation-icon { height: 1.5em } )' => array(
				'scope'   => 'all',
				'pattern' => '/\.ltg-slide[^{}]*\.swiper-navigation-icon[^{}]*\{[^{}]*height:\s*1\.5em/',
			),
			'v11フォールバックの矢印サイズ ( ::after { font-size: 1.5em } )' => array(
				'scope'   => 'all',
				'pattern' => '/\.ltg-slide[^{}]*\.swiper-button-(?:next|prev)::after[^{}]*\{[^{}]*font-size:\s*1\.5em/',
			),
			'SP幅での矢印非表示 ( @media (max-width:575.98px) の display: none )' => array(
				'scope'   => 'sp_media',
				'pattern' => '/\.ltg-slide[^{}]*\.swiper-button-(?:next|prev)[^{}]*\{[^{}]*display:\s*none/',
			),
		);

		// テストの配列.
		// expected は「そのファイルに各ルールが存在するか」をルール単位で示す.
		$expected_all_exists = array(
			'v12以降のSVG矢印の高さ ( .swiper-navigation-icon { height: 1.5em } )' => true,
			'v11フォールバックの矢印サイズ ( ::after { font-size: 1.5em } )' => true,
			'SP幅での矢印非表示 ( @media (max-width:575.98px) の display: none )' => true,
		);

		$test_cases = array(
			array(
				'test_condition_name' => 'style.css にスライダー矢印の指定が存在する => 3 つとも存在する',
				'conditions'          => array( 'file_name' => 'style.css' ),
				'expected'            => $expected_all_exists,
			),
			array(
				'test_condition_name' => 'style_layout-active.css にスライダー矢印の指定が存在する => 3 つとも存在する',
				'conditions'          => array( 'file_name' => 'style_layout-active.css' ),
				'expected'            => $expected_all_exists,
			),
			array(
				'test_condition_name' => 'style-theme-json.css にスライダー矢印の指定が存在する => 3 つとも存在する',
				'conditions'          => array( 'file_name' => 'style-theme-json.css' ),
				'expected'            => $expected_all_exists,
			),
		);

		$tested = 0;

		foreach ( $test_cases as $case ) {
			$file = __DIR__ . self::BUILT_CSS_DIR . $case['conditions']['file_name'];

			// ビルド済み CSS が無い環境ではスキップする.
			if ( ! file_exists( $file ) ) {
				print $case['conditions']['file_name'] . ' : not built ( skip )' . PHP_EOL;
				continue;
			}

			$css = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			// SP 用メディアクエリの中身は毎回切り出さずに 1 回だけ取得する.
			$sp_media_css = $this->get_sp_media_css( $css );

			print PHP_EOL . $case['conditions']['file_name'] . PHP_EOL;

			// 期待値テスト（ルール単位で存在を判定）.
			foreach ( $case['expected'] as $rule_name => $should_exist ) {
				$rule    = $rule_patterns[ $rule_name ];
				$subject = ( 'sp_media' === $rule['scope'] ) ? $sp_media_css : $css;
				$result  = (bool) preg_match( $rule['pattern'], $subject );

				print '  ' . $rule_name . ' : ' . var_export( $result, true ) . ' / correct = ' . var_export( $should_exist, true ) . PHP_EOL;
				$this->assertSame( $should_exist, $result, $case['test_condition_name'] . ' / ' . $rule_name );
			}

			++$tested;
		}

		if ( ! $tested ) {
			$this->markTestSkipped( 'ビルド済み CSS が存在しないためスキップします : ' . __DIR__ . self::BUILT_CSS_DIR );
		}
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

		// このテストだけが vendor のクラスを必要とするため、ここで読み込む.
		$this->require_tree_shaking_class();

		// 読み込み直後の解析結果キャッシュを念のためリセットする.
		$this->reset_tree_shaking_cache();

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'CSS_tree_shaking::extended_minify() ( swiper-navigation-icon )' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$icon_rule  = '.ltg-slide .swiper-button-next .swiper-navigation-icon,.ltg-slide .swiper-button-prev .swiper-navigation-icon{height:1.5em;width:auto}';
		$media_rule = '.ltg-slide .swiper-button-next,.ltg-slide .swiper-button-prev{display:none}';

		// テストの配列.
		// expected は「そのルールがシェイキング後に残るか」を CSS ルール単位で示す.
		$test_cases = array(
			array(
				'test_condition_name' => '除外フィルタが有効でスライダーが出力されている場合 => JS 注入クラスの矢印スタイルが残る',
				'conditions'          => array(
					'exclude_filter' => true,
					'html'           => self::SLIDER_HTML,
				),
				'expected'            => array(
					$icon_rule  => true,
					$media_rule => true,
					'.ltg-slide .swiper-button-next::after{font-size:1.5em}' => true,
					'.ltg-slide .ltg-slide-not-in-html{color:red}' => false,
				),
			),
			array(
				'test_condition_name' => '除外フィルタが有効でもスライダーが出力されていない場合 => 祖先セレクタが無いので矢印スタイルは削除される',
				'conditions'          => array(
					'exclude_filter' => true,
					'html'           => self::NO_SLIDER_HTML,
				),
				'expected'            => array(
					$icon_rule  => false,
					$media_rule => false,
					'.ltg-slide .swiper-button-next::after{font-size:1.5em}' => false,
					'.ltg-slide .ltg-slide-not-in-html{color:red}' => false,
				),
			),
			array(
				'test_condition_name' => '除外フィルタを解除した場合（陰性の対照） => JS 注入クラスの矢印スタイルが削除される',
				'conditions'          => array(
					'exclude_filter' => false,
					'html'           => self::SLIDER_HTML,
				),
				'expected'            => array(
					$icon_rule  => false,
					$media_rule => true,
					'.ltg-slide .swiper-button-next::after{font-size:1.5em}' => true,
					'.ltg-slide .ltg-slide-not-in-html{color:red}' => false,
				),
			),
		);

		foreach ( $test_cases as $case ) {

			// 除外フィルタの有無を切り替える.
			if ( ! $case['conditions']['exclude_filter'] ) {
				remove_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );
			}

			try {
				// 前のケースの解析結果が残っていると条件が反映されないためリセットする.
				$this->reset_tree_shaking_cache();

				// ツリーシェイキング実行.
				$actual = CSS_tree_shaking::extended_minify( self::SLIDER_CSS, $case['conditions']['html'] );
			} finally {
				// 例外時もフィルタが外れたまま後続へ漏れないよう必ず元に戻す.
				if ( ! $case['conditions']['exclude_filter'] ) {
					add_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );
				}

				// 次のテストに解析結果を持ち越さないようリセットする.
				$this->reset_tree_shaking_cache();
			}

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
