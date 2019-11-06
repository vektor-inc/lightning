<?php

/*-------------------------------------------*/
/*	Load Package
/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/

/*-------------------------------------------*/
/*	Load Package
/*-------------------------------------------*/
$options = get_option( 'lightning_theme_options' );

if ( ! empty( $options['disable_functions'] ) && is_array( $options['disable_functions'] ) ) {
	$functions = lightning_package_array();
	foreach ( $options['disable_functions'] as $key => $value ) {
		if ( ! $value ) {
			$value[''];
			require $functions[ $key ]['path'];
		}
	}
}

function lightning_package_array() {
	$packages = array(
		// 'woocommerce'                => array(
		// 	'label'       => __( 'WooCommerce', 'lightning' ),
		// 	'description' => __( '', 'lightning' ),
		// ),
		'widget_full_wide_title'     => array(
			'label'       => __( 'Full Wide Title Widget', 'lightning' ),
			'description' => __( '', 'lightning' ),
			'path'        => get_parent_theme_file_path( '/inc/widgets/widget-full-wide-title.php' ),
		),
		'widget_contents_area_posts' => array(
			'label'       => __( 'Content Area Posts Widget', 'lightning' ),
			'description' => __( '', 'lightning' ),
			'path'        => get_parent_theme_file_path( '/inc/widgets/widget-new-posts.php' ),
		),
		'widget_front_pr'            => array(
			'label'       => __( 'Content Area Posts Widget', 'lightning' ),
			'description' => __( 'You can use same function by Plugin VK Blocks', 'lightning' ),
			'path'        => get_parent_theme_file_path( '/inc/front-page-pr.php' ),
		),
	);
	return apply_filters( 'lightning', $packages );
}

/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_function' );
function lightning_customize_register_function( $wp_customize ) {

	$wp_customize->add_section(
		'lightning_function', array(
			'title'    => lightning_get_prefix_customize_panel() . __( 'Function Settings', 'lightning' ),
			'priority' => 500,
		)
	);

	$functions = lightning_package_array();

	foreach ( $functions as $key => $value ) {
		$wp_customize->add_setting(
			"lightning_theme_options[disable_functions][$key]", array(
				'default'           => false,
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'lightning_sanitize_checkbox',
			)
		);
		$wp_customize->add_control(
			"lightning_theme_options[disable_functions][$key]", array(
				'label'       => $value['label'],
				'section'     => 'lightning_function',
				'settings'    => "lightning_theme_options[disable_functions][$key]",
				'type'        => 'checkbox',
				'description' => $value['description'],
				'priority'    => 700,
			)
		);
	}

}
