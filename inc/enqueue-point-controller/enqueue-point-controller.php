<?php
/*
  customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_enqueue_point' );
function lightning_customize_register_enqueue_point( $wp_customize ) {

	$wp_customize->add_setting(
		'enqueue_point_title',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new Custom_Html_Control(
			$wp_customize,
			'enqueue_point_title',
			array(
				'label'            => __( 'CSS and JavaScript load point ', 'lightning' ),
				'section'          => 'lightning_function',
				'type'             => 'text',
				'custom_title_sub' => '',
				'custom_html'      => __( 'Move part of CSS and JS to the footer to improve display speed.', 'lightning' ),
			)
		)
	);

	$wp_customize->add_setting(
		'lightning_theme_options[enqueue_point_footer]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[enqueue_point_footer]',
		array(
			'label'       => __( 'Move to footer', 'lightning' ),
			'section'     => 'lightning_function',
			'settings'    => 'lightning_theme_options[enqueue_point_footer]',
			'type'        => 'checkbox',
			'description' => __( 'If you enabled this checkbox that page loading speed become little faster.', 'lightning' ),
		)
	);

}

function lightning_is_speeding(){

	$return = false;
	$options = get_option( 'lightning_theme_options' );

	if ( ! empty( $options['enqueue_point_footer'] ) ) {
		$return = true;
	}

	return $return;
}

/**
 * Change load point
 */

 add_action( 'after_setup_theme', 'lightning_change_enqueue_point_run_filter', 5 );
function lightning_change_enqueue_point_run_filter() {

	$options = get_option( 'lightning_theme_options' );

	if ( ! empty( $options['enqueue_point_footer'] ) ) {

		// Theme late load css ( ex : overwrite ExUnit common CSS )
		add_filter( 'lightning_late_load_style_enqueue_point', 'lightning_change_enqueue_point_to_footer' );

		// Theme common and theme-style
		add_filter( 'lightning_enqueue_point_common_and_theme_css', 'lightning_change_enqueue_point_to_footer' );

		// font awesome
		add_filter( 'vkfa_enqueue_point', 'lightning_change_enqueue_point_to_footer' );

		// Mobile fix nav ( Mobile Fix Nav css is included in assets/common.scss (Pro version) )
		// add_filter( 'vk_mobile_fix_nav_enqueue_point', 'lightning_change_enqueue_point_to_footer');

		// vk blocks css
		add_filter( 'vkblocks_enqueue_point', 'lightning_change_enqueue_point_to_footer' );

	}

}

function lightning_change_enqueue_point_to_footer( $enqueue_point ) {
	$enqueue_point = 'wp_footer';
	return $enqueue_point;
}
