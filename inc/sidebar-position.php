<?php

/*-------------------------------------------*/
/*  Remove lightning-advanced-unit's function.
/*-------------------------------------------*/

$if_existed_in_plugins = array(
	'customize_register' => 'lightning_adv_unit_customize_register_sidebar_position',
	'wp_head' => 'lightning_adv_unit_sidebar_position_custom'
);

foreach ($if_existed_in_plugins as $key => $val){
	$priority = has_filter( $key, $val );
	if ( $priority ){
		remove_action( $key, $val, $priority);
	}
}

/*-------------------------------------------*/
/*  Customizer
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_sidebar_position' );
function lightning_customize_register_sidebar_position($wp_customize) {
	$wp_customize->add_setting( 'lightning_theme_options[sidebar_position]',  array(
		'default'           => 'right',
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_radio',
	));
	$wp_customize->add_control( 'lightning_theme_options[sidebar_position]',array(
		'label'     => __('Sidebar position ( PC mode )', 'lightning'),
		'section'   => 'lightning_design',
		'settings'  => 'lightning_theme_options[sidebar_position]',
		'type' => 'radio',
		'choices' => array(
			'right' => __('Right', 'lightning'),
			'left' => __('Left', 'lightning'),
		),
		'priority' => 610,
	));
}

/*-------------------------------------------*/
/*  Position Change
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_sidebar_position_custom', 2 );
function lightning_sidebar_position_custom(){
	$options = get_option( 'lightning_theme_options' );
	if ( isset($options['sidebar_position'] ) && $options['sidebar_position'] === "left" ){
		$custom_css = "@media (min-width: 992px) { .siteContent .subSection { float:left;margin-left:0; } .siteContent .mainSection { float:right; } }";
		wp_add_inline_style( 'lightning-design-style', $custom_css );
	}
}