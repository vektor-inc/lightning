<?php
class Lightning_Design_Manager {

	static function init() {
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_skin_css_and_js' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_editor_css' ) );
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'load_skin_gutenberg_css' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_php' ) );
		add_action( 'after_setup_theme', array( __CLASS__, 'load_skin_callback' ) );
		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
	}


	// Set default skin
	static function get_skins() {
		$skins = array(
			'origin' => array(
				'label'              => 'Origin',
				'css_path'           => get_template_directory_uri() . '/design-skin/origin/css/style.css',
				'editor_css_path'    => get_template_directory_uri() . '/design-skin/origin/css/editor.css',
				'gutenberg_css_path' => '',
				'php_path'           => get_parent_theme_file_path( '/design-skin/origin/origin.php' ),
				'js_path'            => '',
				'callback'           => '',
				'version'            => LIGHTNING_THEME_VERSION,
			),
		);
		return apply_filters( 'lightning-design-skins', $skins );
	}

	static function get_current_skin() {
		$skins        = self::get_skins();
		$current_skin = get_option( 'lightning_design_skin' );
		if ( ! $current_skin ) {
			$current_skin = 'origin';
		}
		if ( ! isset( $skins[ $current_skin ]['version'] ) ) {
			$skins[ $current_skin ]['version'] = '';
		}
		return $skins[ $current_skin ];
	}

	static function load_skin_css_and_js() {
		$skin_info = self::get_current_skin();

		if ( ! empty( $skin_info['css_path'] ) ) {
			wp_enqueue_style( 'lightning-design-style', $skin_info['css_path'], array(), $skin_info['version'] );
		}

		if ( ! empty( $skin_info['js_path'] ) ) {
			wp_enqueue_script( 'lightning-design-js', $skin_info['js_path'], array( 'jquery' ), $skin_info['version'], true );
		}

	}

	static function load_skin_editor_css() {
		$skin_info = self::get_current_skin();
		if ( ! empty( $skin_info['editor_css_path'] ) ) {
			$version = '';
			if ( $skin_info['version'] ) {
				$version = '?ver=' . $skin_info['version'];
			}
			add_editor_style( $skin_info['editor_css_path'] . $version );
		}

	}
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
			'lightning_design_skin', array(
				'default'           => 'origin',
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
			'lightning_design_skin', array(
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
