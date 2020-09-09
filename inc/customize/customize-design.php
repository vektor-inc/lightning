<?php
/*
  customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_design' );
function lightning_customize_register_design( $wp_customize ) {

	$wp_customize->add_section(
		'lightning_design',
		array(
			'title'    => lightning_get_prefix_customize_panel() . __( 'Design settings', 'lightning' ),
			'priority' => 501,
		// 'panel'				=> 'lightning_setting',
		)
	);

	// Add setting

	// head logo
	$wp_customize->add_setting(
		'lightning_theme_options[head_logo]',
		array(
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
		'lightning_theme_options[head_logo]',
		array(
			'selector'        => '.siteHeader_logo:not(.siteHeader_logo-trans-true)',
			'render_callback' => '',
		)
	);

	/*
	Unuse Judgment
	if ( apply_filters( 'lightning_show_default_keycolor_customizer', true ) ) {
	*/

	/*
	  Color Setting
	/*-------------------------------------------*/
	$wp_customize->add_setting(
		'color_header',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new Custom_Html_Control(
			$wp_customize,
			'color_header',
			array(
				'label'            => __( 'Color Setting', 'lightning' ),
				'section'          => 'lightning_design',
				'type'             => 'text',
				'custom_title_sub' => __( 'Key Color', 'lightning' ),
				'custom_html'      => '',
				'priority'         => 600,
			)
		)
	);

	// color
	$wp_customize->add_setting(
		'lightning_theme_options[color_key]',
		array(
			'default'           => '#337ab7',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'color_key',
			array(
				'label'    => __( 'Key color', 'lightning' ),
				'section'  => 'lightning_design',
				'settings' => 'lightning_theme_options[color_key]',
				'priority' => 600,
			)
		)
	);

	$wp_customize->add_setting(
		'lightning_theme_options[color_key_dark]',
		array(
			'default'           => '#2e6da4',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'color_key_dark',
			array(
				'label'    => __( 'Key color (dark)', 'lightning' ),
				'section'  => 'lightning_design',
				'settings' => 'lightning_theme_options[color_key_dark]',
				'priority' => 600,
			)
		)
	);

	// Link Color Heading.
	$wp_customize->add_setting(
		'link_color_header',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new Custom_Html_Control(
			$wp_customize,
			'link_color_header',
			array(
				'label'            => '',
				'section'          => 'lightning_design',
				'type'             => 'text',
				'custom_title_sub' => __( 'Link text color', 'lightning' ),
				'custom_html'      => '',
				'priority'         => 600,
			)
		)
	);

	// Link Text Color ( Default ).
	$wp_customize->add_setting(
		'lightning_theme_options[link_text_color_default]',
		array(
			'default'           => '#337ab7',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'lightning_theme_options[link_text_color_default]',
			array(
				'label'    => __( 'Link text color ( default )', 'lightning' ),
				'section'  => 'lightning_design',
				'settings' => 'lightning_theme_options[link_text_color_default]',
				'priority' => 600,
			)
		)
	);

	// Link Text Color ( Hover ).
	$wp_customize->add_setting(
		'lightning_theme_options[link_text_color_hover]',
		array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'lightning_theme_options[link_text_color_hover]',
			array(
				'label'    => __( 'Link text color ( hover )', 'lightning' ),
				'section'  => 'lightning_design',
				'settings' => 'lightning_theme_options[link_text_color_hover]',
				'priority' => 600,
			)
		)
	);

	// Link Text Color ( Visited ).
	// $wp_customize->add_setting(
	// 	'lightning_theme_options[link_text_color_visited]',
	// 	array(
	// 		'default'           => '',
	// 		'type'              => 'option',
	// 		'capability'        => 'edit_theme_options',
	// 		'sanitize_callback' => 'sanitize_hex_color',
	// 	)
	// );
	// $wp_customize->add_control(
	// 	new WP_Customize_Color_Control(
	// 		$wp_customize,
	// 		'lightning_theme_options[link_text_color_visited]',
	// 		array(
	// 			'label'    => __( 'Link text color ( visited )', 'lightning' ),
	// 			'section'  => 'lightning_design',
	// 			'settings' => 'lightning_theme_options[link_text_color_visited]',
	// 			'priority' => 600,
	// 		)
	// 	)
	// );

	/*
	Unuse judgement
	} // if ( apply_filters( 'lightning_show_default_keycolor_customizer', true ) ) {
	*/

	/*
	  Layout
	/*-------------------------------------------*/
	$wp_customize->add_setting(
		'layout',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new Custom_Html_Control(
			$wp_customize,
			'layout',
			array(
				'label'            => __( 'Layout Setting', 'lightning' ),
				'section'          => 'lightning_design',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '<p>' . __( 'This setting field was moved to the "Layout Setting" panel.', 'lightning' ) . '</p>',
				'priority'         => 700,
			)
		)
	);

	/*
	  Other Setting
	/*-------------------------------------------*/
	$wp_customize->add_setting(
		'Others',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new Custom_Html_Control(
			$wp_customize,
			'Others',
			array(
				'label'            => __( 'Other Setting', 'lightning' ),
				'section'          => 'lightning_design',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '',
				'priority'         => 800,
			)
		)
	);

	$wp_customize->selective_refresh->add_partial(
		'lightning_theme_options[top_sidebar_hidden]',
		array(
			'selector'        => '.home .mainSection',
			'render_callback' => '',
		)
	);

	// top_default_content_hidden
	$wp_customize->add_setting(
		'lightning_theme_options[top_default_content_hidden]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[top_default_content_hidden]',
		array(
			'label'    => __( 'Don\'t show default content(Post list or Front page) at home page', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[top_default_content_hidden]',
			'type'     => 'checkbox',
			'priority' => 800,
		)
	);

	// postUpdate_hidden
	$wp_customize->add_setting(
		'lightning_theme_options[postUpdate_hidden]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[postUpdate_hidden]',
		array(
			'label'    => __( 'Hide modified date on single pages.', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[postUpdate_hidden]',
			'type'     => 'checkbox',
			'priority' => 800,
		)
	);

	// postAuthor_hidden
	$wp_customize->add_setting(
		'lightning_theme_options[postAuthor_hidden]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[postAuthor_hidden]',
		array(
			'label'    => __( 'Don\'t display post author on a single page', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[postAuthor_hidden]',
			'type'     => 'checkbox',
			'priority' => 800,
		)
	);

	// sidebar_child_list_hidden
	$wp_customize->add_setting(
		'lightning_theme_options[sidebar_child_list_hidden]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[sidebar_child_list_hidden]',
		array(
			'label'    => __( 'Don\'t display grandchild page of deactive page at page sidebar.', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[sidebar_child_list_hidden]',
			'type'     => 'checkbox',
			'priority' => 800,
		)
	);

}

function lightning_default_comment_options() {
	$comment_options = array(
		'hide_comment' => array(
			'post' => false,
			'page' => true,
		),
	);
	return $comment_options;
}

/*
  Lightning custom color Print head
  * This is used for Contents and Plugins and others
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_output_keycolor_css' );

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
	// wp_add_inline_style() is not stable on change enquepoint system.
	echo '<style id="lightning-color-custom-for-plugins" type="text/css">' . $dynamic_css . '</style>';
}

/*
  Print head
/*-------------------------------------------*/
add_action( 'wp_enqueue_scripts', 'lightning_print_css_common', 20 );

function lightning_print_css_common() {
	$options     	= get_option( 'lightning_theme_options' );
	$skin_info   	= Lightning_Design_Manager::get_current_skin();
	$dynamic_css 	= '';

	$colors 		= lightning_get_colors();
	$color_key		= $colors['color_key'];
	$color_key_dark = $colors['color_key_dark'];

	if ( ! empty( $options['color_key'] ) || ! empty( $options['color_key_dark'] ) ) {

		$dynamic_css   .= '/* ltg common custom */
		:root {
			--vk-menu-acc-btn-border-color:#333;
			--color-key:' . $color_key . ';
			--color-key-dark:' . $color_key_dark . ';
		}
		.bbp-submit-wrapper .button.submit { background-color:' . $color_key_dark . ' ; }
		.bbp-submit-wrapper .button.submit:hover { background-color:' . $color_key . ' ; }
		.veu_color_txt_key { color:' . $color_key_dark . ' ; }
		.veu_color_bg_key { background-color:' . $color_key_dark . ' ; }
		.veu_color_border_key { border-color:' . $color_key_dark . ' ; }
		.btn-default { border-color:' . $color_key . ';color:' . $color_key . ';}
		.btn-default:focus,
		.btn-default:hover { border-color:' . $color_key . ';background-color: ' . $color_key . '; }
		.btn-primary { background-color:' . $color_key . ';border-color:' . $color_key_dark . '; }
		.btn-primary:focus,
		.btn-primary:hover { background-color:' . $color_key_dark . ';border-color:' . $color_key . '; }
		.btn-outline-primary { color : ' . $color_key  . ' ; border-color:' . $color_key .'; }
		.btn-outline-primary:hover { color : #fff; background-color:' . $color_key .';border-color:' . $color_key_dark  .'; }
		';
	} // if ( isset($options['color_key'] && isset($options['color_key_dark'] ) {

	$link_text_color_default = ! empty( $options['link_text_color_default'] ) ? $options['link_text_color_default'] : '#337ab7';
	$link_text_color_hover   = ! empty( $options['link_text_color_hover'] ) ? $options['link_text_color_hover'] : '';
	// $link_text_color_visited = ! empty( $options['link_text_color_visited'] ) ? $options['link_text_color_visited'] : '';

	$dynamic_css .= 'a { color:' . $link_text_color_default . '; }';
	if ( ! empty( $link_text_color_hover ) ) {
		$dynamic_css .= 'a:hover { color:' . $link_text_color_hover . '; }';
	}
	// if ( ! empty( $link_text_color_visited ) ) {
	// 	$dynamic_css .= 'a:visited { color:' . $link_text_color_visited . '; }';
	// }

	/*
	  Child list hidden
	/*-------------------------------------------*/
	if ( isset( $options['sidebar_child_list_hidden'] ) && $options['sidebar_child_list_hidden'] ) {
		$dynamic_css .= '/* sidebar child menu display */
		.localNav ul ul.children	{ display:none; }
		.localNav ul li.current_page_ancestor ul.children,
		.localNav ul li.current_page_item ul.children,
		.localNav ul li.current-cat ul.children{ display:block; }';
	}

	if ( empty( $skin_info['bootstrap'] ) ) {
		if ( isset( $options['sidebar_position'] ) && $options['sidebar_position'] === 'left' ) {
			$dynamic_css .= '@media (min-width: 992px) { .siteContent .subSection { float:left;margin-left:0; } .siteContent .mainSection { float:right; } }';
		}
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

/*
  add body class
/*-------------------------------------------*/
add_filter( 'body_class', 'ltg_add_body_class_sidefix' );
function ltg_add_body_class_sidefix( $class ) {
	$options = get_option( 'lightning_theme_options' );
	if ( ! lightning_is_layout_onecolumn() ) {
		if ( isset( $options['sidebar_fix'] ) ) {
			if ( $options['sidebar_fix'] == 'priority-top' ) {
				$class[] = 'sidebar-fix';
				$class[] = 'sidebar-fix-priority-top';
			} elseif ( $options['sidebar_fix'] == 'priority-bottom' ) {
				$class[] = 'sidebar-fix';
				$class[] = 'sidebar-fix-priority-bottom';
			} elseif ( $options['sidebar_fix'] === 'no-fix' || $options['sidebar_fix'] === true ) {
				return $class;
			}
		} else {
			$class[] = 'sidebar-fix';
			$class[] = 'sidebar-fix-priority-top';
		}
	}
	return $class;
}

add_filter( 'body_class', 'ltg_add_body_class_bootstrap_version' );
function ltg_add_body_class_bootstrap_version( $class ) {
	global $bootstrap;
	if ( isset( $bootstrap ) && $bootstrap == '4' ) {
			$class[] = 'bootstrap4';
	}
	return $class;
}

/**
 * Lightning common dynamic css
 *
 * @return string
 */
function lightning_get_common_inline_css() {
	$options        = get_option( 'lightning_theme_options' );
	$color_key      = ( ! empty( $options['color_key'] ) ) ? esc_html( $options['color_key'] ) : '#337ab7';
	$color_key_dark = ( ! empty( $options['color_key_dark'] ) ) ? esc_html( $options['color_key_dark'] ) : '#2e6da4';
	$dynamic_css    = '
	:root {
		--color-key:' . $color_key . ';
		--color-key-dark:' . $color_key_dark . ';
	}
	';
	// delete before after space
	$dynamic_css = trim( $dynamic_css );
	// convert tab and br to space
	$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
	// Change multiple spaces to single space
	$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
	return $dynamic_css;
}

function lightning_add_common_dynamic_css() {
	$dynamic_css = lightning_get_common_inline_css();
	wp_add_inline_style( 'lightning-design-style', $dynamic_css );
}
add_action( 'wp_enqueue_scripts', 'lightning_add_common_dynamic_css' );

function lightning_add_common_dynamic_css_to_editor() {
	$dynamic_css = lightning_get_common_inline_css();
	wp_add_inline_style( 'lightning-common-editor-gutenberg', $dynamic_css );
}
add_action( 'enqueue_block_editor_assets', 'lightning_add_common_dynamic_css_to_editor' );

/*
  編集ショートカットボタンの位置調整（ウィジェットのショートカットボタンと重なってしまうため）
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_customize_preview_css_design', 2 );
function lightning_customize_preview_css_design() {
	if ( is_customize_preview() ) {
		$custom_css = '.sideSection > .customize-partial-edit-shortcut-lightning_theme_options-sidebar_fix { left:0px; }';
		wp_add_inline_style( 'lightning-design-style', $custom_css );
	}
}
