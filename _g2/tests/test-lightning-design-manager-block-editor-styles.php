<?php
/**
 * Class LightningDesignManagerBlockEditorStylesTest
 *
 * @package Lightning
 */

/**
 * Lightning_Design_Manager::add_skin_css_to_block_editor_settings()（block_editor_settings_all
 * フィルター経由でブロックエディターへ CSS を渡す処理）を検証するテスト。
 *
 * これまで bootstrap.min.css・スキンの editor.css を enqueue_block_assets フックで
 * wp_enqueue_style() していたが（旧 load_skin_gutenberg_css()）、この方法はブロックエディターの
 * プレビュー用 iframe だけでなく、管理画面のトップレベル文書（wp-admin 本体）にも <link> として
 * 出力されてしまい、origin2 スキンの editor.css が持つ裸の body / html セレクタへの
 * font-size・font-family 指定が管理画面全体に漏れ、サイドバー最下部の「メニューを閉じる」
 * ボタンの文字が折り返される不具合が起きていた。
 *
 * この不具合を根治するため、block_editor_settings_all フィルターで
 * $editor_settings['styles'] へ直接 CSS を追加する方式に切り替えた。
 *
 * resolve_content_url_to_path() / get_local_css_contents() / get_skin_editor_css_style_entry()
 * は private のため、このテストは公開されている add_skin_css_to_block_editor_settings()
 * （block_editor_settings_all フィルターの実体）を経由してのみ検証する。
 */
class LightningDesignManagerBlockEditorStylesTest extends WP_UnitTestCase {

	/**
	 * テスト用に作成した一時ファイル・ディレクトリのパス一覧。tear_down() で削除する。
	 *
	 * @var array
	 */
	private $temp_paths = array();

	/**
	 * lightning-design-skins フィルターに追加したコールバック一覧。tear_down() で必ず外す
	 * （テスト内のアサーションで失敗した場合でも filter が残らないようにするため）。
	 *
	 * @var array
	 */
	private $skins_filters = array();

	/**
	 * pre_http_request フィルターに追加したコールバック一覧。tear_down() で必ず外す。
	 *
	 * @var array
	 */
	private $http_filters = array();

	/**
	 * set_up() 時点の $pagenow（グローバル）。tear_down() で元に戻す。
	 *
	 * @var string|null
	 */
	private $original_pagenow;

	/**
	 * 各テストの前に、管理画面のブロックエディター画面にいる状態を模す
	 * （add_skin_css_to_block_editor_settings() は is_admin() を見るため）。
	 */
	public function set_up() {
		parent::set_up();
		set_current_screen( 'edit-post' );

		global $pagenow;
		$this->original_pagenow = $pagenow;
	}

	/**
	 * テスト後に一時ファイル・ディレクトリ・オプション・フィルター・$pagenow を片付ける。
	 */
	public function tear_down() {
		foreach ( $this->skins_filters as $filter ) {
			remove_filter( 'lightning-design-skins', $filter );
		}
		foreach ( $this->http_filters as $filter ) {
			remove_filter( 'pre_http_request', $filter );
		}
		foreach ( array_reverse( $this->temp_paths ) as $path ) {
			if ( is_file( $path ) ) {
				wp_delete_file( $path );
			} elseif ( is_dir( $path ) ) {
				rmdir( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
			}
		}
		delete_option( 'lightning_design_skin' );

		global $pagenow;
		$pagenow = $this->original_pagenow; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

		set_current_screen( 'front' );
		parent::tear_down();
	}

	/**
	 * pre_http_request をブロックし、HTTP 取得（wp_safe_remote_get() 経由）が
	 * 発生した場合は常に WP_Error を返すようにする。パストラバーサル等、ローカル解決が
	 * 失敗するはずのテストで、実際のネットワークアクセスが起きないようにするため。
	 */
	private function block_remote_requests() {
		$filter = static function () {
			return new WP_Error( 'blocked_in_test', 'blocked in test' );
		};
		add_filter( 'pre_http_request', $filter );
		$this->http_filters[] = $filter;
	}

	/**
	 * pre_http_request をモックし、指定した本文・HTTP レスポンスコードで常に応答する
	 * ようにする。実際のネットワークアクセスは発生しない。呼び出し回数を数えられる
	 * ように、カウンタを持つ stdClass を返す（transient キャッシュが効いて2回目以降は
	 * 呼ばれないことを検証するため）。
	 *
	 * @param string $body 応答するボディ.
	 * @param int    $code 応答する HTTP レスポンスコード.
	 * @return stdClass ->count に呼び出し回数が入るオブジェクト.
	 */
	private function mock_remote_response( $body, $code ) {
		$tracker        = new stdClass();
		$tracker->count = 0;

		$filter = function () use ( $body, $code, $tracker ) {
			++$tracker->count;
			return array(
				'headers'  => array(),
				'body'     => $body,
				'response' => array(
					'code'    => $code,
					'message' => '',
				),
				'cookies'  => array(),
				'filename' => null,
			);
		};
		add_filter( 'pre_http_request', $filter );
		$this->http_filters[] = $filter;

		return $tracker;
	}

	/**
	 * $editor_settings['styles'] から、__unstableType が 'theme' のエントリだけを
	 * 抜き出す（スキン CSS 以外の既定エントリと混ざらないようにするため）。
	 *
	 * @param array $editor_settings add_skin_css_to_block_editor_settings() の戻り値.
	 * @return array 'theme' エントリだけの配列（連番に振り直し済み）.
	 */
	private function get_theme_style_entries( $editor_settings ) {
		return array_values(
			array_filter(
				$editor_settings['styles'],
				static function ( $entry ) {
					return isset( $entry['__unstableType'] ) && 'theme' === $entry['__unstableType'];
				}
			)
		);
	}

	/**
	 * add_skin_css_to_block_editor_settings() が、is_admin() が偽（フロント表示）の
	 * 場合には何もせず $editor_settings をそのまま返すことを検証する（block_editor_settings_all
	 * はフロント側でブロックエディターを実装するプラグインからも発火し得るため、
	 * 管理画面に限定している）。
	 *
	 * 条件が1つ（is_admin() が偽）しかなく、表にする意味が薄いため単一シナリオのままにしている。
	 */
	public function test_add_skin_css_to_block_editor_settings_returns_unchanged_outside_admin() {
		set_current_screen( 'front' );
		update_option( 'lightning_design_skin', 'origin2' );

		$original        = array( 'styles' => array( array( 'css' => 'existing' ) ) );
		$editor_settings = Lightning_Design_Manager::add_skin_css_to_block_editor_settings( $original, null );

		$this->assertSame(
			$original,
			$editor_settings,
			'is_admin() が偽の場合は $editor_settings を変更せずそのまま返す必要があります。'
		);
	}

	/**
	 * add_skin_css_to_block_editor_settings() が、bs4 系スキン（Origin II）が有効な場合に
	 * bootstrap.min.css → スキンの editor.css の順で $editor_settings['styles'] へ
	 * 追加することを検証する。Origin II は editor_css_sv_path を持つため、この経路は
	 * サーバーパスを直接読む（URL 逆算・HTTP 取得を通らない）。
	 *
	 * 「2件が、この順序で入っている」という単一の並び順の検証であり、条件の組み合わせ表には
	 * ならないため単一シナリオのままにしている。
	 *
	 * この関数はコアから渡された $editor_settings['styles']（グローバルスタイル・
	 * テーマスタイル等、既存のエントリ）へ「追記」する位置にいる。将来 誤って
	 * `$editor_settings['styles'] = array(...)` のような代入に書き換えられると、
	 * 既存のエントリが丸ごと消えてしまう（エディターのスタイルが全滅する）。
	 * それを検出するため、入力に既存エントリを含め、加工後も残っていることを検証する。
	 */
	public function test_add_skin_css_to_block_editor_settings_adds_bootstrap_and_skin_css_in_order() {
		update_option( 'lightning_design_skin', 'origin2' );

		$original        = array( 'styles' => array( array( 'css' => 'existing' ) ) );
		$editor_settings = Lightning_Design_Manager::add_skin_css_to_block_editor_settings( $original, null );

		$this->assertArrayHasKey( 'styles', $editor_settings );

		// 既存の styles エントリ（コアのグローバルスタイル・テーマスタイル等を模したもの）が、
		// 追記後も落ちずに残っていることを確認する（追記ではなく代入に書き換えられる事故の検出）.
		$this->assertContains(
			array( 'css' => 'existing' ),
			$editor_settings['styles'],
			'既存の styles エントリが、スキン CSS の追記後も残っている必要があります。'
		);

		$theme_entries = $this->get_theme_style_entries( $editor_settings );

		// bs4 系スキンでは bootstrap.min.css とスキンの editor.css の2件が追加される想定.
		$this->assertCount(
			2,
			$theme_entries,
			'bs4 系スキンでは bootstrap.min.css とスキンの editor.css の2件が styles に追加される必要があります。'
		);

		// 1件目（bootstrap.min.css）は baseURL を持たない（自テーマのサーバーパスを直接読むため）。
		// Bootstrap 固有のクラス（.btn-primary。editor.css には含まれない）で判定する。
		$this->assertArrayNotHasKey(
			'baseURL',
			$theme_entries[0],
			'1件目（bootstrap.min.css）は baseURL を持たない想定です。'
		);
		$this->assertStringContainsString(
			'.btn-primary',
			$theme_entries[0]['css'],
			'1件目は bootstrap.min.css の内容（Bootstrap 固有の .btn-primary を含む）である必要があります。'
		);

		// 2件目（スキンの editor.css）は baseURL を持ち、origin2/css/editor.css を指す.
		$this->assertArrayHasKey(
			'baseURL',
			$theme_entries[1],
			'2件目（スキンの editor.css）は baseURL を持つ必要があります。'
		);
		$this->assertStringContainsString(
			'design-skin/origin2/css/editor.css',
			$theme_entries[1]['baseURL'],
			'2件目は origin2 スキンの editor.css を指す必要があります。'
		);
	}

	/**
	 * add_skin_css_to_block_editor_settings() の bs4 判定分岐を検証する。
	 *
	 * 「bs4 判定の内側に gutenberg_css_path の処理を入れてしまい、bs3 系スキンプラグインで
	 * ブロックエディターにスキン CSS が一切読み込まれなくなる」という退行の再発防止を兼ねる。
	 * lightning-skin-charm・lightning-skin-fort の bs3 版など、'bootstrap' キーを持たずに
	 * gutenberg_css_path だけを返すスキンプラグインが実在するため、'lightning-design-skins'
	 * フィルターでそれを模して検証する。
	 */
	public function test_add_skin_css_to_block_editor_settings_bs4_branching() {
		$dir = WP_CONTENT_DIR . '/lightning-test-' . wp_generate_password( 8, false );
		mkdir( $dir ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		$this->temp_paths[] = $dir;

		$file = $dir . '/editor-gutenberg.css';
		file_put_contents( $file, 'body{color:green}' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
		$this->temp_paths[] = $file;

		$url = content_url( '/' . basename( $dir ) . '/editor-gutenberg.css' );

		$filter = static function ( $skins ) use ( $url ) {
			// 'bootstrap' キーも '*_sv_path' も意図的に持たせない
			// （新キーに未対応の bs3 系スキンプラグインを模している）.
			$skins['bs3-plugin-skin']          = array(
				'label'              => 'BS3 Plugin Skin (test)',
				'gutenberg_css_path' => $url,
			);
			// 'bootstrap' キーに 'bs4' 以外の値を明示的に持たせたケース
			// （キー自体が無いケースとは別に、「bs4 ではない値が入っている」場合の
			// 境界も確認するため）.
			$skins['bs3-explicit-plugin-skin'] = array(
				'label'              => 'BS3 Explicit Plugin Skin (test)',
				'bootstrap'          => 'bs3',
				'gutenberg_css_path' => $url,
			);
			return $skins;
		};
		add_filter( 'lightning-design-skins', $filter );
		$this->skins_filters[] = $filter;

		$test_cases = array(
			array(
				'test_condition_name'   => 'bootstrap キーも gutenberg_css_path も持たない bs3 系スキン（Origin）の場合 => 何も追加されない',
				'skin_key'              => 'origin',
				'expected_count'        => 0,
				'expected_css_contains' => null,
			),
			array(
				'test_condition_name'   => 'bootstrap キーを持たないが gutenberg_css_path だけを持つ bs3 系スキンプラグインの場合 => 1件追加される',
				'skin_key'              => 'bs3-plugin-skin',
				'expected_count'        => 1,
				'expected_css_contains' => 'color:green',
			),
			array(
				'test_condition_name'   => 'bootstrap キーに bs4 以外の値（bs3）を明示的に持つスキンプラグインの場合 => gutenberg_css_path だけが1件追加される',
				'skin_key'              => 'bs3-explicit-plugin-skin',
				'expected_count'        => 1,
				'expected_css_contains' => 'color:green',
			),
		);

		foreach ( $test_cases as $case ) {
			update_option( 'lightning_design_skin', $case['skin_key'] );

			$editor_settings = Lightning_Design_Manager::add_skin_css_to_block_editor_settings( array(), null );
			$theme_entries   = $this->get_theme_style_entries( $editor_settings );

			$this->assertCount( $case['expected_count'], $theme_entries, $case['test_condition_name'] );

			if ( null !== $case['expected_css_contains'] ) {
				$this->assertStringContainsString( $case['expected_css_contains'], $theme_entries[0]['css'], $case['test_condition_name'] );
			}

			delete_option( 'lightning_design_skin' );
		}
	}

	/**
	 * gutenberg_css_path の URL 解決（resolve_content_url_to_path()）を、
	 * add_skin_css_to_block_editor_settings() 経由で検証する。
	 *
	 * - パーセントエンコードされた URL（空白・非 ASCII 文字を含むパス）が正しく
	 *   ローカル解決されること（rawurldecode() 追加分の回帰テスト）
	 * - '../' によるパストラバーサル（非エンコード・パーセントエンコードの両方）が、
	 *   デコード後の realpath() 正規化 + WP_CONTENT_DIR 配下チェックで拒否されること
	 * - content_url() を文字列として「含むだけ」の別ホスト URL（前方一致だけでは
	 *   弾けないケース）が、後続の配下チェックで拒否されること
	 * - %00（NUL バイト）を含む URL が、致命的エラー（PHP 8 の realpath() が NUL バイトを
	 *   含むパスに対して投げる ValueError）にならず、null として扱われること
	 *
	 * ローカル解決に失敗するケースはすべて HTTP 取得（wp_safe_remote_get()）へ進むため、
	 * ループの外側で一括して pre_http_request をブロックし、実際のネットワークアクセスが
	 * 起きないようにする。
	 */
	public function test_add_skin_css_to_block_editor_settings_gutenberg_css_path_url_resolution() {
		$this->block_remote_requests();

		// ケース1: 半角スペースを含むディレクトリ名（パーセントエンコードされた URL）.
		$dir_with_space = WP_CONTENT_DIR . '/lightning test ' . wp_generate_password( 6, false );
		mkdir( $dir_with_space ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		$this->temp_paths[] = $dir_with_space;
		$file_with_space     = $dir_with_space . '/editor-gutenberg.css';
		file_put_contents( $file_with_space, '.ltg-space-marker{color:blue}' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
		$this->temp_paths[] = $file_with_space;
		$url_with_space      = content_url( '/' . rawurlencode( basename( $dir_with_space ) ) . '/editor-gutenberg.css' );

		// ケース2: 日本語のファイル名（パーセントエンコードされた URL）.
		$dir_ja = WP_CONTENT_DIR . '/lightning-test-' . wp_generate_password( 8, false );
		mkdir( $dir_ja ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		$this->temp_paths[] = $dir_ja;
		$file_ja = $dir_ja . '/エディタ.css';
		file_put_contents( $file_ja, '.ltg-ja-marker{color:orange}' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
		$this->temp_paths[] = $file_ja;
		$url_ja  = content_url( '/' . basename( $dir_ja ) . '/' . rawurlencode( 'エディタ.css' ) );

		// ケース3: '../' によるパストラバーサル（非エンコード）。WP_CONTENT_DIR から見て
		// ABSPATH . 'wp-load.php'（標準構成で確実に存在する）を狙う。
		// content_url( '/../wp-config.php' ) を使わない理由: wp-config.php を
		// WP_CONTENT_DIR のひとつ上の階層に置く構成（公式サポート）では、そもそも
		// realpath() の時点で false になり、配下チェックへ到達せずに null になる。
		// それでは「封じ込めチェックが効いた」のか「ファイルが無かっただけ」かを
		// 区別できないため、確実に存在するファイルを狙わせる.
		$traversal_url = content_url( '/../wp-load.php' );

		// ケース4: パーセントエンコードされたパストラバーサル（%2e%2e%2f = '../'）。
		// デコードを追加したことで、%2e%2e%2f のようなエンコード済みトラバーサルが
		// 実在するパスへ復元されるようになった（デコード前は 1 セグメントのファイル名として
		// 扱われ realpath() が false を返していた）。デコードを配下チェックより後ろに
		// 移すと素通りするため、この順序を固定する.
		$encoded_traversal_url = content_url() . '/%2e%2e%2fwp-load.php';

		// ケース6: %00（NUL バイト）を含む URL。デコードにより NUL バイトへ変わり、
		// PHP 8 の realpath() はこれを渡すと ValueError を投げる。致命的エラーにならず
		// null を返す（=styles に何も追加されない）ことを確認する.
		$null_byte_url = content_url( '/editor%00.css' );

		// ケース5: content_url() を文字列として「含むだけ」の別ホスト URL
		// （例: https://example.com/wp-content-evil/x.css）。strpos() による前方一致
		// チェックだけでは弾けないが、その後の候補パスが実在しないディレクトリを指すため
		// realpath() が false を返して弾かれる想定.
		$boundary_url = untrailingslashit( content_url() ) . '-evil/x.css';

		$filter = static function ( $skins ) use ( $url_with_space, $url_ja, $traversal_url, $encoded_traversal_url, $boundary_url, $null_byte_url ) {
			$skins['url-res-space']             = array(
				'label'              => 'URL Resolution Space (test)',
				'gutenberg_css_path' => $url_with_space,
			);
			$skins['url-res-ja']                = array(
				'label'              => 'URL Resolution JA (test)',
				'gutenberg_css_path' => $url_ja,
			);
			$skins['url-res-traversal']         = array(
				'label'              => 'URL Resolution Traversal (test)',
				'gutenberg_css_path' => $traversal_url,
			);
			$skins['url-res-encoded-traversal'] = array(
				'label'              => 'URL Resolution Encoded Traversal (test)',
				'gutenberg_css_path' => $encoded_traversal_url,
			);
			$skins['url-res-boundary']          = array(
				'label'              => 'URL Resolution Boundary (test)',
				'gutenberg_css_path' => $boundary_url,
			);
			$skins['url-res-null-byte']         = array(
				'label'              => 'URL Resolution Null Byte (test)',
				'gutenberg_css_path' => $null_byte_url,
			);
			return $skins;
		};
		add_filter( 'lightning-design-skins', $filter );
		$this->skins_filters[] = $filter;

		$test_cases = array(
			array(
				'test_condition_name'   => '半角スペースを含むディレクトリ名がパーセントエンコードされた URL の場合 => ローカル解決されて1件追加される',
				'skin_key'              => 'url-res-space',
				'expected_count'        => 1,
				'expected_css_contains' => '.ltg-space-marker',
			),
			array(
				'test_condition_name'   => '日本語のファイル名がパーセントエンコードされた URL の場合 => ローカル解決されて1件追加される',
				'skin_key'              => 'url-res-ja',
				'expected_count'        => 1,
				'expected_css_contains' => '.ltg-ja-marker',
			),
			array(
				'test_condition_name'   => '非エンコードの \'../\' によるパストラバーサルの場合 => 封じ込めチェックで拒否され0件',
				'skin_key'              => 'url-res-traversal',
				'expected_count'        => 0,
				'expected_css_contains' => null,
			),
			array(
				'test_condition_name'   => 'パーセントエンコードされたパストラバーサル（%2e%2e%2f）の場合 => デコード後の封じ込めチェックで拒否され0件',
				'skin_key'              => 'url-res-encoded-traversal',
				'expected_count'        => 0,
				'expected_css_contains' => null,
			),
			array(
				'test_condition_name'   => 'content_url() を文字列として含むだけの別ホスト URL の場合 => 封じ込めチェックで拒否され0件',
				'skin_key'              => 'url-res-boundary',
				'expected_count'        => 0,
				'expected_css_contains' => null,
			),
			array(
				'test_condition_name'   => '%00（NUL バイト）を含む URL の場合 => 致命的エラーにならず null が返り0件',
				'skin_key'              => 'url-res-null-byte',
				'expected_count'        => 0,
				'expected_css_contains' => null,
			),
		);

		foreach ( $test_cases as $case ) {
			update_option( 'lightning_design_skin', $case['skin_key'] );

			$editor_settings = Lightning_Design_Manager::add_skin_css_to_block_editor_settings( array(), null );
			$theme_entries   = $this->get_theme_style_entries( $editor_settings );

			$this->assertCount( $case['expected_count'], $theme_entries, $case['test_condition_name'] );

			if ( null !== $case['expected_css_contains'] ) {
				$this->assertStringContainsString( $case['expected_css_contains'], $theme_entries[0]['css'], $case['test_condition_name'] );
			}

			delete_option( 'lightning_design_skin' );
		}
	}

	/**
	 * 管理画面本体に <link> が出力される不具合そのものの回帰テスト。
	 *
	 * 将来誰かが「エディターにスタイルが足りない」という理由で、
	 * enqueue_block_assets フックへ wp_enqueue_style( 'lightning-gutenberg-editor', ... )
	 * や wp_enqueue_style( 'lightning-bootstrap-editor', ... ) を復活させた場合に、
	 * このテストが失敗して検出できるようにする。
	 *
	 * 「特定のハンドルが enqueue されていないこと」を確認するだけの単一の回帰チェックで
	 * あり、条件の組み合わせ表にはならないため単一シナリオのままにしている。
	 */
	public function test_enqueue_block_assets_does_not_enqueue_legacy_editor_styles() {
		update_option( 'lightning_design_skin', 'origin2' );

		do_action( 'enqueue_block_assets' );

		$this->assertFalse(
			wp_style_is( 'lightning-gutenberg-editor', 'enqueued' ),
			'enqueue_block_assets で lightning-gutenberg-editor が enqueue されてはいけません（管理画面本体への漏れの再発）。'
		);
		$this->assertFalse(
			wp_style_is( 'lightning-bootstrap-editor', 'enqueued' ),
			'enqueue_block_assets で lightning-bootstrap-editor が enqueue されてはいけません（管理画面本体への漏れの再発）。'
		);
	}

	/**
	 * add_root_font_size_for_block_editor() が、$pagenow が widgets.php のときだけ
	 * html の font-size を出力し、post.php・site-editor.php では出力しないことを検証する。
	 *
	 * この対処はウィジェット画面（非 iframe）限定で rem 基準のずれを補うためのもので、
	 * 投稿編集画面・サイトエディター（iframe 化されており対処不要）にまで広げてしまうと、
	 * 管理画面本体へ不要に CSS を出力することになる。
	 *
	 * wp_add_inline_style() は一度追加した内容を取り消さない（累積する）ため、
	 * 各ケースの実行順序に意味がある。widgets.php のケースを先に実行してしまうと、
	 * 後続の post.php・site-editor.php のケースで「まだ出力されていないこと」を
	 * 検証できなくなる。そのため配列の並び順（post.php → site-editor.php →
	 * widgets.php）を変更してはいけない。
	 */
	public function test_add_root_font_size_for_block_editor_only_applies_on_widgets_screen() {
		update_option( 'lightning_design_skin', 'origin2' );

		$expected_css = 'html{font-size:87.5%}@media (992px < width){html{font-size:100%}}';

		global $pagenow;

		$test_cases = array(
			array(
				'test_condition_name' => 'pagenow が post.php（投稿編集画面、iframe 化されている）の場合 => 出力されない',
				'pagenow'             => 'post.php',
				'expected_contains'   => false,
			),
			array(
				'test_condition_name' => 'pagenow が site-editor.php（iframe 化されている）の場合 => 出力されない',
				'pagenow'             => 'site-editor.php',
				'expected_contains'   => false,
			),
			array(
				'test_condition_name' => 'pagenow が widgets.php（非 iframe）の場合 => 出力される',
				'pagenow'             => 'widgets.php',
				'expected_contains'   => true,
			),
		);

		foreach ( $test_cases as $case ) {
			$pagenow = $case['pagenow']; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			do_action( 'enqueue_block_assets' );

			$inline_styles = (array) wp_styles()->get_data( 'wp-edit-blocks', 'after' );

			if ( $case['expected_contains'] ) {
				$this->assertContains( $expected_css, $inline_styles, $case['test_condition_name'] );
			} else {
				$this->assertNotContains( $expected_css, $inline_styles, $case['test_condition_name'] );
			}
		}
	}

	/**
	 * *_sv_path（get_skins() のサーバーパス）が、URL からの逆算・HTTP 取得より
	 * 優先されることを固定するテスト。
	 *
	 * gutenberg_css_path には URL 逆算（resolve_content_url_to_path()）でも
	 * HTTP 取得（get_remote_css_contents()。pre_http_request をブロックしているため
	 * 必ず失敗する）でも解決できない URL を与え、gutenberg_css_sv_path にだけ
	 * 一意のマーカーを含むファイルを指定する。styles にマーカー入りの内容が
	 * 追加されれば、*_sv_path 経由で解決されたことが確定する（URL 逆算や HTTP に
	 * フォールバックしていれば、内容が一致しないか、そもそも styles に何も
	 * 追加されない）。
	 *
	 * WP のテスト環境では content_url() と WP_CONTENT_DIR が対応しているため、
	 * gutenberg_css_path 自体を wp-content 配下の URL にしてしまうと、*_sv_path の
	 * 参照が壊れていても URL 逆算が同じ結果を返してこのテストの意図が守れない。
	 * そのため gutenberg_css_path は wp-content の外（example.com）を指す URL にする。
	 *
	 * 「到達不可能な URL」と「一意なマーカーを持つ sv_path」を組み合わせた単一シナリオで
	 * 優先順位を確定させるものであり、条件の組み合わせ表にはならないため単一シナリオの
	 * ままにしている。
	 */
	public function test_sv_path_takes_priority_over_url_resolution_and_http() {
		$this->block_remote_requests();

		$dir = WP_CONTENT_DIR . '/lightning-test-' . wp_generate_password( 8, false );
		mkdir( $dir ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_mkdir
		$this->temp_paths[] = $dir;

		$file = $dir . '/editor-gutenberg.css';
		file_put_contents( $file, '.ltg-sv-path-marker{color:red}' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fwrite
		$this->temp_paths[] = $file;

		$filter = static function ( $skins ) use ( $file ) {
			$skins['sv-skin'] = array(
				'label'                 => 'SV Skin (test)',
				// wp-content の外（example.com）を指すため、URL 逆算では解決できず、
				// HTTP はブロックされているため必ず失敗する.
				'gutenberg_css_path'    => 'https://example.com/nowhere/unreachable.css',
				'gutenberg_css_sv_path' => $file,
			);
			return $skins;
		};
		add_filter( 'lightning-design-skins', $filter );
		$this->skins_filters[] = $filter;

		update_option( 'lightning_design_skin', 'sv-skin' );

		$editor_settings = Lightning_Design_Manager::add_skin_css_to_block_editor_settings( array(), null );
		$theme_entries   = $this->get_theme_style_entries( $editor_settings );

		$this->assertCount(
			1,
			$theme_entries,
			'gutenberg_css_sv_path 経由で解決できるはずのスキンで、styles に何も追加されていません（sv_path が使われていない可能性があります）。'
		);
		$this->assertStringContainsString(
			'.ltg-sv-path-marker',
			$theme_entries[0]['css'],
			'*_sv_path 経由で解決された内容（マーカー入り）である必要があります。URL 逆算や HTTP にフォールバックしていないことを確認してください。'
		);
	}

	/**
	 * get_remote_css_contents() の HTTP レスポンス処理（200 応答時の取得成功、
	 * 200 以外は本文の内容にかかわらず失敗として扱うこと）を検証する。
	 *
	 * transient キャッシュにより2回目の呼び出しでは実際の HTTP リクエストが
	 * 発生しないことの検証は、1回目・2回目の呼び出し順序そのものに意味があるため、
	 * このテストには含めず test_http_fallback_succeeds_and_caches_via_transient() に
	 * 単一シナリオとして残している。
	 */
	public function test_get_skin_editor_css_style_entry_http_response_handling() {
		$test_cases = array(
			array(
				'test_condition_name'   => '200 応答（CSS 本文その1）の場合 => styles に1件追加される',
				'body'                  => '.ltg-http-marker-1{color:red}',
				'code'                  => 200,
				'expected_count'        => 1,
				'expected_css_contains' => '.ltg-http-marker-1{color:red}',
			),
			array(
				'test_condition_name'   => '200 応答（CSS 本文その2、別内容）の場合 => styles に1件追加される',
				'body'                  => '.ltg-http-marker-2{color:blue}',
				'code'                  => 200,
				'expected_count'        => 1,
				'expected_css_contains' => '.ltg-http-marker-2{color:blue}',
			),
			array(
				'test_condition_name'   => '404 応答（エラーページの HTML 本文）の場合 => 失敗として扱われ styles に何も追加されない',
				'body'                  => '<html>404 Not Found</html>',
				'code'                  => 404,
				'expected_count'        => 0,
				'expected_css_contains' => null,
			),
		);

		foreach ( $test_cases as $index => $case ) {
			$skin_key = 'http-response-skin-' . $index;

			$tracker        = new stdClass();
			$tracker->count = 0;
			$http_filter    = function () use ( $case, $tracker ) {
				++$tracker->count;
				return array(
					'headers'  => array(),
					'body'     => $case['body'],
					'response' => array(
						'code'    => $case['code'],
						'message' => '',
					),
					'cookies'  => array(),
					'filename' => null,
				);
			};
			add_filter( 'pre_http_request', $http_filter );
			$this->http_filters[] = $http_filter;

			$skins_filter = static function ( $skins ) use ( $skin_key ) {
				$skins[ $skin_key ] = array(
					'label'              => 'HTTP Response Skin (test)',
					'gutenberg_css_path' => 'https://example.com/nowhere/http-response-test-' . $skin_key . '.css',
				);
				return $skins;
			};
			add_filter( 'lightning-design-skins', $skins_filter );
			$this->skins_filters[] = $skins_filter;

			update_option( 'lightning_design_skin', $skin_key );

			$editor_settings = Lightning_Design_Manager::add_skin_css_to_block_editor_settings( array(), null );
			$theme_entries   = $this->get_theme_style_entries( $editor_settings );

			$this->assertCount( $case['expected_count'], $theme_entries, $case['test_condition_name'] );

			if ( null !== $case['expected_css_contains'] ) {
				$this->assertStringContainsString( $case['expected_css_contains'], $theme_entries[0]['css'], $case['test_condition_name'] );
			}

			$this->assertSame(
				1,
				$tracker->count,
				$case['test_condition_name'] . '（HTTP リクエストは1回だけ発生する必要があります）'
			);

			// 次のケースへ影響しないよう、このケース専用のフィルターとオプションを都度取り除く
			// （複数の pre_http_request フィルターが積み重なると、前のケースの $tracker まで
			// 再カウントされてしまうため）.
			remove_filter( 'pre_http_request', $http_filter );
			remove_filter( 'lightning-design-skins', $skins_filter );
			delete_option( 'lightning_design_skin' );
		}
	}

	/**
	 * get_skin_editor_css_style_entry() の HTTP フォールバック（get_remote_css_contents()）が、
	 * 200 応答時に CSS を取得して styles へ追加すること、および transient キャッシュにより
	 * 2回目の呼び出しでは実際の HTTP リクエストが発生しないことを検証する。
	 *
	 * 1回目・2回目の呼び出しという順序そのものが検証内容であり、条件の組み合わせ表には
	 * ならないため単一シナリオのままにしている。
	 */
	public function test_http_fallback_succeeds_and_caches_via_transient() {
		$marker  = '.ltg-http-marker-' . wp_generate_password( 8, false ) . '{color:red}';
		$tracker = $this->mock_remote_response( $marker, 200 );

		$filter = static function ( $skins ) {
			$skins['http-ok-skin'] = array(
				'label'              => 'HTTP OK Skin (test)',
				'gutenberg_css_path' => 'https://example.com/nowhere/http-ok-test.css',
			);
			return $skins;
		};
		add_filter( 'lightning-design-skins', $filter );
		$this->skins_filters[] = $filter;

		update_option( 'lightning_design_skin', 'http-ok-skin' );

		$editor_settings = Lightning_Design_Manager::add_skin_css_to_block_editor_settings( array(), null );
		$theme_entries   = $this->get_theme_style_entries( $editor_settings );

		$this->assertCount( 1, $theme_entries, '200 応答時は styles に1件追加される必要があります。' );
		$this->assertStringContainsString(
			$marker,
			$theme_entries[0]['css'],
			'追加された内容は HTTP レスポンスのボディである必要があります。'
		);
		$this->assertSame( 1, $tracker->count, 'HTTP リクエストは1回だけ発生する必要があります。' );

		// 2回目の呼び出し: transient が効いていれば、実際の HTTP リクエストは発生しない.
		Lightning_Design_Manager::add_skin_css_to_block_editor_settings( array(), null );
		$this->assertSame(
			1,
			$tracker->count,
			'2回目の呼び出しでは transient キャッシュが効いて HTTP が発生してはいけません。'
		);
	}
}
