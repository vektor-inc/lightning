<?php
/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_design' );
function lightning_customize_register_design( $wp_customize ) {

	$wp_customize->add_section(
		'lightning_design', array(
			'title'    => lightning_get_theme_name() . ' ' . __( 'Design settings', 'lightning' ),
			'priority' => 500,
		// 'panel'				=> 'lightning_setting',
		)
	);

	// Add setting

	// head logo
	$wp_customize->add_setting(
		'lightning_theme_options[head_logo]', array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Image_Control(
			$wp_customize,
			'head_logo',
			array(
				'label'       => __( 'Header logo image', 'lightning' ),
				'section'     => 'lightning_design',
				'settings'    => 'lightning_theme_options[head_logo]',
				'priority'    => 501,
				'description' => __( 'Recommended image size : 280*60px', 'lightning' ),
			)
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'lightning_theme_options[head_logo]', array(
			'selector'        => '.siteHeader_logo',
			'render_callback' => '',
		)
	);

	// color
	$wp_customize->add_setting(
		'lightning_theme_options[color_key]', array(
			'default'           => '#337ab7',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_setting(
		'lightning_theme_options[color_key_dark]', array(
			'default'           => '#2e6da4',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);

	if ( apply_filters( 'lightning_show_default_keycolor_customizer', true ) ) {
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize, 'color_key', array(
					'label'    => __( 'Key color', 'lightning' ),
					'section'  => 'lightning_design',
					'settings' => 'lightning_theme_options[color_key]',
					'priority' => 502,
				)
			)
		);
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize, 'color_key_dark', array(
					'label'    => __( 'Key color (dark)', 'lightning' ),
					'section'  => 'lightning_design',
					'settings' => 'lightning_theme_options[color_key_dark]',
					'priority' => 503,
				)
			)
		);
	}

	// top_sidebar_hidden
	$wp_customize->add_setting(
		'lightning_theme_options[top_sidebar_hidden]', array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[top_sidebar_hidden]', array(
			'label'       => __( 'Don\'t show sidebar on home page', 'lightning' ),
			'section'     => 'lightning_design',
			'settings'    => 'lightning_theme_options[top_sidebar_hidden]',
			'type'        => 'checkbox',
			'priority'    => 520,
			'description' => __( '* Even if there is no check here, if the page template specified on the top page is "without sidebar", it will be 1 column.', 'lightning' ),
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'lightning_theme_options[top_sidebar_hidden]', array(
			'selector'        => '.home .mainSection',
			'render_callback' => '',
		)
	);

	// top_default_content_hidden
	$wp_customize->add_setting(
		'lightning_theme_options[top_default_content_hidden]', array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[top_default_content_hidden]', array(
			'label'    => __( 'Don\'t show default content(Post list or Front page) at home page', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[top_default_content_hidden]',
			'type'     => 'checkbox',
			'priority' => 521,
		)
	);

	// postUpdate_hidden
	$wp_customize->add_setting(
		'lightning_theme_options[postUpdate_hidden]', array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[postUpdate_hidden]', array(
			'label'    => __( 'Hide modified date on single pages.', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[postUpdate_hidden]',
			'type'     => 'checkbox',
			'priority' => 522,
		)
	);

	// postAuthor_hidden
	$wp_customize->add_setting(
		'lightning_theme_options[postAuthor_hidden]', array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[postAuthor_hidden]', array(
			'label'    => __( 'Don\'t display post author on a single page', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[postAuthor_hidden]',
			'type'     => 'checkbox',
			'priority' => 523,
		)
	);

	// sidebar_child_list_hidden
	$wp_customize->add_setting(
		'lightning_theme_options[sidebar_child_list_hidden]', array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[sidebar_child_list_hidden]', array(
			'label'    => __( 'Sidebar menu hide list children', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[sidebar_child_list_hidden]',
			'type'     => 'checkbox',
			'priority' => 550,
		)
	);

	// sidebar_fix
	$wp_customize->add_setting(
		'lightning_theme_options[sidebar_fix]', array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[sidebar_fix]', array(
			'label'    => __( 'Don\'t fix the sidebar', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[sidebar_fix]',
			'type'     => 'checkbox',
			'priority' => 555,
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'lightning_theme_options[sidebar_fix]', array(
			'selector'        => '.sideSection',
			'render_callback' => '',
		)
	);

	// sidebar_position
	$wp_customize->add_setting(
		'lightning_theme_options[sidebar_position]', array(
			'default'           => 'right',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_radio',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[sidebar_position]', array(
			'label'    => __( 'Sidebar position ( PC mode )', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[sidebar_position]',
			'type'     => 'radio',
			'choices'  => array(
				'right' => __( 'Right', 'lightning' ),
				'left'  => __( 'Left', 'lightning' ),
			),
			'priority' => 610,
		)
	);

}

/*-------------------------------------------*/
/*	Lightning custom color Print head
/*	* This is used for Contents and Plugins and others
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_output_keycolor_css', 1 );
function lightning_output_keycolor_css() {
	$options        = get_option( 'lightning_theme_options' );
	$colors_default = array(
		'color_key'      => empty( $options['color_key'] ) ? '#337ab7' : $options['color_key'],
		'color_key_dark' => empty( $options['color_key_dark'] ) ? '#2e6da4' : $options['color_key_dark'],
	);
	$colors         = apply_filters( 'lightning_keycolors', $colors_default );
	$types          = array(
		'_bg'     => 'background-color',
		'_txt'    => 'color',
		'_border' => 'border-color',
	);
	reset( $colors );
	$dynamic_css = '/* ltg theme common */';
	foreach ( $colors as $k => $v ) {
		reset( $types );
		foreach ( $types as $kk => $vv ) {
			$dynamic_css .= ".{$k}{$kk},.{$k}{$kk}_hover:hover{{$vv}: {$v};}";
		}
	}
	// delete before after space
	$dynamic_css = trim( $dynamic_css );
	// convert tab and br to space
	$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
	// Change multiple spaces to single space
	$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
	wp_add_inline_style( 'lightning-design-style', $dynamic_css );
}

/*-------------------------------------------*/
/*	Print head
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_print_css_common', 2 );
function lightning_print_css_common() {
	$options     = get_option( 'lightning_theme_options' );
	$dynamic_css = '';
	// 全幅ウィジェット等を使用した際にWindowsで横スクロールバーが出る対策( Charm 2.2.2 / Variety 2.3.2 以降リリースしたら削除)
	$dynamic_css = 'html, body { overflow-x: hidden; }';

	if ( isset( $options['color_key'] ) && isset( $options['color_key_dark'] ) ) {
		$color_key      = ( ! empty( $options['color_key'] ) ) ? esc_html( $options['color_key'] ) : '#337ab7';
		$color_key_dark = ( ! empty( $options['color_key_dark'] ) ) ? esc_html( $options['color_key_dark'] ) : '#2e6da4';
		$dynamic_css   .= '
		.bbp-submit-wrapper .button.submit,
		.woocommerce a.button.alt:hover,
		.woocommerce-product-search button:hover,
		.woocommerce button.button.alt { background-color:' . $color_key_dark . ' ; }
		.bbp-submit-wrapper .button.submit:hover,
		.woocommerce a.button.alt,
		.woocommerce-product-search button,
		.woocommerce button.button.alt:hover { background-color:' . $color_key . ' ; }
		.woocommerce ul.product_list_widget li a:hover img { border-color:' . $color_key . '; }
		.veu_color_txt_key { color:' . $color_key_dark . ' ; }
		.veu_color_bg_key { background-color:' . $color_key_dark . ' ; }
		.veu_color_border_key { border-color:' . $color_key_dark . ' ; }
		.btn-default { border-color:' . $color_key . ';color:' . $color_key . ';}
		.btn-default:focus,
		.btn-default:hover { border-color:' . $color_key . ';background-color: ' . $color_key . '; }
		.btn-primary { background-color:' . $color_key . ';border-color:' . $color_key_dark . '; }
		.btn-primary:focus,
		.btn-primary:hover { background-color:' . $color_key_dark . ';border-color:' . $color_key . '; }
		';
	} // if ( isset($options['color_key'] && isset($options['color_key_dark'] ) {

	/*  Child list hidden
	/*-------------------------------------------*/
	if ( isset( $options['sidebar_child_list_hidden'] ) && $options['sidebar_child_list_hidden'] ) {
		$dynamic_css .= '/* sidebar child menu display */
		.localNav ul ul.children	{ display:none; }
		.localNav ul li.current_page_ancestor	ul.children,
		.localNav ul li.current_page_item ul.children,
		.localNav ul li.current-cat ul.children{ display:block; }';
	}

	if ( isset( $options['sidebar_position'] ) && $options['sidebar_position'] === 'left' ) {
		$dynamic_css .= '@media (min-width: 992px) { .siteContent .subSection { float:left;margin-left:0; } .siteContent .mainSection { float:right; } }';
	}

	if ( $dynamic_css ) {
		// delete br
		$dynamic_css = str_replace( PHP_EOL, '', $dynamic_css );
		// delete tab
		$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
		// multi space convert to single space
		$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );

		wp_add_inline_style( 'lightning-design-style', $dynamic_css );

	}

}

/*-------------------------------------------*/
/*	add body class
/*-------------------------------------------*/
add_filter( 'body_class', 'ltg_add_body_class_sidefix' );
function ltg_add_body_class_sidefix( $class ) {
	$options = get_option( 'lightning_theme_options' );
	if ( ! isset( $options['sidebar_fix'] ) || ! $options['sidebar_fix'] ) {
		if ( apply_filters( 'lightning_sidefix_enable', true ) ) {
			$class[] = 'sidebar-fix';
		}
	}
	return $class;
}

/*-------------------------------------------*/
/*  編集ショートカットボタンの位置調整（ウィジェットのショートカットボタンと重なってしまうため）
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_customize_preview_css_design', 2 );
function lightning_customize_preview_css_design() {
	if ( is_customize_preview() ) {
		$custom_css = '.sideSection > .customize-partial-edit-shortcut-lightning_theme_options-sidebar_fix { left:0px; }';
		wp_add_inline_style( 'lightning-design-style', $custom_css );
	}
}
