<?php
class Lightning_Design_Manager {

	static function init() {

		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_skin_css_and_js' ) );
		// Set to priority 9 that to be load before theme style.css
		add_action( 'after_setup_theme', array( __CLASS__, 'load_late_css_action' ), 9 );

		// Don't use following action point.
		// wp : Bring to phpunit test error
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_php' ) );

		add_action( 'wp', array( __CLASS__, 'load_skin_callback' ) );

		// Don't use following action point.
		// wp : become do not load css.
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_editor_css' ) );

		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );

		// This method is planned to be removed.
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'load_skin_gutenberg_css' ) );

	}

	/**
	 * Set default skin
	 *
	 * @return array スキン固有で読み込むファイル情報の配列
	 */
	static function get_skins() {
		$skins = array(
			'origin'  => array(
				'label'           => __( 'Origin ( Not recommended )', 'lightning' ),
				'css_path'        => get_template_directory_uri() . '/design-skin/origin/css/style.css',
				'css_sv_path'     => get_parent_theme_file_path( '/design-skin/origin/css/style.css' ),
				'editor_css_path' => get_template_directory_uri() . '/design-skin/origin/css/editor.css',
				// 'gutenberg_css_path' => get_template_directory_uri() . '/design-skin/origin/css/editor-gutenberg.css',
				'php_path'        => get_parent_theme_file_path( '/design-skin/origin/origin.php' ),
				'js_path'         => '',
				'callback'        => '',
				'version'         => LIGHTNING_THEME_VERSION,
			),
			'origin2' => array(
				'label'           => __( 'Origin II ( Bootstrap4 )', 'lightning' ),
				'css_path'        => get_template_directory_uri() . '/design-skin/origin2/css/style.css',
				'css_sv_path'     => get_parent_theme_file_path( '/design-skin/origin2/css/style.css' ),
				'css_late_path'   => '',
				'editor_css_path' => get_template_directory_uri() . '/design-skin/origin2/css/editor.css',
				'php_path'        => get_parent_theme_file_path( '/design-skin/origin2/origin2.php' ),
				'js_path'         => '',
				'version'         => LIGHTNING_THEME_VERSION,
				'bootstrap'       => 'bs4',
			),
		);
		return apply_filters( 'lightning-design-skins', $skins );
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
			$current_skin = 'origin2';
		}

		// If selected skin plugin is deactive that, set to default skin.
		if ( $current_skin != 'origin2' ) {
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
	 * @return [type] [description]
	 */
	static function load_skin_css_and_js() {
		$skin_info = self::get_current_skin();

		$skin_css_url = '';
		if ( ! empty( $skin_info['css_path'] ) ) {
			$skin_css_url = $skin_info['css_path'];
		}

		// load bootstrap ///////////////////////

		global $bootstrap;
		$bootstrap = '3';
		if ( empty( $skin_info['bootstrap'] ) ) {
			// Bootstrap3 skin
			// load bootstrap3 js
			wp_enqueue_script( 'bootstrap-js', get_template_directory_uri() . '/library/bootstrap-3/js/bootstrap.min.js', array( 'jquery' ), '3.4.1', true );

			wp_enqueue_style( 'lightning-design-style', $skin_css_url, array(), $skin_info['version'] );

		} elseif ( $skin_info['bootstrap'] == 'bs4' ) {
			$options = get_option( 'lightning_theme_options' );
			$bootstrap = '4';
			// Bootstrap4 skin
			$bs4_version = '4.5.0';
			$bs4_css_url = '';
			if ( ! empty( $options['optimize_css'] ) && 'minimal-bootstrap' === $options['optimize_css'] ) {
				$bs4_css_url = get_template_directory_uri() . '/library/bootstrap-4/css/bootstrap-lite.min.css';
			} else {
				$bs4_css_url = get_template_directory_uri() . '/library/bootstrap-4/css/bootstrap.min.css';
			}
			wp_enqueue_style( 'bootstrap-4-style', $bs4_css_url, array(), $bs4_version );

			wp_enqueue_script( 'bootstrap-4-js', get_template_directory_uri() . '/library/bootstrap-4/js/bootstrap.min.js', array( 'jquery' ), $bs4_version, true );

			// load skin CSS ///////////////////////

			wp_enqueue_style( 'lightning-design-style', $skin_css_url, array( 'bootstrap-4-style','lightning-common-style' ), $skin_info['version'] );

		}

		// load JS ///////////////////////

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

	static function load_late_load_css() {
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
	 * @return [type] [description]
	 */
	static function load_skin_editor_css() {
		$skin_info = self::get_current_skin();

		if ( isset( $skin_info['bootstrap'] ) && $skin_info['bootstrap'] == 'bs4' ) {
			// Bootstrap4 skin
			/*
			 Notice : Use url then if you use local environment https has error that bring to get css error and don't refrected */
			/* Notice : add_editor_style() is only one args. */
			add_editor_style( 'library/bootstrap-4/css/bootstrap.min.css' );
		}

		if ( ! empty( $skin_info['editor_css_path'] ) ) {
			add_editor_style( $skin_info['editor_css_path'] );
		}
	}

	// This method is planned to be removed.
	// It's aleady don't need function that add_editor_style() become covered gutenberg more better.
	static function load_skin_gutenberg_css() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['gutenberg_css_path'] ) ) {
			wp_enqueue_style(
				'lightning-gutenberg-editor',
				$skin_info['gutenberg_css_path'],
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
