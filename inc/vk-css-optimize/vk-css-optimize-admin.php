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
			'default'           => 'minimal-bootstrap',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_radio',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[optimize_css]',
		array(
			'label'       => __( 'Optimize CSS', 'lightning' ),
			'section'     => 'lightning_function',
			'settings'    => 'lightning_theme_options[optimize_css]',
			'type'        => 'select',
			'choices'     => array(
				'full-bootstrap'    => __( 'Nothing to do ( Full Bootstrap )', 'lightning' ),
				'minimal-bootstrap' => __( 'Load minimal Bootstrap ( only for BS4 )', 'lightning' ),
				'tree-shaking'      => __( 'Optimize All CSS ( Tree Shaking ) (Beta)', 'lightning' ),
				// 'optomize-all-css'  => __( 'Optimize All CSS ( Tree Shaking + Preload ) (Beta)', 'lightning' ),
			),
		)
	);

	$wp_customize->add_setting(
		'lightning_theme_options[tree_shaking_class_exclude]',
		array(
			'default'           => '',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[tree_shaking_class_exclude]',
		array(
			'label'       => __( 'Exclude class of tree shaking', 'lightning' ),
			'section'     => 'lightning_function',
			'settings'    => 'lightning_theme_options[tree_shaking_class_exclude]',
			'type'        => 'textarea',
			'description' => __( 'If you choose "Optimize All CSS" that delete the useless css.If you using active css class that please fill in class name. Ex) btn-active,slide-active,scrolled', 'lightning' ),
		)
	);
}
