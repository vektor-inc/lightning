<?php
/*
  customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_enqueue_point' );
function lightning_customize_register_enqueue_point( $wp_customize ) {

	$wp_customize->add_setting(
		'speedinc_title',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new Custom_Html_Control(
			$wp_customize,
			'speedinc_title',
			array(
				'label'            => __( 'Speed setting', 'lightning' ),
				'section'          => 'lightning_function',
				'type'             => 'text',
				'custom_title_sub' => '',
				// 'custom_html'      => __( 'Move part of CSS and JS to the footer to improve display speed.', 'lightning' ),
			)
		)
	);

	$wp_customize->add_setting(
		'lightning_theme_options[optimize_css]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[optimize_css]',
		array(
			'label'       => __( 'Optimize CSS', 'lightning' ),
			'section'     => 'lightning_function',
			'settings'    => 'lightning_theme_options[optimize_css]',
			'type'        => 'checkbox',
			'description' => __( 'If you enabled this checkbox that the CSS will shrink or preload.', 'lightning' ),
		)
	);
}
