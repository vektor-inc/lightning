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

	/*
	  Comment Setting
	/*-------------------------------------------*/
	$wp_customize->add_setting(
		'comment_header',
		array(
			'sanitize_callback' => 'sanitize_text_field',
		)
	);
	$wp_customize->add_control(
		new Custom_Html_Control(
			$wp_customize,
			'comment_header',
			array(
				'label'            => __( 'Comment Setting', 'lightning' ),
				'section'          => 'lightning_function',
				'type'             => 'text',
				'custom_html'      => '',
				'priority'         => 801,
			)
		)
	);

	// hide_comment.
	$wp_customize->add_setting(
		'lightning_theme_options[hide_comment][post]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[hide_comment][post]',
		array(
			'label'    => __( 'Hide cooment template on post.', 'lightning' ),
			'section'  => 'lightning_function',
			'settings' => 'lightning_theme_options[hide_comment][post]',
			'type'     => 'checkbox',
			'priority' => 801,
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'lightning_theme_options[hide_comment][post]',
		array(
			'selector'        => '.comments-area',
			'render_callback' => '',
		)
	);

	// page_hide_comment
	$wp_customize->add_setting(
		'lightning_theme_options[hide_comment][page]',
		array(
			'default'           => true,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);
	$wp_customize->add_control(
		'lightning_theme_options[hide_comment][page]',
		array(
			'label'    => __( 'Hide cooment template on page.', 'lightning' ),
			'section'  => 'lightning_function',
			'settings' => 'lightning_theme_options[hide_comment][page]',
			'type'     => 'checkbox',
			'priority' => 801,
		)
	);
	$wp_customize->selective_refresh->add_partial(
		'lightning_theme_options[hide_comment][page]',
		array(
			'selector'        => '.comments-area',
			'render_callback' => '',
		)
	);

	$coment_post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'object'
	);

	foreach ( $coment_post_types as $coment_post_type ) {

		if ( post_type_supports( $coment_post_type->name, 'comments' ) ) {
			// page_hide_comment
			$wp_customize->add_setting(
				'lightning_theme_options[hide_comment][' . $coment_post_type->name . ']',
				array(
					'default'           => false,
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'lightning_sanitize_checkbox',
				)
			);
			$wp_customize->add_control(
				'lightning_theme_options[hide_comment][' . $coment_post_type->name . ']',
				array(
					'label'    => sprintf( __( 'Hide cooment template on %s.', 'lightning' ), $coment_post_type->label ),
					'section'  => 'lightning_function',
					'settings' => 'lightning_theme_options[hide_comment][' . $coment_post_type->name . ']',
					'type'     => 'checkbox',
					'priority' => 801,
				)
			);
			$wp_customize->selective_refresh->add_partial(
				'lightning_theme_options[hide_comment][' . $coment_post_type->name . ']',
				array(
					'selector'        => '.comments-area',
					'render_callback' => '',
				)
			);
		}

	}

}