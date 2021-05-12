<?php

/*-------------------------------------------*/
/*	Load Package
/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/

/*-------------------------------------------*/
/*	Load Package
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'Lightning_load_packages' );
function Lightning_load_packages() {
	$options  = get_option( 'lightning_theme_options' );
	$packages = lightning_old_packages_array();
	foreach ( $packages as $key => $value ) {
		if ( empty( $options['disable_functions'][ $key ] ) ) {
			require $value['path'];
		}
	}
}

function lightning_old_packages_array() {
	$packages = array(
		'widget_full_wide_title'     => array(
			'label'       => __( 'Full Wide Title Widget', 'lightning' ),
			'description' => __( 'If you are using Lightning Pro that, You can use the same function by Outer Block and Title Block in Plugin VK Blocks.', 'lightning' ),
			'path'        => dirname( __FILE__ )  . '/widgets/widget-full-wide-title.php',
		),
		'widget_contents_area_posts' => array(
			'label'       => __( 'Content Area Posts Widget', 'lightning' ),
			'description' => __( 'If you are using Lightning Pro that, You can use the more powerful function by Media Posts BS4 Widget and Latest Posts Block in Plugin VK Blocks.', 'lightning' ),
			'path'        => dirname( __FILE__ )  . '/widgets/widget-new-posts.php',
		),
		'widget_front_pr'            => array(
			'label'       => __( 'Front Page PR Block', 'lightning' ),
			'description' => __( 'You can use same function by PR Block in Plugin VK Blocks', 'lightning' ),
			'path'        => dirname( __FILE__ )  . '/front-page-pr.php',
		),
	);
	return apply_filters( 'lightning_old_packages_array', $packages );
}

/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_package' );
function lightning_customize_register_package( $wp_customize ) {

	$wp_customize->add_setting(
		'not_recommended_title', array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize, 'not_recommended_title', array(
				'label'            => __( 'Not recommended functions', 'lightning' ),
				'section'          => 'lightning_function',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '<p>' . __( 'These are old functions that already alternated to the new function.', 'lightning' ) . '</p>',
			)
		)
	);

	$functions = lightning_old_packages_array();

	foreach ( $functions as $key => $value ) {
		$wp_customize->add_setting(
			"lightning_theme_options[disable_functions][$key]", array(
				'default'           => false,
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => array( 'VK_Helpers', 'sanitize_checkbox' ),
			)
		);
		$wp_customize->add_control(
			"lightning_theme_options[disable_functions][$key]", array(
				'label'       => sprintf( __( 'Stop %s', 'lightning' ), $value['label'] ),
				'section'     => 'lightning_function',
				'settings'    => "lightning_theme_options[disable_functions][$key]",
				'type'        => 'checkbox',
				'description' => $value['description'],
				'priority'    => 700,
			)
		);
	}

}
