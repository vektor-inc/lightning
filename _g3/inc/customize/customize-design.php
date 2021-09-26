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
		)
	);

	$wp_customize->add_setting(
		'logo_header',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize,
			'logo_header',
			array(
				'label'            => __( 'Header logo image', 'lightning' ),
				'section'          => 'lightning_design',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => __( 'Recommended image size : 500*120px', 'lightning' ),
				'priority'         => 501,
			)
		)
	);

	// head logo.
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
				'label'       => '',
				'section'     => 'lightning_design',
				'settings'    => 'lightning_theme_options[head_logo]',
				'priority'    => 501,
				'description' => '',
			)
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'lightning_theme_options[head_logo]',
		array(
			'selector'        => '.site-header-logo',
			'render_callback' => '',
		)
	);

	/*********************************************
	 * Color Setting
	 */
	$wp_customize->add_setting(
		'lightning_design[color_header]',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize,
			'lightning_design[color_header]',
			array(
				'label'       => __( 'Color Setting', 'lightning' ),
				'section'     => 'lightning_design',
				'type'        => 'text',
				'custom_html' => __(
					'Color settings can be made from the Appearance > Customize > Color panel.',
					'lightning'
				),
				'priority'    => 600,
			)
		)
	);

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
				'section'  => 'colors',
				'settings' => 'lightning_theme_options[color_key]',
				'priority' => 500,
			)
		)
	);

}

/**
 * Lightning common dynamic css
 *
 * @return string
 */
function lightning_get_common_inline_css() {
	$options         = lightning_get_theme_options();
	$color_key       = ! empty( $options['color_key'] ) ? esc_html( $options['color_key'] ) : '#337ab7';
	$vk_helpers      = new VK_Helpers();
	$color_key_dark  = $vk_helpers->color_auto_modifi( $color_key, 0.8 );
	$color_key_vivid = $vk_helpers->color_auto_modifi( $color_key, 1.1 );
	$dynamic_css     = '
	/* Lightning */
	:root {
		--vk-color-primary:' . $color_key . ';
		--vk-color-primary-dark:' . $color_key_dark . ';
		--vk-color-primary-vivid:' . $color_key_vivid . ';
		--g_nav_main_acc_icon_open_url:url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-acc-icon-open-black.svg);
		--g_nav_main_acc_icon_close_url: url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-close-black.svg);
		--g_nav_sub_acc_icon_open_url: url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-acc-icon-open-white.svg);
		--g_nav_sub_acc_icon_close_url: url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-close-white.svg);
	}
	';
	// delete before after space.
	$dynamic_css = trim( $dynamic_css );
	// convert tab and br to space.
	$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
	// Change multiple spaces to single space.
	$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
	return $dynamic_css;
}

function lightning_add_common_dynamic_css() {
	$dynamic_css = lightning_get_common_inline_css();
	wp_add_inline_style( 'lightning-common-style', $dynamic_css );
}
add_action( 'wp_enqueue_scripts', 'lightning_add_common_dynamic_css', 11 );

function lightning_add_common_dynamic_css_to_editor() {
	$dynamic_css = lightning_get_common_inline_css();
	wp_add_inline_style( 'lightning-common-editor-gutenberg', $dynamic_css, 11 );
}
// 11 指定が無いと先に読み込んでしまって効かない
add_action( 'enqueue_block_editor_assets', 'lightning_add_common_dynamic_css_to_editor', 11 );
