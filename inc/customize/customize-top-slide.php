<?php

/*
  top slide
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register_top_slide' );
function lightning_customize_register_top_slide( $wp_customize ) {

	$wp_customize->add_section(
		'lightning_slide',
		array(
			'title'    => lightning_get_prefix_customize_panel() . __( 'Home page slide show', 'lightning' ),
			'priority' => 520,
		// 'panel'			=> 'lightning_setting',
		)
	);

	// Hide Slide.
	$wp_customize->add_setting(
		'lightning_theme_options[top_slide_hide]',
		array(
			'default'           => false,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		)
	);

	$wp_customize->add_control(
		'lightning_theme_options[top_slide_hide]',
		array(
			'label'    => __( 'Hide slide', 'lightning' ),
			'section'  => 'lightning_slide',
			'settings' => 'lightning_theme_options[top_slide_hide]',
			'type'     => 'checkbox',
		)
	);

	$skin_info = Lightning_Design_Manager::get_current_skin();
	if ( isset( $skin_info['bootstrap'] ) && $skin_info['bootstrap'] == 'bs4' ) {
		// Slide Effect
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_effect]',
			array(
				'default'           => 'slide',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$wp_customize->add_control(
			'top_slide_effect',
			array(
				'label'       => __( 'Slide effect', 'lightning' ),
				'section'     => 'lightning_slide',
				'settings'    => 'lightning_theme_options[top_slide_effect]',
				'type'        => 'select',
				'choices'     => array(
					'slide' => 'slide',
					'fade'  => 'fade',
				),
				'priority'    => 604,
				'description' => '',
				'input_after' => '',
			)
		);
	}

	// Slide interval time
	$wp_customize->add_setting(
		'lightning_theme_options[top_slide_time]',
		array(
			'default'           => 4000,
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_number',
		)
	);

	$wp_customize->add_control(
		new Custom_Text_Control(
			$wp_customize,
			'top_slide_time',
			array(
				'label'       => __( 'Slide interval time', 'lightning' ),
				'section'     => 'lightning_slide',
				'settings'    => 'lightning_theme_options[top_slide_time]',
				'type'        => 'text',
				'priority'    => 605,
				'description' => '',
				'input_after' => __( 'millisecond', 'lightning' ),
			)
		)
	);

	// slide image
	$priority = 610;

	$theme_options_default = lightning_theme_options_default();
	$top_slide_count_max   = lightning_top_slide_count_max();

	for ( $i = 1; $i <= $top_slide_count_max; ) {

		$theme_options_customize_default['top_slide_image'] = '';
		switch ( $i ) {
			case 1:
				$theme_options_customize_default['top_slide_image'] = $theme_options_default['top_slide_image_1'];
				break;
			case 2:
				$theme_options_customize_default['top_slide_image'] = $theme_options_default['top_slide_image_2'];
				break;
		}

		// slide_title
		$wp_customize->add_setting(
			'slide_title_' . $i,
			array(
				'sanitize_callback' => 'sanitize_text_field',
			)
		);
		$priority = $priority + 1;
		$wp_customize->add_control(
			new Custom_Html_Control(
				$wp_customize,
				'slide_title_' . $i,
				array(
					'label'            => __( 'Slide', 'lightning' ) . ' [' . $i . ']',
					'section'          => 'lightning_slide',
					'type'             => 'text',
					'custom_title_sub' => '',
					'custom_html'      => '',
					'priority'         => $priority,
				)
			)
		);

		$wp_customize->selective_refresh->add_partial(
			'lightning_theme_options[top_slide_image_' . $i . ']',
			array(
				'selector'        => '.item-' . $i . ' picture',
				'render_callback' => '',
			)
		);

		// image
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_image_' . $i . ']',
			array(
				'default'           => $theme_options_customize_default['top_slide_image'],
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$priority = $priority + 1;
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'top_slide_image_' . $i,
				array(
					'label'       => '[' . $i . '] ' . __( 'Slide image', 'lightning' ),
					'section'     => 'lightning_slide',
					'settings'    => 'lightning_theme_options[top_slide_image_' . $i . ']',
					'priority'    => $priority,
					'description' => __( 'Recommended image size : 1900*600px', 'lightning' ),
				)
			)
		);

		// image mobile
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_image_mobile_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$priority = $priority + 1;
		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'top_slide_image_mobile_' . $i,
				array(
					'label'       => '[' . $i . '] ' . __( 'Slide image for mobile', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
					'section'     => 'lightning_slide',
					'settings'    => 'lightning_theme_options[top_slide_image_mobile_' . $i . ']',
					'priority'    => $priority,
					'description' => '',
				)
			)
		);

		// alt
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_alt_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$priority = $priority + 1;
		$wp_customize->add_control(
			new Custom_Text_Control(
				$wp_customize,
				'top_slide_alt_' . $i,
				array(
					'label'       => '[' . $i . '] ' . __( 'Slide image alt', 'lightning' ),
					'section'     => 'lightning_slide',
					'settings'    => 'lightning_theme_options[top_slide_alt_' . $i . ']',
					'type'        => 'text',
					'priority'    => $priority,
					'description' => __( 'This title text is print to alt tag.', 'lightning' ),
				)
			)
		);

		// color
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_cover_color_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$priority = $priority + 1;
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'top_slide_cover_color_' . $i . '',
				array(
					'label'    => '[' . $i . '] ' . __( 'Slide cover color', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
					'section'  => 'lightning_slide',
					'settings' => 'lightning_theme_options[top_slide_cover_color_' . $i . ']',
					'priority' => $priority,
				)
			)
		);

		// opacity
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_cover_opacity_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'lightning_sanitize_number_percentage',
			)
		);
		$priority = $priority + 1;
		$wp_customize->add_control(
			new Custom_Text_Control(
				$wp_customize,
				'top_slide_cover_opacity_' . $i,
				array(
					'label'       => '[' . $i . '] ' . __( 'Slide cover opacity', 'lightning' ),
					'section'     => 'lightning_slide',
					'settings'    => 'lightning_theme_options[top_slide_cover_opacity_' . $i . ']',
					'type'        => 'text',
					'priority'    => $priority,
					'description' => __( 'Please input 0 - 100 number', 'lightning' ),
					'input_after' => '%',
				)
			)
		);

			// url
			$wp_customize->add_setting(
				'lightning_theme_options[top_slide_url_' . $i . ']',
				array(
					'default'           => '',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'esc_url_raw',
				)
			);

			$priority = $priority + 1;
			$wp_customize->add_control(
				'top_slide_url_' . $i,
				array(
					'label'    => '[' . $i . '] ' . __( 'Slide image link url', 'lightning' ),
					'section'  => 'lightning_slide',
					'settings' => 'lightning_theme_options[top_slide_url_' . $i . ']',
					'type'     => 'text',
					'priority' => $priority,
				)
			);

			// link blank
			$wp_customize->add_setting(
				'lightning_theme_options[top_slide_link_blank_' . $i . ']',
				array(
					'default'           => false,
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'lightning_sanitize_checkbox',
				)
			);

			$priority = $priority + 1;
			$wp_customize->add_control(
				'lightning_theme_options[top_slide_link_blank_' . $i . ']',
				array(
					'label'    => __( 'Open in new window.', 'lightning' ),
					'section'  => 'lightning_slide',
					'settings' => 'lightning_theme_options[top_slide_link_blank_' . $i . ']',
					'type'     => 'checkbox',
					'priority' => $priority,
				)
			);

		// text title
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_text_title_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$priority = $priority + 1;
		$wp_customize->add_control(
			'top_slide_text_title_' . $i,
			array(
				'label'       => '[' . $i . '] ' . __( 'Slide title', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
				'section'     => 'lightning_slide',
				'settings'    => 'lightning_theme_options[top_slide_text_title_' . $i . ']',
				'type'        => 'textarea',
				'priority'    => $priority,
				'description' => '',
			)
		);

		// text caption
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_text_caption_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$priority = $priority + 1;
		$wp_customize->add_control(
			'top_slide_text_caption_' . $i,
			array(
				'label'       => '[' . $i . '] ' . __( 'Slide text', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
				'section'     => 'lightning_slide',
				'settings'    => 'lightning_theme_options[top_slide_text_caption_' . $i . ']',
				'type'        => 'textarea',
				'priority'    => $priority,
				'description' => '',
			)
		);

		// btn text
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_text_btn_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'wp_kses_post',
			)
		);

		$priority = $priority + 1;
		$wp_customize->add_control(
			new Custom_Text_Control(
				$wp_customize,
				'top_slide_text_btn_' . $i,
				array(
					'label'       => '[' . $i . '] ' . __( 'Button text', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
					'section'     => 'lightning_slide',
					'settings'    => 'lightning_theme_options[top_slide_text_btn_' . $i . ']',
					'type'        => 'text',
					'priority'    => $priority,
					'description' => __( 'If you do not fill in the link url and button text that, button is do not display.', 'lightning' ),
				)
			)
		);

		// text position
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_text_align_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'lightning_sanitize_radio',
			)
		);

		$priority = $priority + 1;
		$wp_customize->add_control(
			'top_slide_text_align_' . $i,
			array(
				'label'    => '[' . $i . '] ' . __( 'Position to display text', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
				'section'  => 'lightning_slide',
				'settings' => 'lightning_theme_options[top_slide_text_align_' . $i . ']',
				'type'     => 'radio',
				'priority' => $priority,
				'choices'  => array(
					'left'   => __( 'Left', 'lightning' ),
					'center' => __( 'Center', 'lightning' ),
					'right'  => __( 'Right', 'lightning' ),
				),
			)
		);

		// color
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_text_color_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$priority = $priority + 1;
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'top_slide_text_color_' . $i . '',
				array(
					'label'    => '[' . $i . '] ' . __( 'Slide text color', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
					'section'  => 'lightning_slide',
					'settings' => 'lightning_theme_options[top_slide_text_color_' . $i . ']',
					'priority' => $priority,
				)
			)
		);

		// top_slide_text_shadow_use_
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_text_shadow_use_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'lightning_sanitize_checkbox',
			)
		);
		$priority = $priority + 1;
		$wp_customize->add_control(
			'lightning_theme_options[top_slide_text_shadow_use_' . $i . ']',
			array(
				'label'    => __( 'Use text shadow', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
				'section'  => 'lightning_slide',
				'settings' => 'lightning_theme_options[top_slide_text_shadow_use_' . $i . ']',
				'type'     => 'checkbox',
				'priority' => $priority,
			)
		);

		// top_slide_text_shadow_color_
		$wp_customize->add_setting(
			'lightning_theme_options[top_slide_text_shadow_color_' . $i . ']',
			array(
				'default'           => '',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_hex_color',
			)
		);
		$priority = $priority + 1;
		$wp_customize->add_control(
			new WP_Customize_Color_Control(
				$wp_customize,
				'top_slide_text_shadow_color_' . $i . '',
				array(
					'label'    => '[' . $i . '] ' . __( 'Text shadow color', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
					'section'  => 'lightning_slide',
					'settings' => 'lightning_theme_options[top_slide_text_shadow_color_' . $i . ']',
					'priority' => $priority,
				)
			)
		);

		$i++;
	}
}
