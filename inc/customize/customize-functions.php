<?php
/*
  customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_function' );
function lightning_customize_register_function( $wp_customize ) {
	$wp_customize->add_section(
		'lightning_function', array(
			'title'    => lightning_get_prefix_customize_panel() . __( 'Function Settings', 'lightning' ),
			'priority' => 450,
		)
	);
}