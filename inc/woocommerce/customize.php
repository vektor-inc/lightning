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

/*
  add body class
/*-------------------------------------------*/
add_filter( 'body_class', 'lightning_add_body_class_woo_image_border' );
function lightning_add_body_class_woo_image_border( $class ) {

	$options = get_option( 'lightning_theme_options' );
	if ( ! lightning_is_layout_onecolumn() ) {
		if ( ! isset( $options['sidebar_fix'] ) || ! $options['sidebar_fix'] ) {
			if ( apply_filters( 'lightning_sidefix_enable', true ) ) {
				$class[] = 'sidebar-fix';
			}
		}
	}
	return $class;
}