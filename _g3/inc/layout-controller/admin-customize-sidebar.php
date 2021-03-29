<?php

/*
  customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_sidebar' );
function lightning_customize_register_sidebar( $wp_customize ) {
	/*--------------------------------------*/
	/*	Sidebar Setting
	/*--------------------------------------*/
	$wp_customize->add_section(
		'lightning_sidebar',
		array(
			'title'    	=> __( 'Sidebar Setting', 'lightning' ),
			'panel'		=> 'lightning_layout',
		)
	);
	$wp_customize->add_setting(
		'ltg_sidebar_setting',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize,
			'ltg_sidebar_setting',
			array(
				'label'            => __( 'Sidebar Setting', 'lightning' ),
				'section'          => 'lightning_sidebar',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '',
				// 'priority'         => 700,
			)
		)
	);

	// sidebar_position
	$wp_customize->add_setting(
		'lightning_theme_options[sidebar_position]',
		array(
			'default'           => 'right',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => array( 'VK_Helpers', 'sanitize_choice' ),
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[sidebar_position]',
		array(
			'label'    => __( 'Sidebar position ( PC mode )', 'lightning' ),
			'section'  => 'lightning_sidebar',
			'settings' => 'lightning_theme_options[sidebar_position]',
			'type'     => 'radio',
			'choices'  => array(
				'right' => __( 'Right', 'lightning' ),
				'left'  => __( 'Left', 'lightning' ),
			),
		)
	);

	// sidebar_fix
	$wp_customize->add_setting(
		'ltg_sidebar_fix_setting_title',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize,
			'ltg_sidebar_fix_setting_title',
			array(
				'label'            => '',
				'section'          => 'lightning_sidebar',
				'type'             => 'text',
				'custom_title_sub' => __( 'Sidebar fix', 'lightning' ),
				'custom_html'      => '',
			)
		)
	);
	$wp_customize->add_setting(
		'lightning_theme_options[sidebar_fix]',
		array(
			'default'           => 'priority-top',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[sidebar_fix]',
		array(
			'label'    => __( 'Sidebar fix setting', 'lightning' ),
			'section'  => 'lightning_sidebar',
			'settings' => 'lightning_theme_options[sidebar_fix]',
			'type'     => 'select',
			'choices'  => array(
				'priority-top'		=> __( 'Fix top priority', 'lightning' ),
				'priority-bottom'	=> __( 'Fix bottom priority', 'lightning' ),
				'no-fix' 			=> __( 'Don\'t fix the sidebar', 'lightning' ),
			)
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'lightning_theme_options[sidebar_fix]',
		array(
			'selector'        => '.sub-section',
			'render_callback' => '',
		)
	);
}