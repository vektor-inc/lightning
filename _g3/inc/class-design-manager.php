<?php
class Lightning_Design_Manager {

	static function init() {

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_skin_css_and_js' ) );

		// Don't use following action point.
		// wp : Bring to phpunit test error
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_php' ) );

		add_action( 'wp', array( __CLASS__, 'load_skin_callback' ) );


		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );

		/**
		 * 編集画面において enqueue_block_editor_assets は上部で add_editor_style は下部で読み込まれる
		 * -> 両方書くと enqueue_block_editor_assets で定義した CSS に wp_add_inline_style で引っ掛けても効かない
		 * -> add_editor_style は Classic Editor 専用にすることで解決
		 */
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_editor_css' ) );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'load_skin_gutenberg_css' ) );

	}

	/**
	 * Set default skin
	 *
	 * @return array スキン固有で読み込むファイル情報の配列
	 */
	static function get_skins() {
		$skins = array(
			'plain'   => array(
				'label'          => __( 'Plain', 'lightning' ),
				'css_url'        => get_template_directory_uri() . '/design-skin/plain/css/style.css', // 中身が空でも指定しないと lightning-design-style がが出力されずに インラインCSSが効かないため',
				'css_path'       => get_parent_theme_file_path( '/design-skin/plain/css/style.css' ),
				'editor_css_url' => '',
				'php_path'       => '',
				'js_url'         => '',
				'version'        => LIGHTNING_THEME_VERSION,
				'bootstrap'      => '',
			),
			'origin3' => array(
				'label'                    => __( 'Origin III', 'lightning' ),
				'css_url'                  => get_template_directory_uri() . '/design-skin/origin3/css/style.css',
				'css_path'                 => get_parent_theme_file_path( '/design-skin/origin3/css/style.css' ),
				'editor_css_path_relative' => '_g3/design-skin/origin3/css/editor.css',
				'editor_css_url'           => get_template_directory_uri() . '/design-skin/origin3/css/editor.css',
				'php_path'                 => get_parent_theme_file_path() . '/design-skin/origin3/origin3.php',
				'js_url'                   => '',
				'version'                  => LIGHTNING_THEME_VERSION,
				'bootstrap'                => '',
			),
		);
		return apply_filters( 'lightning_g3_skins', $skins );
	}


	/**
	 * Only using deal with plugin skin deactive fallback.
	 *
	 * @return [array] Return plugin url to activate check.
	 */
	static function get_skins_info() {
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
			$current_skin = 'origin3';
		}

		// If selected skin plugin is deactive that, set to default skin.
		if ( $current_skin != 'origin3' ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			$skins_info = self::get_skins_info();
			if ( isset( $skins_info[ $current_skin ]['plugin_path'] ) && ! is_plugin_active( $skins_info[ $current_skin ]['plugin_path'] ) ) {
				$current_skin = 'origin3';
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
	 * @return [type] [description]
	 */
	static function load_skin_css_and_js() {
		$skin_info = self::get_current_skin();

		$skin_css_url = '';
		if ( ! empty( $skin_info['css_url'] ) ) {
			$skin_css_url = $skin_info['css_url'];
		}

		wp_enqueue_style( 'lightning-design-style', $skin_css_url, array( 'lightning-common-style' ), $skin_info['version'] );

		// load JS ///////////////////////

		if ( ! empty( $skin_info['js_url'] ) ) {
			wp_enqueue_script( 'lightning-design-js', esc_url( $skin_info['js_url'] ), array(), $skin_info['version'], true );
		}

	}

	/**
	 * Load skin Editor CSS
	 *
	 * @return [type] [description]
	 */
	static function load_skin_editor_css() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['editor_css_path_relative'] ) ) {
			add_editor_style( $skin_info['editor_css_path_relative'] );
		} elseif ( ! empty( $skin_info['editor_css_url'] ) ) {
			add_editor_style( $skin_info['editor_css_url'] );
		}
	}

	// This method is planned to be removed.
	// It's aleady don't need function that add_editor_style() become covered gutenberg more better.
	static function load_skin_gutenberg_css() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['gutenberg_css_url'] ) ) {
			wp_enqueue_style(
				'lightning-gutenberg-editor',
				$skin_info['gutenberg_css_url'],
				array( 'wp-edit-blocks' ),
				$skin_info['version']
			);
		}

	}

	static function load_skin_php() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['php_path'] ) && file_exists( $skin_info['php_path'] ) ) {
			require $skin_info['php_path'];
		}
	}

	static function load_skin_callback() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['callback'] ) and $skin_info['callback'] ) {
			call_user_func_array( $skin_info['callback'], array() );
		}
	}

	static function customize_register( $wp_customize ) {
		$wp_customize->add_setting(
			'lightning_design_skin',
			array(
				'default'           => 'origin3',
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
