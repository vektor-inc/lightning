<?php
/*
  customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_woo' );
function lightning_customize_register_woo( $wp_customize ) {



	
	$wp_customize->add_setting(
		'lightning_woo_options[image_border_archive]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_woo_options[image_border_archive]',
		array(
			'label'    => __( 'Add border to item image on item list section', 'lightning' ),
			'section'  => 'woocommerce_product_images',
			'settings' => 'lightning_woo_options[image_border]',
			'type'     => 'checkbox',
			'priority' => 800,
		)
	);

}