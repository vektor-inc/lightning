<?php // phpcs:ignore
/**
 * Lightning_Design_Manager
 */
class Lightning_Design_Manager {

	/**
	 * Init functions
	 *
	 * @return void
	 */
	public static function init() {

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_skin_css_and_js' ) );
		// Set to priority 9 that to be load before theme style.css.
		add_action( 'after_setup_theme', array( __CLASS__, 'load_late_css_action' ), 9 );

		// Don't use following action point.
		// wp : Bring to phpunit test error.
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_php' ) );

		add_action( 'wp', array( __CLASS__, 'load_skin_callback' ) );

		// Don't use following action point.
		// wp : become do not load css.
		// クラシックエディター（TinyMCE）向け。ブロックエディターにはタイミング上間に合わないため、
		// ブロックエディター側は block_editor_settings_all（add_skin_css_to_block_editor_settings）で
		// 別途対応している（詳細は同メソッドのコメントを参照）。
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_skin_editor_css' ), 11 );

		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );

		add_filter( 'block_editor_settings_all', array( __CLASS__, 'add_skin_css_to_block_editor_settings' ), 10, 2 );

		add_action( 'enqueue_block_assets', array( __CLASS__, 'add_root_font_size_for_block_editor' ) );

	}

	/**
	 * Set default skin
	 *
	 * @return array スキン固有で読み込むファイル情報の配列
	 */
	public static function get_skins() {
		$skins = array(
			'origin'  => array(
				'label'              => __( 'Origin ( Not recommended )', 'lightning' ),
				'css_path'           => get_template_directory_uri() . '/design-skin/origin/css/style.css',
				'css_sv_path'        => get_parent_theme_file_path( '/design-skin/origin/css/style.css' ),
				'editor_css_path'    => get_template_directory_uri() . '/design-skin/origin/css/editor.css',
				// add_skin_css_to_block_editor_settings() がサーバーパスを直接読めるように、
				// editor_css_path に対応するサーバーパスを持たせる（URL 逆算・HTTP 取得を通らない）.
				'editor_css_sv_path' => get_parent_theme_file_path( '/design-skin/origin/css/editor.css' ),
				// 'gutenberg_css_path' => get_template_directory_uri() . '/design-skin/origin/css/editor-gutenberg.css',
				'php_path'           => get_parent_theme_file_path( '/design-skin/origin/origin.php' ),
				'js_path'            => '',
				'callback'           => '',
				'version'            => LIGHTNING_THEME_VERSION,
			),

			'origin2' => array(
				'label'              => __( 'Origin II ( Bootstrap4 )', 'lightning' ),
				'css_path'           => get_template_directory_uri() . '/design-skin/origin2/css/style.css',
				'css_sv_path'        => get_parent_theme_file_path( '/design-skin/origin2/css/style.css' ),
				'css_late_path'      => '',
				'editor_css_path'    => get_template_directory_uri() . '/design-skin/origin2/css/editor.css',
				// add_skin_css_to_block_editor_settings() がサーバーパスを直接読めるように、
				// editor_css_path に対応するサーバーパスを持たせる（URL 逆算・HTTP 取得を通らない）.
				'editor_css_sv_path' => get_parent_theme_file_path( '/design-skin/origin2/css/editor.css' ),
				'php_path'           => get_parent_theme_file_path( '/design-skin/origin2/origin2.php' ),
				'js_path'            => '',
				'version'            => LIGHTNING_THEME_VERSION,
				'bootstrap'          => 'bs4',
			),
		);
		return apply_filters( 'lightning-design-skins', $skins ); // phpcs:ignore
	}


	/**
	 * Only using deal with plugin skin deactive fallback.
	 *
	 * @return [array] Return plugin url to activate check.
	 */
	public static function get_skins_info() {
		$skins = array(
			'variety'               => array(
				'plugin_path' => 'lightning-skin-variety/lightning_skin_variety.php',
			),
			'variety-bs4'           => array(
				'plugin_path' => 'lightning-skin-variety/lightning_skin_variety.php',
			),
			'charm'                 => array(
				'plugin_path' => 'lightning-skin-charm/lightning_skin_charm.php',
			),
			'charm-bs4'             => array(
				'plugin_path' => 'lightning-skin-charm/lightning_skin_charm.php',
			),
			'jpnstyle'              => array(
				'plugin_path' => 'lightning-skin-jpnstyle/lightning_skin_jpnstyle.php',
			),
			'jpnstyle-bs4'          => array(
				'plugin_path' => 'lightning-skin-jpnstyle/lightning_skin_jpnstyle.php',
			),
			'fort'                  => array(
				'plugin_path' => 'lightning-skin-fort/lightning-skin-fort.php',
			),
			'fort2'                 => array(
				'plugin_path' => 'lightning-skin-fort/lightning-skin-fort.php',
			),
			'fort-bs4'              => array(
				'plugin_path' => 'lightning-skin-fort/lightning-skin-fort.php',
			),
			'fort-bs4-footer-light' => array(
				'plugin_path' => 'lightning-skin-fort/lightning-skin-fort.php',
			),
			'pale'                  => array(
				'plugin_path' => 'lightning-skin-pale/lightning-skin-pale.php',
			),
			'pale-bs4'              => array(
				'plugin_path' => 'lightning-skin-pale/lightning-skin-pale.php',
			),
		);
		return $skins;
	}


	/**
	 * Get current skin function
	 *
	 * @return [string] If empty current skin that set default skin.
	 */
	public static function get_current_skin() {
		$skins        = self::get_skins();
		$current_skin = get_option( 'lightning_design_skin' );

		if ( ! $current_skin ) {
			$current_skin = 'origin2';
		}

		// If selected skin plugin is deactive that, set to default skin.
		if ( 'origin2' !== $current_skin ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			$skins_info = self::get_skins_info();
			if ( isset( $skins_info[ $current_skin ]['plugin_path'] ) && ! is_plugin_active( $skins_info[ $current_skin ]['plugin_path'] ) ) {
				$current_skin = 'origin2';
			}
		}

		if ( ! isset( $skins[ $current_skin ]['version'] ) ) {
			$skins[ $current_skin ]['version'] = '';
		}
		return $skins[ $current_skin ];
	}

	/**
	 * Load skin CSS and JavaScript
	 *
	 * @return void
	 */
	public static function load_skin_css_and_js() {
		$skin_info = self::get_current_skin();

		$skin_css_url = '';
		if ( ! empty( $skin_info['css_path'] ) ) {
			$skin_css_url = $skin_info['css_path'];
		}

		// load bootstrap ///////////////////////.

		global $bootstrap;
		$bootstrap = '3';
		if ( empty( $skin_info['bootstrap'] ) ) {
			// Bootstrap3 skin
			// load bootstrap3 js .
			wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/library/bootstrap-3/js/bootstrap.min.js', array( 'jquery' ), '3.4.1', true );

			wp_enqueue_style( 'lightning-design-style', $skin_css_url, array(), $skin_info['version'] );

		} elseif ( 'bs4' === $skin_info['bootstrap'] ) {
			$options   = get_option( 'lightning_theme_options' );
			$bootstrap = '4';
			// Bootstrap4 skin.
			$bs4_version = '4.5.0';
			$bs4_css_url = '';
			$bs4_css_url = get_template_directory_uri() . '/library/bootstrap-4/css/bootstrap.min.css';
			wp_enqueue_style( 'bootstrap-4-style', $bs4_css_url, array(), $bs4_version );
			wp_enqueue_script( 'bootstrap-4-js', get_template_directory_uri() . '/library/bootstrap-4/js/bootstrap.min.js', array( 'jquery' ), $bs4_version, true );

			// load skin CSS ///////////////////////.

			wp_enqueue_style( 'lightning-design-style', $skin_css_url, array( 'bootstrap-4-style', 'lightning-common-style' ), $skin_info['version'] );

		}

		// load JS ///////////////////////.

		if ( ! empty( $skin_info['js_path'] ) ) {
			wp_enqueue_script( 'lightning-design-js', $skin_info['js_path'], array( 'jquery' ), $skin_info['version'], true );
		}

	}

	/**
	 * Load late css ( To be overwrite )
	 *
	 * @return void
	 */
	public static function load_late_css_action() {
		$hook_point = apply_filters( 'lightning_late_load_style_enqueue_point', 'wp_enqueue_scripts' );
		add_action( $hook_point, array( __CLASS__, 'load_late_load_css' ) );
	}

	/**
	 * Load late css
	 *
	 * @return void
	 */
	public static function load_late_load_css() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['css_late_path'] ) ) {
			$skin_css_footer_url = $skin_info['css_late_path'];
			// $deps is needed to overwrite with skins.
			wp_enqueue_style( 'lightning-late-load-style', $skin_css_footer_url, array( 'lightning-design-style', 'lightning-common-style', 'vk-font-awesome', 'vk-blocks-build-css' ), $skin_info['version'] );
		}
	}

	/**
	 * Load skin Editor CSS
	 *
	 * クラシックエディター（TinyMCE）が本文の見た目をフロントに合わせるために読み込む。
	 * ブロックエディターの投稿・固定ページ編集画面／ウィジェット画面／サイトエディターは、
	 * この関数が動く admin_enqueue_scripts（優先度 11）よりも前に編集画面の設定
	 * （$editor_settings['styles']）を組み立て終えているため、add_editor_style() を
	 * ここで呼んでもブロックエディターには一切反映されない。
	 * ブロックエディター側は add_skin_css_to_block_editor_settings()
	 * （block_editor_settings_all フィルター）で別途対応している。
	 *
	 * @return void
	 */
	public static function load_skin_editor_css() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['editor_css_path'] ) ) {
			add_editor_style( $skin_info['editor_css_path'] );
		}
	}

	/**
	 * wp-content 配下の URL をサーバー上のファイルパスへ解決する
	 *
	 * スキンプラグインが返す editor CSS のパスは URL のため、*_sv_path（サーバーパス）を
	 * 持たない旧スキンプラグインでも、ブロックエディター設定へ直接 CSS を埋め込む
	 * （HTTP 取得を避ける）ために、まずサーバー上のパスへの逆算を試みる。
	 * content_url() を WP_CONTENT_DIR に置き換えて候補パスを作り、realpath() で
	 * 正規化したうえで WP_CONTENT_DIR 配下に収まっているか（.. によるパストラバーサルを
	 * 含んでいないか）を確認してから返す。逆算できない・ファイルが存在しない・
	 * WP_CONTENT_DIR 配下に収まらない場合は null を返し、呼び出し側で
	 * get_remote_css_contents()（HTTP 取得）へのフォールバックに委ねる。
	 *
	 * @internal get_skin_editor_css_style_entry() からのみ呼び出す想定。
	 *           任意のファイルを読み出す一次関数ではなく、外部からの直接呼び出しは想定していない。
	 *
	 * @param string $url CSS ファイルの URL.
	 * @return string|null 解決できたサーバー上のパス。解決できない場合は null.
	 */
	private static function resolve_content_url_to_path( $url ) {
		if ( ! is_string( $url ) || '' === $url ) {
			return null;
		}

		$content_url = untrailingslashit( content_url() );
		if ( 0 !== strpos( $url, $content_url ) ) {
			return null;
		}

		// クエリ文字列・フラグメントが付いている場合に備えて取り除く.
		// strtok() はプロセス全体で内部状態を共有するため使わない（他所の strtok() 呼び出しを
		// 壊す可能性がある）。
		$relative_path = preg_replace( '/[?#].*$/', '', substr( $url, strlen( $content_url ) ) );

		// URL はパーセントエンコードされていることがある（ディレクトリ名・ファイル名に
		// 空白・非 ASCII 文字を含む構成等）。デコードしないと realpath() がファイルを
		// 見つけられず、ローカル解決が黙って失敗して毎回 HTTP フォールバックへ落ちてしまう。
		// デコードで .. のようなパストラバーサル文字列が復元される可能性があるため、
		// 「デコード → 連結 → realpath() → 封じ込めチェック」の順序を必ず守る
		// （デコードを後回しにしない）。
		$relative_path = rawurldecode( $relative_path );

		// %00 がデコードで NUL バイトへ変わることがある。PHP 8 の realpath() は
		// NUL バイトを含むパスを渡すと ValueError を投げるため（PHP 7 は警告 + false）、
		// wp_normalize_path() は NUL を除去しないので素通りしてしまう前提で、
		// realpath() に渡す前にここで弾いておく.
		if ( false !== strpos( $relative_path, "\0" ) ) {
			return null;
		}

		$candidate_path = wp_normalize_path( WP_CONTENT_DIR . $relative_path );
		$real_path      = realpath( $candidate_path );
		if ( false === $real_path ) {
			return null;
		}
		$real_path = wp_normalize_path( $real_path );

		$real_content_dir = realpath( WP_CONTENT_DIR );
		if ( false === $real_content_dir ) {
			return null;
		}
		$real_content_dir = wp_normalize_path( $real_content_dir );

		// WP_CONTENT_DIR 配下に収まっているかを確認する（.. によるパストラバーサル対策）。
		// $url 自体が https://example.com/wp-content-evil/x.css のように content_url() を
		// 文字列として含むだけの別ホストであっても、strpos() の時点では通ってしまうが、
		// このチェックで正規化後のパスが WP_CONTENT_DIR 配下かどうかを見るため弾かれる。
		$is_within_content_dir = ( $real_path === $real_content_dir )
			|| ( 0 === strpos( $real_path, $real_content_dir . '/' ) );
		if ( ! $is_within_content_dir ) {
			return null;
		}

		if ( ! is_file( $real_path ) ) {
			return null;
		}

		return $real_path;
	}

	/**
	 * サーバー上の CSS ファイルの内容を読み込む
	 *
	 * @internal get_skin_editor_css_style_entry() および
	 *           add_skin_css_to_block_editor_settings()（bootstrap.min.css 用）からのみ
	 *           呼び出す想定。$path は is_file() 以外の制約を課さない任意パスの読み出しに
	 *           なるため、外部からの直接呼び出しは想定していない。
	 *
	 * @param string $path サーバー上の CSS ファイルパス.
	 * @return string|null 読み込めた内容。読み込めない場合は null.
	 */
	private static function get_local_css_contents( $path ) {
		if ( ! is_string( $path ) || '' === $path || ! is_file( $path ) ) {
			return null;
		}

		// get_block_editor_theme_styles()（WordPress コア）が同種の CSS を読み込む際も
		// file_get_contents() を直接使っているため、それに合わせている.
		$content = file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $content ) {
			return null;
		}

		return $content;
	}

	/**
	 * CSS の URL を短いタイムアウトと transient キャッシュ付きで HTTP 取得する
	 *
	 * *_sv_path も content_url() からの逆算も使えない場合（新キーに未対応の
	 * スキンプラグインが、content_url() 配下とは異なる URL 構造で CSS を配布している場合等）
	 * の最終手段。wp_safe_remote_get() を使うため、内部 IP・非 HTTP スキームは弾かれるが、
	 * 同一ホスト宛（自己参照）は例外的に許可されるため、正規のユースケース
	 * （スキンプラグインが自サイト上の CSS を返す）は壊れない。
	 *
	 * 失敗・成功のいずれも、URL とスキンのバージョンから作ったキーで transient に
	 * キャッシュする。ブロックエディターの画面を開くたびに毎回 HTTP 往復が発生するのを
	 * 避けるため。成功時は 1 日、失敗時は 1 時間で再試行させる（失敗が長時間そのまま
	 * 固定化しないように短くしている）。
	 *
	 * wp_safe_remote_get() が返す is_wp_error() は**トランスポート層の失敗のみ**
	 * （DNS 不達・接続拒否・タイムアウト等）を表す。401（Basic 認証）・403・404・
	 * 500・502 等は HTTP としては「成功」で返り、wp_remote_retrieve_body() は
	 * エラーページの HTML 本文を返してしまう。これを CSS として扱うと、非 iframe 時に
	 * コアの postcss 変換を通す際の挙動が読めないうえ、本来 1 時間で再試行させたい
	 * 一時的な障害（502 等）まで 1 日キャッシュに固定化されてしまう。そのため
	 * HTTP レスポンスコードが 200 以外の場合・ボディが空の場合は、どちらも
	 * 失敗として扱い 1 時間キャッシュにする。
	 *
	 * 注意: キャッシュキーはスキンの version から作るため、version を返さない
	 * スキンプラグイン（get_current_skin() は未設定なら空文字を補うため、この場合
	 * キーは URL のみで決まる）では、CSS を更新してもキーが変わらず、最大 1 日は
	 * 更新前の内容（または失敗キャッシュなら最大 1 時間）が使われ続ける可能性がある。
	 * TTL に上限があるため実害は限定的。
	 *
	 * @internal get_skin_editor_css_style_entry() からのみ呼び出す想定。
	 *
	 * @param string $url     取得する CSS の URL.
	 * @param string $version キャッシュキーに使うスキンのバージョン文字列.
	 * @return string|null 取得できた CSS の内容。取得できなかった場合は null.
	 */
	private static function get_remote_css_contents( $url, $version ) {
		$cache_key = 'ltg_ed_css_' . md5( $url . '|' . $version );

		$cached = get_transient( $cache_key );
		if ( false !== $cached ) {
			return ( '' === $cached ) ? null : $cached;
		}

		$response = wp_safe_remote_get( $url, array( 'timeout' => 3 ) );
		if ( is_wp_error( $response ) ) {
			set_transient( $cache_key, '', HOUR_IN_SECONDS );
			return null;
		}

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		if ( 200 !== $response_code ) {
			set_transient( $cache_key, '', HOUR_IN_SECONDS );
			return null;
		}

		$css = wp_remote_retrieve_body( $response );
		if ( '' === $css ) {
			set_transient( $cache_key, '', HOUR_IN_SECONDS );
			return null;
		}

		set_transient( $cache_key, $css, DAY_IN_SECONDS );

		return $css;
	}

	/**
	 * スキンの editor CSS からブロックエディター用の styles エントリを組み立てる
	 *
	 * 解決の優先順位は次のとおり（上ほど優先）。
	 * 1. $sv_path（get_skins() の *_sv_path。get_parent_theme_file_path() で作った
	 *    サーバーパス）があればそれをそのまま読む。テーマ同梱のスキン（Origin・Origin II）は
	 *    常にここで解決し、URL 逆算・HTTP 取得を一切通らない。
	 * 2. $sv_path が無い（新キーに未対応のスキンプラグイン等）場合は、$url を
	 *    resolve_content_url_to_path() でサーバーパスへ逆算できないか試みる。
	 * 3. それも駄目な場合だけ、get_remote_css_contents()（wp_safe_remote_get() +
	 *    transient キャッシュ）で HTTP 取得する。
	 *
	 * add_editor_style() を呼ぶ形は採用していない。block_editor_settings_all フィルターの
	 * 時点では、ブロックエディター設定の styles 配列が既に組み立て終わっており、
	 * ここで add_editor_style() を呼んでも今回のリクエストのブロックエディターには
	 * 一切反映されないため（クラシックエディター向けの load_skin_editor_css() と
	 * 同じ URL を二重に登録するだけになる）。
	 *
	 * @internal add_skin_css_to_block_editor_settings() からのみ呼び出す想定。
	 *
	 * @param string $url     スキンが返す editor CSS の URL.
	 * @param string $sv_path get_skins() が返す対応するサーバーパス（*_sv_path）。無ければ空文字.
	 * @param string $version キャッシュキーに使うスキンのバージョン文字列.
	 * @return array|null 追加すべき styles エントリ。取得できなかった場合は null.
	 */
	private static function get_skin_editor_css_style_entry( $url, $sv_path, $version ) {
		$css = null;

		if ( ! empty( $sv_path ) ) {
			$css = self::get_local_css_contents( $sv_path );
		}

		if ( null === $css ) {
			$resolved_path = self::resolve_content_url_to_path( $url );
			if ( null !== $resolved_path ) {
				$css = self::get_local_css_contents( $resolved_path );
			}
		}

		if ( null === $css ) {
			$css = self::get_remote_css_contents( $url, $version );
		}

		if ( null === $css ) {
			return null;
		}

		return array(
			'css'            => $css,
			'baseURL'        => $url,
			// __unstableType は WordPress コアの private API（wp-includes/block-editor.php の
			// get_block_editor_theme_styles() 等が使う内部識別子）で、予告なく変更されうる。
			'__unstableType' => 'theme',
			'isGlobalStyles' => false,
		);
	}

	/**
	 * スキンの CSS（Bootstrap ＋ スキンの editor CSS）をブロックエディター自身の styles へ追加する
	 *
	 * 従来は bootstrap.min.css・スキンの editor.css を enqueue_block_assets フックで
	 * wp_enqueue_style() していたが（旧 load_skin_gutenberg_css()）、この方法は仕様上
	 * ブロックエディターのプレビュー用 iframe だけでなく、管理画面のトップレベル文書
	 * （wp-admin 本体）にも <link> として出力されてしまう。origin2（Bootstrap4）スキンの
	 * editor.css には裸の body / html セレクタへの font-size・font-family 指定が含まれるため、
	 * これが管理画面全体の基準文字サイズ・フォントを書き換えてしまい、サイドバー最下部の
	 * 「メニューを閉じる」ボタンの文字が折り返される不具合の原因になっていた。
	 *
	 * block_editor_settings_all フィルターで $editor_settings['styles'] へ直接 CSS を
	 * 追加する方式に切り替えた。この配列は WordPress コアの EditorStyles コンポーネントが
	 * セレクタをエディター本文のスコープ（iframe 化されている場合は iframe 内の body、
	 * されていない場合は .editor-styles-wrapper）へ書き換えたうえで描画するため、
	 * この関数が追加する CSS は管理画面本体の <body class="wp-admin"> には構造的に
	 * 到達しない。
	 *
	 * なお _g2/functions.php の lightning_load_common_editor_css_to_gutenberg() は、
	 * 引き続き enqueue_block_assets 経由で assets/css/common_editor.css を管理画面本体にも
	 * 出し続けているが、これは意図した状態である。同 CSS の裸の body { ... } 規則は
	 * --vk-width-editor-sidebar 等のカスタムプロパティ定義のみで、font-size・font-family は
	 * 一切持たない（唯一の font-family 指定は html :where(.editor-styles-wrapper){...} と
	 * 既にスコープされている）ことを確認済みで、この不具合は再発しない。
	 *
	 * 非 iframe のブロックエディター画面（widgets.php、カスタマイザー内のブロックウィジェット
	 * 編集、旧来型メタボックスを持つプラグイン有効時に非 iframe へフォールバックした投稿編集画面
	 * 等）では、WordPress コアが html セレクタを .editor-styles-wrapper へ書き換えるため、
	 * rem の基準がフロントと異なる。rem 指定の見出し等（例: .h2,.mainSection-title,h2
	 * { font-size: 1.75rem }）はフロントと違うサイズで表示される。widgets.php は
	 * add_root_font_size_for_block_editor() で対処済み（詳細は同メソッドのコメントを参照）。
	 * カスタマイザー内のブロックウィジェット編集と、非 iframe にフォールバックした投稿編集画面は
	 * 対象外（未対応）で、この差は既知の制約として残っている。
	 *
	 * 旧実装にあった is_customize_preview() での早期リターンは、この方式には引き継いでいない。
	 * 旧実装（enqueue_block_assets + wp_enqueue_style()）はカスタマイザー内で発火すると
	 * 管理画面本体にも <link> が漏れるため、is_customize_preview() で個別に塞ぐ必要があった。
	 * この方式は block_editor_settings_all の $editor_settings['styles'] へ追記するだけで、
	 * どの文脈で呼ばれても管理画面本体には構造的に到達しないため、カスタマイザー内で
	 * 発火したとしても新たな漏れは発生しない（is_admin() のチェックのみで足りる）。
	 *
	 * 追加順（bootstrap.min.css → スキンの editor_css_path → gutenberg_css_path）は、
	 * 従来の load_skin_gutenberg_css() での enqueue 順を維持している。従来は
	 * editor_css_path と gutenberg_css_path を同じハンドル（lightning-gutenberg-editor）で
	 * enqueue していたため、両方設定されたスキンでは片方しか読まれなかったが、この実装では
	 * 両方読み込む（両方指定したら両方読まれるのが素直な挙動であり、旧実装の取りこぼしを
	 * 解消したもの。現行のスキン定義2本には両方設定したものは無いため、この変更による
	 * 退行は無い）。
	 *
	 * bootstrap.min.css・editor_css_path は bs4 系スキン（'bootstrap' => 'bs4'）のときだけ
	 * 追加する（従来の load_skin_gutenberg_css() と同じ条件）。一方 gutenberg_css_path は
	 * bs4 かどうかに関係なく処理する。'bootstrap' キーを持たない bs3 系スキンプラグイン
	 * （例: lightning-skin-charm・lightning-skin-fort の bs3 版）にも gutenberg_css_path を
	 * 返すものが実在するため、bs4 判定の内側に入れると該当スキンでブロックエディターに
	 * スキン CSS が一切読み込まれなくなる（旧実装から踏襲した分岐）。
	 *
	 * block_editor_settings_all はフロント側でブロックエディターを実装するプラグインからも
	 * 発火し得るため、is_admin() で管理画面に限定する（従来の load_skin_gutenberg_css() の
	 * 挙動を踏襲）。フロントにはスキンの style.css が wp_enqueue_scripts 側で別途
	 * 読み込まれるため、ここでエディター用 CSS を追加で出す必要はない。
	 *
	 * bootstrap.min.css は _g2/library/bootstrap-4/css/bootstrap.min.css に同梱されている。
	 * このテーマでは template_directory_uri フィルター（class-ltg-template-redirect.php）が
	 * get_template_directory_uri() の戻り値に自動で /_g2 を付与するが、get_template_directory()
	 * （URL ではなくファイルシステムパスを返す方）にはその補正が入らない（同ファイルの
	 * template_directory フィルターは意図的にコメントアウトされている）。そのため
	 * get_template_directory() . '/library/...' を直接組み立てると _g2 の無い誤ったパスに
	 * なる。get_parent_theme_file_path フィルターは /_g2 の付与に対応しているため、
	 * サーバーパスの組み立てには get_template_directory() ではなく
	 * get_parent_theme_file_path() を使う。
	 *
	 * この方式では CSS がブラウザキャッシュの効く <link> ではなく、編集画面ごとに
	 * ブロックエディター設定へ埋め込まれる（bs4 系（origin2）で bootstrap.min.css 153,670
	 * バイト＋ editor.css 5,453 バイトの合計約 159KB、gzip 前の実測値）。
	 * エディター本文へ正しくスコープすることの構造的な対価として許容している。
	 *
	 * @param array                        $editor_settings      ブロックエディター設定.
	 * @param WP_Block_Editor_Context|null $block_editor_context ブロックエディターのコンテキスト（未使用）.
	 * @return array フィルター後のブロックエディター設定.
	 */
	public static function add_skin_css_to_block_editor_settings( $editor_settings, $block_editor_context = null ) {
		if ( ! is_admin() ) {
			return $editor_settings;
		}

		$skin_info = self::get_current_skin();

		if ( ! isset( $editor_settings['styles'] ) || ! is_array( $editor_settings['styles'] ) ) {
			$editor_settings['styles'] = array();
		}

		// bs4 系スキン（bootstrap.min.css を同梱で読み込むスキン）のときだけ、
		// bootstrap.min.css とスキンの editor_css_path を追加する.
		if ( ! empty( $skin_info['bootstrap'] ) && 'bs4' === $skin_info['bootstrap'] ) {
			// bootstrap.min.css は自テーマ同梱のため、URL 逆算を介さずサーバーパスを直接組み立てる.
			$bootstrap_path = get_parent_theme_file_path( '/library/bootstrap-4/css/bootstrap.min.css' );
			$bootstrap_css  = self::get_local_css_contents( $bootstrap_path );
			if ( null !== $bootstrap_css ) {
				$editor_settings['styles'][] = array(
					'css'            => $bootstrap_css,
					'__unstableType' => 'theme',
					'isGlobalStyles' => false,
				);
			}

			if ( ! empty( $skin_info['editor_css_path'] ) ) {
				$entry = self::get_skin_editor_css_style_entry(
					$skin_info['editor_css_path'],
					isset( $skin_info['editor_css_sv_path'] ) ? $skin_info['editor_css_sv_path'] : '',
					$skin_info['version']
				);
				if ( null !== $entry ) {
					$editor_settings['styles'][] = $entry;
				}
			}
		}

		// gutenberg_css_path は bs4 かどうかに関係なく処理する（上記コメント参照）.
		if ( ! empty( $skin_info['gutenberg_css_path'] ) ) {
			$entry = self::get_skin_editor_css_style_entry(
				$skin_info['gutenberg_css_path'],
				isset( $skin_info['gutenberg_css_sv_path'] ) ? $skin_info['gutenberg_css_sv_path'] : '',
				$skin_info['version']
			);
			if ( null !== $entry ) {
				$editor_settings['styles'][] = $entry;
			}
		}

		return $editor_settings;
	}

	/**
	 * ブロックエディターのルート文字サイズの基準をフロントと揃える（ウィジェット画面限定）
	 *
	 * block_editor_settings_all で渡す $editor_settings['styles'] は、WordPress コアが
	 * セレクタをエディター本文のスコープへ書き換えてから描画する。ブロックエディターの
	 * プレビュー用 iframe（投稿・固定ページ編集画面、サイトエディター）では iframe 自身の
	 * 実の html 要素にそのまま届くため、テーマの html { font-size: ... } はフロントと
	 * 同じ基準で効く。一方、iframe 化されていないウィジェット画面（widgets.php）では、
	 * コアが html セレクタを .editor-styles-wrapper 自身へ書き換えてしまう。rem は常に
	 * 「本当の」ルート要素（このケースでは管理画面本体の実の html 要素）の font-size を
	 * 基準に計算されるため、.editor-styles-wrapper だけに font-size を与えても rem 指定の
	 * 見出し等（例: .h2,.mainSection-title,h2 { font-size: 1.75rem }）はフロントと異なる
	 * サイズで表示されてしまう。
	 *
	 * この対処は widgets.php（$pagenow で判定）に限定して、管理画面本体の実の html 要素に
	 * のみ font-size を与える。html セレクタのみに絞っており、body の font-size・font-family
	 * までは含めない。
	 *
	 * origin2 スキンの editor.css は 992px 以下で body,html{font-size:14px}、992px 超で
	 * body,html{font-size:16px} を指定している。この関数は同じブレークポイント・比率を
	 * 管理画面本体の実の html 要素にも与える（87.5% = 14px、100% = 16px、いずれも
	 * ブラウザの既定文字サイズ 16px からの相対値）。
	 *
	 * トレードオフ: この出力により、幅 992px 以下の widgets.php では管理画面本体の rem 基準が
	 * 16px から 14px に変わり、wp-admin/css/forms.css の input[type=checkbox] /
	 * input[type=radio]（1rem 前提）が約 2px（12.5%）縮小する。これは意図した代償である。
	 * 出力しない場合、ウィジェット画面のブロックプレビューで rem 指定の見出しがフロントより
	 * 大きく表示され続けるため、編集対象ではない管理 UI が 2px 縮むことより、編集中の本文の
	 * 見た目がフロントとずれ続けることの方が実害が大きいと判断し、こちらを選んでいる。
	 *
	 * @return void
	 */
	public static function add_root_font_size_for_block_editor() {
		if ( ! is_admin() ) {
			return;
		}

		global $pagenow;
		if ( 'widgets.php' !== $pagenow ) {
			return;
		}

		$skin_info = self::get_current_skin();
		if ( empty( $skin_info['bootstrap'] ) || 'bs4' !== $skin_info['bootstrap'] ) {
			return;
		}

		// design-skin/foundation/_scss/_variables.scss の $md-max（現在 992px）に合わせている。
		// ビルド済み CSS が出力するレンジ構文 @media (992px < width) と表記を揃えている
		// （min-width の等号境界での食い違いを避けるため）。$md-max を変更した場合は
		// この値も追随させること（自動連携の手段は無いため、変更時に手動で揃える）。
		//
		// テーマの SCSS 側は px 表記だが、ここだけ意図的に相対単位（%）にしている。px 固定だと、
		// 読みやすさのためにブラウザの既定文字サイズを（16px より）大きく設定している利用者の
		// 設定を無条件に握りつぶし、rem 基準の管理 UI がその人にとって縮んで見えてしまう。
		// % はブラウザの既定文字サイズからの相対値のため、既定（16px）の利用者への挙動は
		// px 指定と完全に同じ（87.5% = 14px、100% = 既定値そのもの）ままで、既定より大きく
		// 設定している利用者の設定だけを尊重できる。
		wp_add_inline_style(
			'wp-edit-blocks',
			'html{font-size:87.5%}@media (992px < width){html{font-size:100%}}'
		);
	}

	/**
	 * Load_skin_php
	 *
	 * @return void
	 */
	public static function load_skin_php() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['php_path'] ) && file_exists( $skin_info['php_path'] ) ) {
			require $skin_info['php_path'];
		}
	}

	/**
	 * Load skin callback
	 *
	 * @return void
	 */
	public static function load_skin_callback() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['callback'] ) && $skin_info['callback'] ) {
			call_user_func_array( $skin_info['callback'], array() );
		}
	}

	/**
	 * Customize register
	 *
	 * @param object $wp_customize : customize object.
	 * @return void
	 */
	public static function customize_register( $wp_customize ) {
		$wp_customize->add_setting(
			'lightning_design_skin',
			array(
				'default'           => 'origin2',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$skins = self::get_skins();
		foreach ( $skins as $k => $v ) {
			$skins[ $k ] = isset( $v['label'] ) ? $v['label'] : $k;
		}

		$wp_customize->add_control(
			'lightning_design_skin',
			array(
				'label'       => __( 'Design skin', 'lightning' ),
				'section'     => 'lightning_design',
				'settings'    => 'lightning_design_skin',
				'description' => '<span style="color:red;font-weight:bold;">' . __( 'If you change the skin, please save once and reload the page.', 'lightning' ) . '</span><br/>' .
					__( 'If you reload after the saving, it will be displayed skin-specific configuration items.', 'lightning' ) . '<br/> ' .
					__( '*There is also a case where there is no skin-specific installation item.', 'lightning' ),
				'type'        => 'select',
				'priority'    => 100,
				'choices'     => $skins,
			)
		);
	}
}

Lightning_Design_Manager::init();
