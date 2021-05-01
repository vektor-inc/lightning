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
			'selector'        => '.site-header-logo',
			'render_callback' => '',
		)
	);

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
		new VK_Custom_Html_Control(
			$wp_customize,
			'color_header',
			array(
				'label'       => __( 'Color Setting', 'lightning' ),
				'section'     => 'lightning_design',
				'type'        => 'text',
				// 'custom_title_sub' => __( 'Key Color', 'lightning' ),
				'custom_html' => '',
				'priority'    => 600,
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
}

/**
 * Lightning common dynamic css
 *
 * @return string
 */
function lightning_get_common_inline_css() {
	$options        = lightning_get_theme_options();
	$color_key      = ! empty( $options['color_key'] ) ? esc_html( $options['color_key'] ) : '#337ab7';
	$color_key_dark = VK_Helpers::color_auto_modifi( $color_key, 0.8 );
	$dynamic_css    = '
	/* Lightning */
	:root {
		--vk-color-primary:' . $color_key . ';
		--vk-color-primary-dark:' . $color_key_dark . ';
		--g_nav_main_acc_icon_open_url:url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-acc-icon-open-black.svg);
		--g_nav_main_acc_icon_close_url: url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-close-black.svg);
		--g_nav_sub_acc_icon_open_url: url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-acc-icon-open-white.svg);
		--g_nav_sub_acc_icon_close_url: url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-close-white.svg);
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
	wp_add_inline_style( 'lightning-common-style', $dynamic_css );
}
add_action( 'wp_enqueue_scripts', 'lightning_add_common_dynamic_css', 11 );

function lightning_add_common_dynamic_css_to_editor() {
	$dynamic_css = lightning_get_common_inline_css();
	wp_add_inline_style( 'lightning-common-editor-gutenberg', $dynamic_css );
}
add_action( 'enqueue_block_editor_assets', 'lightning_add_common_dynamic_css_to_editor' );
