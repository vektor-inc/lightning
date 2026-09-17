<?php
/**
 * CSS ツリーシェイキングの除外設定に関するテスト（ページネーション）
 *
 * ページネーションのドット（.swiper-pagination-bullet / -active）は Swiper の JS が
 * 実行時に生成する要素で、サーバー出力の HTML には空の .swiper-pagination しか無い。
 * そのため除外登録が無いとツリーシェイキングでセレクタごと削除される。
 * ドットは全て同じ白・同じ形にしてあり、現在位置の手がかりは -active の幅と角丸だけなので、
 * 削除されると現在どのスライドかが判別できなくなる。
 *
 * 矢印（.swiper-navigation-icon）の除外は test-css-tree-shaking-exclude.php の担当。
 * このファイルでは矢印固有の検証は行わない。
 *
 * @package vektor-inc/lightning
 */

use VektorInc\VK_CSS_Optimize\CSS_tree_shaking;

/**
 * Class CSS_Tree_Shaking_Exclude_Pagination_Test
 */
class CSS_Tree_Shaking_Exclude_Pagination_Test extends WP_UnitTestCase {

	/**
	 * class-css-tree-shaking.php の相対パス.
	 *
	 * PSR-4 のファイル命名規則に沿っていないため composer のオートロードでは
	 * 読み込まれない。ライブラリ側のファイル配置に依存するため、存在しない場合は
	 * fatal にせずスキップする.
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
	 * ツリーシェイキングの対象になるビルド済み CSS.
	 *
	 * lightning_css_tree_shaking_handles() が登録している handle
	 * （lightning-common-style / lightning-design-style）が読み込むファイル。
	 * editor 用の CSS は handle が対象外なのでシェイキングされず、ここには含めない.
	 *
	 * @var array
	 */
	const SHAKEN_CSS_FILES = array(
		'style.css',
		'style_layout-active.css',
		'style-theme-json.css',
	);

	/**
	 * このテストが対象にするクラス.
	 *
	 * @var array
	 */
	const TARGET_CLASSES = array(
		'swiper-pagination-bullet',
		'swiper-pagination-bullet-active',
	);

	/**
	 * テストの前処理.
	 *
	 * @return void
	 */
	public function set_up() {
		parent::set_up();

		// 他テストの解析結果を持ち込まないよう無条件にリセットする.
		$this->reset_tree_shaking_cache();

		// スライダーの HTML を既定値（画像 2 枚）で出せるようにする.
		// ドットと矢印は $slide_count >= 2 のときだけ出力されるため、既定値が前提.
		update_option( 'lightning_theme_options', lightning_g3_slider_default_options() );
		wp_cache_flush();
	}

	/**
	 * テストの後処理.
	 *
	 * extended_minify() が例外を投げた場合にループ内のリセットが実行されず
	 * static が汚染されたまま残るため、ここでも無条件にリセットする.
	 * 同じ理由で除外フィルタも無条件に復帰させる.
	 *
	 * @return void
	 */
	public function tear_down() {
		$this->reset_tree_shaking_cache();

		// 陰性の対照で外したまま後続テストへ漏れないよう、重複登録を避けつつ戻す.
		// has_filter() は優先度 0 で登録されていると 0 を返すため、真偽ではなく false と比較する.
		if ( false === has_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' ) ) {
			add_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );
		}

		delete_option( 'lightning_theme_options' );
		wp_cache_flush();

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

		if ( ! class_exists( CSS_tree_shaking::class, false ) ) {
			$this->markTestSkipped( '読み込みましたが CSS_tree_shaking が定義されていないためスキップします : ' . $file );
		}
	}

	/**
	 * CSS_tree_shaking が内部に持つ解析結果キャッシュを初期化する.
	 *
	 * extended_minify() は $cmplist['parse'] が立っていると HTML 解析と
	 * css_tree_shaking_exclude フィルタの適用をスキップする。static なので
	 * 1 プロセス内で条件を変えて比較するテストでは、リセットしないと
	 * 前のケースの解析結果が残り「除外を外しても残存した」という誤った緑になる.
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
	 * 除外クラス一覧を取得する.
	 *
	 * 配列をテスト側に複製すると本番と別々に育ち、テストが通るのに本番で落ちるため、
	 * 本番と同じ css_tree_shaking_exclude フィルタ経由で取得する.
	 *
	 * @return array 除外登録されたクラス名の配列.
	 */
	private function get_excluded_classes() {
		$inidata = array(
			'id'    => array(),
			'class' => array(),
			'tag'   => array( 'html' ),
		);

		$inidata = apply_filters( 'css_tree_shaking_exclude', $inidata );

		return $inidata['class'];
	}

	/**
	 * スライダーのサーバー出力 HTML を取得する.
	 *
	 * フィクスチャを手書きすると、マークアップ側でクラス名が変わったり削除されたりしても
	 * テストが気付けないため、実際の出力を使う.
	 *
	 * @return string ツリーシェイキングへ渡す HTML.
	 */
	private function get_slider_html() {
		$slide_html = LTG_G3_Slider::get_slide_html();

		// 出力が空だと「除外が効いている」ではなく「HTML が空だから何も残らない」に
		// なってしまい、テストの意味が失われるため、ここで担保する.
		$this->assertNotEmpty( $slide_html, 'スライダーのサーバー出力 HTML が取得できている' );

		return '<html><body>' . $slide_html . '</body></html>';
	}

	/**
	 * HTML に含まれる class 属性のクラス名を列挙する.
	 *
	 * @param string $html 対象の HTML.
	 * @return array クラス名の配列.
	 */
	private function get_classes_in_html( $html ) {
		$classes = array();

		if ( preg_match_all( '/class\s*=\s*["\']([^"\']*)["\']/', $html, $matches ) ) {
			foreach ( $matches[1] as $class_attr ) {
				foreach ( preg_split( '/\s+/', trim( $class_attr ) ) as $class_name ) {
					if ( '' !== $class_name ) {
						$classes[ $class_name ] = true;
					}
				}
			}
		}

		return array_keys( $classes );
	}

	/**
	 * ビルド済み CSS が参照している swiper 系のクラス名を列挙する.
	 *
	 * 対象を swiper 接頭辞に絞っているのは、Swiper の JS が実行時に生成する要素が
	 * この接頭辞を持つため。テーマ自身のクラス（ltg-slide 等）はサーバー出力なので
	 * ツリーシェイキングで消える心配が無く、対象にすると誤検知が増える.
	 *
	 * @return array クラス名 => 参照しているファイル名の配列.
	 */
	private function get_swiper_classes_in_built_css() {
		$classes = array();

		foreach ( self::SHAKEN_CSS_FILES as $file_name ) {
			$file = __DIR__ . self::BUILT_CSS_DIR . $file_name;

			// この 3 ファイルは git 管理下で常に存在する。無い場合はビルド成果物の
			// 欠落そのものが異常なので、黙ってスキップせず失敗させる.
			$this->assertFileExists( $file, 'ビルド済み CSS が存在する : ' . $file_name );

			$css = file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

			if ( preg_match_all( '/\.(swiper[\w-]*)/', $css, $matches ) ) {
				foreach ( $matches[1] as $class_name ) {
					$classes[ $class_name ][ $file_name ] = true;
				}
			}
		}

		$result = array();
		foreach ( $classes as $class_name => $files ) {
			$result[ $class_name ] = implode( ' / ', array_keys( $files ) );
		}
		ksort( $result );

		return $result;
	}

	/**
	 * ページネーションのドットのクラスが除外登録されているかのテスト.
	 *
	 * 除外リストの行を消した場合に検知するための安価なガード.
	 *
	 * @return void
	 */
	public function test_pagination_bullet_classes_are_excluded() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'css_tree_shaking_exclude ( pagination bullet )' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$excluded = $this->get_excluded_classes();

		// テストの配列.
		$test_cases = array(
			array(
				'test_condition_name' => 'ドット本体のクラスが除外登録されている => swiper-pagination-bullet を含む',
				'conditions'          => array( 'class_name' => 'swiper-pagination-bullet' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '現在位置のドットのクラスが除外登録されている => swiper-pagination-bullet-active を含む',
				'conditions'          => array( 'class_name' => 'swiper-pagination-bullet-active' ),
				'expected'            => true,
			),
			array(
				'test_condition_name' => '登録していないクラスは含まれない => swiper-pagination-not-registered を含まない',
				'conditions'          => array( 'class_name' => 'swiper-pagination-not-registered' ),
				'expected'            => false,
			),
		);

		foreach ( $test_cases as $case ) {
			$result = in_array( $case['conditions']['class_name'], $excluded, true );
			print $case['conditions']['class_name'] . ' : ' . var_export( $result, true ) . ' / correct = ' . var_export( $case['expected'], true ) . PHP_EOL;
			$this->assertSame( $case['expected'], $result, $case['test_condition_name'] );
		}

		// 照合はクラス名単位で行われ前方一致ではないため、無印を登録しても -active は
		// 救われない（逆も同じ）。上の 2 件はどちらも必要で、片方だけにはできない.
	}

	/**
	 * ビルド済み CSS が参照する swiper 系クラスが、HTML に出るか除外登録されているかのテスト.
	 *
	 * このテストが本命。個別のクラス名を列挙するのではなく、
	 * 「ビルド済み CSS が参照している swiper 系クラス」から
	 * 「スライダーのサーバー出力 HTML に実在するクラス」を引いた残りが、
	 * すべて除外登録されていることを突き合わせる。
	 *
	 * これにより双方向で検知できる。
	 *
	 * - 除外リストの行を消した => 参照されているのに HTML にも除外にも無い状態になり落ちる
	 * - 新しい実行時生成クラスに対する CSS を書いた => 除外登録を忘れた時点で落ちる
	 *
	 * 個別のクラス名をテスト側に列挙しないので、対象が増えてもテストの更新が要らない.
	 *
	 * @return void
	 */
	public function test_runtime_generated_classes_are_excluded() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'built css の swiper 系クラス x サーバー出力 HTML x 除外リスト' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$css_classes  = $this->get_swiper_classes_in_built_css();
		$html_classes = $this->get_classes_in_html( $this->get_slider_html() );
		$excluded     = $this->get_excluded_classes();

		// 参照が 0 件だと、この後の判定が無条件に通ってしまう（黙って緑になる）。
		// 正規表現がビルド出力の形と合わなくなった場合もここで気付ける.
		$this->assertNotEmpty( $css_classes, 'ビルド済み CSS が swiper 系のクラスを参照している' );

		$missing = array();

		foreach ( $css_classes as $class_name => $found_in ) {
			$in_html     = in_array( $class_name, $html_classes, true );
			$is_excluded = in_array( $class_name, $excluded, true );

			print '  ' . str_pad( $class_name, 34 )
				. ' html : ' . var_export( $in_html, true )
				. ' / exclude : ' . var_export( $is_excluded, true )
				. ' / ' . $found_in . PHP_EOL;

			// HTML に実在すればシェイキングで消えないので除外は不要。
			// HTML に無いなら、除外登録が無いとルールごと削除される.
			if ( ! $in_html && ! $is_excluded ) {
				$missing[] = $class_name;
			}
		}

		$this->assertSame(
			array(),
			$missing,
			'CSS が参照している swiper 系クラスは、サーバー出力の HTML に実在するか除外登録されている'
				. '（未登録 : ' . implode( ', ', $missing ) . '）'
		);
	}

	/**
	 * 実際にツリーシェイキングを通した際にドットのスタイルが残るかのテスト.
	 *
	 * 同梱の vk-css-optimize はツリーシェイキングを強制無効化しているが、
	 * CSS_tree_shaking を直接呼べばシェイキング自体の挙動は検証できる.
	 *
	 * @return void
	 */
	public function test_extended_minify_keeps_pagination_bullet() {

		// このテストだけが vendor のクラスを必要とするため、ここで読み込む.
		$this->require_tree_shaking_class();
		$this->reset_tree_shaking_cache();

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'CSS_tree_shaking::extended_minify() ( pagination bullet )' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// 入力はビルド済み CSS をそのまま使う。fixture を持つとビルド出力とズレるため.
		$css_file = __DIR__ . self::BUILT_CSS_DIR . 'style.css';
		$this->assertFileExists( $css_file, 'ビルド済み CSS が存在する : style.css' );
		$css = file_get_contents( $css_file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		$html = $this->get_slider_html();

		// 判定は宣言まで含めた完全一致ではなくセレクタで行う。
		// 宣言の値まで見るとビルドのたびにテストを直すことになり、
		// 「削除されていないか」という本来の関心から外れるため.
		$selectors = array(
			'.ltg-slide .swiper-pagination-bullet{',
			'.ltg-slide .swiper-pagination-bullet-active{',
		);

		// シェイキングにかける前の入力にセレクタが実在することを先に担保する。
		// SCSS 側からルールが消えた場合、これが無いと陰性の対照が無条件に通り、
		// 「除外が効いている」という誤った結論になる.
		foreach ( $selectors as $selector ) {
			$this->assertStringContainsString( $selector, $css, 'ビルド済み CSS にセレクタが実在する : ' . $selector );
		}

		// テストの配列.
		// expected は「そのセレクタがシェイキング後に残るか」をセレクタ単位で示す.
		$test_cases = array(
			array(
				'test_condition_name' => '除外フィルタが有効な場合 => JS が生成するドットのスタイルが残る',
				'conditions'          => array( 'exclude_filter' => true ),
				'expected'            => array(
					$selectors[0] => true,
					$selectors[1] => true,
				),
			),
			array(
				'test_condition_name' => '除外フィルタを解除した場合（陰性の対照） => ドットのスタイルが削除される',
				'conditions'          => array( 'exclude_filter' => false ),
				'expected'            => array(
					$selectors[0] => false,
					$selectors[1] => false,
				),
			),
		);

		foreach ( $test_cases as $case ) {

			if ( ! $case['conditions']['exclude_filter'] ) {
				remove_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );
			}

			try {
				// 前のケースの解析結果が残っていると条件が反映されないためリセットする.
				$this->reset_tree_shaking_cache();

				$actual = CSS_tree_shaking::extended_minify( $css, $html );
			} finally {
				// 例外時もフィルタが外れたまま後続へ漏れないよう必ず元に戻す.
				if ( ! $case['conditions']['exclude_filter'] ) {
					add_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );
				}

				// 次のケースに解析結果を持ち越さないようリセットする.
				$this->reset_tree_shaking_cache();
			}

			print PHP_EOL . $case['test_condition_name'] . PHP_EOL;

			foreach ( $case['expected'] as $selector => $should_remain ) {
				$result = ( false !== strpos( $actual, $selector ) );
				print '  ' . str_pad( $selector, 46 ) . ' : ' . var_export( $result, true ) . ' / correct = ' . var_export( $should_remain, true ) . PHP_EOL;
				$this->assertSame( $should_remain, $result, $case['test_condition_name'] . ' / ' . $selector );
			}

			// 影の指定はスライダーが出力するクラスだけを参照しているので、
			// 除外フィルタの有無に関わらず残る。ここが一緒に消えている場合は
			// HTML の生成やシェイキング自体が想定と違う動きをしているため、
			// 陰性の対照が「本当に除外の有無で分かれたのか」を確認する.
			$this->assertStringContainsString(
				'.swiper-button-prev',
				$actual,
				$case['test_condition_name'] . ' / サーバー出力クラスの指定は常に残る（対照が成立している）'
			);
		}
	}
}
