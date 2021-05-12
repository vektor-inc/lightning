<?php

/*
  customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_woo_customize_register' );
function lightning_woo_customize_register( $wp_customize ) {

	$wp_customize->add_setting(
		'ltg_woo_image_setting',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize,
			'ltg_woo_image_setting',
			array(
				'label'            => __( 'Border Setting', 'lightning' ) .' ('.lightning_get_theme_name().')',
				'section'          => 'woocommerce_product_images',
				'type'             => 'text',
				'custom_title_sub' => __( 'Add border to item image', 'lightning' ), 
				'custom_html'      => '',
				'priority'         => 600,
			)
		)
	);

	$wp_customize->add_setting(
		'lightning_woo_options[image_border_archive]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => array( 'VK_Helpers', 'sanitize_checkbox' ),
		)
	);
	$wp_customize->add_control(
		'lightning_woo_options[image_border_archive]',
		array(
			'label'    => __( 'Item list section', 'lightning' ),
			'section'  => 'woocommerce_product_images',
			'settings' => 'lightning_woo_options[image_border_archive]',
			'type'     => 'checkbox',
			'priority' => 800,
		)
	);
	$wp_customize->add_setting(
		'lightning_woo_options[image_border_single]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => array( 'VK_Helpers', 'sanitize_checkbox' ),
		)
	);
	$wp_customize->add_control(
		'lightning_woo_options[image_border_single]',
		array(
			'label'    => __( 'Single page', 'lightning' ),
			'section'  => 'woocommerce_product_images',
			'settings' => 'lightning_woo_options[image_border_single]',
			'type'     => 'checkbox',
			'priority' => 800,
		)
	);
}


function lightning_woo_add_common_dynamic_css() {
	$dynamic_css = '';
	$options = get_option('lightning_woo_options');
	if ( ! empty( $options['image_border_archive'] ) ){
		$dynamic_css .= '.woocommerce ul.products li.product a img {
			border:1px solid var( --color-woo-image-border );
		}';
	}
	if ( ! empty( $options['image_border_single'] ) ){
		$dynamic_css .= '.woocommerce .woocommerce-product-gallery__image {
			border:1px solid var( --color-woo-image-border );
		}';
	}
	// delete before after space
	$dynamic_css = trim( $dynamic_css );
	// convert tab and br to space
	$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
	// Change multiple spaces to single space
	$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
	wp_add_inline_style( 'lightning-design-style', $dynamic_css );
}
add_action( 'wp_enqueue_scripts', 'lightning_woo_add_common_dynamic_css' );