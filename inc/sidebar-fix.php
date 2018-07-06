<?php

/*-------------------------------------------*/
/*  Remove lightning-advanced-unit's function.
/*-------------------------------------------*/

$if_existed_in_plugins = array(
	'customize_register' => 'lightning_unit_customize_register_sidebar_fix',
	'wp_head' => 'lightning_unit_sidefix_admin_css',
	'body_class' => 'ltg_adv_add_body_class_sidefix',
);

foreach ($if_existed_in_plugins as $key => $val){
	$priority = has_filter( $key, $val );
	var_dump($priority);
	var_dump("hello");
	if ( $priority ){
		remove_action( $key, $val, $priority);
	}
}

/*-------------------------------------------*/
/*  Customizer
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_unit_customize_register_sidebar_fix' );
function lightning_unit_customize_register_sidebar_fix($wp_customize) {
	$wp_customize->add_setting( 'lightning_theme_options[sidebar_fix]',  array(
		'default'           => false,
		'type'              => 'option',
		'capability'        => 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	));
	$wp_customize->add_control( 'lightning_theme_options[sidebar_fix]',array(
		'label'     => __('Don\'t fix the sidebar', LIGHTNING_ADVANCED_TEXTDOMAIN),
		'section'   => 'lightning_design',
		'settings'  => 'lightning_theme_options[sidebar_fix]',
		'type' 		=> 'checkbox',
		'priority' => 555,
	));
	$wp_customize->selective_refresh->add_partial( 'lightning_theme_options[sidebar_fix]', array(
		'selector' => '.sideSection',
		'render_callback' => '',
	) );
}

/*-------------------------------------------*/
/*	add body class
/*-------------------------------------------*/
add_filter( 'body_class', 'ltg_adv_add_body_class_sidefix' );
function ltg_adv_add_body_class_sidefix( $class ){
	$options = get_option('lightning_theme_options');
	if ( !isset( $options['sidebar_fix'] ) || !$options['sidebar_fix'] ) {
		if( apply_filters( 'lightning_sidefix_enable', true ) ) {
			$class[] = 'sidebar-fix';
		}
	}
	return $class;
}

/*-------------------------------------------*/
/*  編集ショートカットボタンの位置調整（ウィジェットのショートカットボタンと重なってしまうため）
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_unit_sidefix_admin_css', 2 );
function lightning_unit_sidefix_admin_css(){
	if ( is_customize_preview() ){
		$custom_css = ".sideSection > .customize-partial-edit-shortcut-lightning_theme_options-sidebar_fix { left:0px; }";
		wp_add_inline_style( 'lightning-design-style', $custom_css );
	}
}

/*-------------------------------------------*/
/*  Position Change
/*-------------------------------------------*/
// add_action( 'wp_head', 'lightning_unit_sidebar_fix_css', 2 );
// function lightning_unit_sidebar_fix_css(){
// 	$options = get_option( 'lightning_theme_options' );
// 	if ( isset($options['sidebar_fix'] ) && $options['sidebar_fix'] ){
// 		$custom_css = "/*-------------------------------------------*/
// /*	sidebar child menu display
// /*-------------------------------------------*/
// .localNav ul ul.children	{ display:none; }
// .localNav ul li.current_page_ancestor	ul.children,
// .localNav ul li.current_page_item		ul.children,
// .localNav ul li.current-cat				ul.children{ display:block; }";
// 		wp_add_inline_style( 'lightning-design-style', $custom_css );
// 	}
// }
