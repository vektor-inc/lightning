<?php
/*
  customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_basic' );
function lightning_customize_register_basic( $wp_customize ) {

	$wp_customize->add_section(
		'lightning_basic',
		array(
			'title'    => lightning_get_prefix_customize_panel() . __( 'Basic settings', 'lightning' ),
			'priority' => 500,
		)
	);

	$wp_customize->add_setting(
		'generation_title',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize,
			'generation_title',
			array(
				'label'            => __( 'Generation Setting', 'lightning' ),
				'section'          => 'lightning_basic',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '<p><span style="color:red;font-weight:bold;">' . __( 'Switch of generations is nearly switch of theme.', 'lightning' ) . '</span></p>' . '<p>' . __( 'Be sure to make a backup before switch of generation as it is not very compatible.', 'lightning') . '</p>',
			)
		)
	);

	$choices = array(
		'g2'     => __( 'Generation 2', 'lightning' ),
		'g3'     => __( 'Generation 3 (Beta)', 'lightning' ),
	);

	$wp_customize->add_setting(
		'lightning_theme_generation',
		array(
			'default'           => __( 'Current Generation', 'lightning' ),
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
		)
	);

	$wp_customize->add_control(
		'lightning_theme_generation',
		array(
			'label'    => '',
			'section'  => 'lightning_basic',
			'settings' => 'lightning_theme_generation',
			'type'     => 'select',
			'choices'  => $choices,
		)
	);

	$wp_customize->add_setting(
		'generation_reload_btn',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new VK_Custom_Html_Control(
			$wp_customize,
			'generation_reload_btn',
			array(
				'label'            => '',
				'section'          => 'lightning_basic',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => '<p>' . __( 'After switching generations, save and reload the page.', 'lightning' ) . '</p><a href="' . $_SERVER["REQUEST_URI"] . '" class="button button-primary button-block">再読み込み</a>',
				// 'active_callback' => 'lightning_generation_reload_callback',
			)
		)
	);

}
