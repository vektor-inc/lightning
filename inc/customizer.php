<?php
/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register' );
function lightning_customize_register($wp_customize)
{

	/*	Add text control description
	/*-------------------------------------------*/
   class Custom_Text_Control extends WP_Customize_Control {
		public $type = 'customtext';
		public $description = ''; // we add this for the extra description
		public $input_after = '';
		public function render_content()
    { ?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php $style = ( $this->input_after ) ? ' style="width:50%"' : '';?>
			<div>
			<input type="text" value="<?php echo esc_attr( $this->value() ); ?>"<?php echo $style;?> <?php $this->link(); ?> />
			<?php echo $this->input_after; ?>
			</div>
			<span><?php echo $this->description; ?></span>
		</label>
		<?php
    } // public function render_content() {
	} // class Custom_Text_Control extends WP_Customize_Control



	/*-------------------------------------------*/
	/*	Lightning Panel
	/*-------------------------------------------*/
	// $wp_customize->add_panel( 'lightning_setting', array(
	//    	'priority'       => 25,
	//    	'capability'     => 'edit_theme_options',
	//    	'theme_supports' => '',
	//    	'title'          => __( 'Lightning settings', 'lightning' ),
	// ));

	/*-------------------------------------------*/
	/*	Design setting
	/*-------------------------------------------*/
	$wp_customize->add_section( 'lightning_design', array(
		'title'				=> __('Lightning Design settings', 'lightning' ),
		'priority'			=> 500,
		// 'panel'				=> 'lightning_setting',
	) );

	// Add setting

	// head logo
	$wp_customize->add_setting( 'lightning_theme_options[head_logo]', array(
		'default'			=> '',
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'esc_url_raw',
	) );
	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize,
		'head_logo',
		array(
			'label'     => __('Header logo image', 'lightning' ),
			'section'   => 'lightning_design',
			'settings'  => 'lightning_theme_options[head_logo]',
			'priority'  => 501,
			'description' => __( 'Recommended image size : 280*60px', 'lightning' ),
		)
	) );
	$wp_customize->selective_refresh->add_partial( 'lightning_theme_options[head_logo]', array(
		'selector' => '.siteHeader_logo',
		'render_callback' => '',
	) );


	// color
	$wp_customize->add_setting( 'lightning_theme_options[color_key]', array(
		'default'			=> '#337ab7',
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_setting( 'lightning_theme_options[color_key_dark]', array(
		'default'			=> '#2e6da4',
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	if( apply_filters( 'lightning_show_default_keycolor_customizer', true ) ){
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'color_key', array(
			'label'    => __('Key color', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[color_key]',
			'priority' => 502,
		)));
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'color_key_dark', array(
			'label'    => __('Key color(dark)', 'lightning' ),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[color_key_dark]',
			'priority' => 503,
		)));
	}


	// top_sidebar_hidden
	$wp_customize->add_setting('lightning_theme_options[top_sidebar_hidden]', array(
		'default'			=> false,
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	));
	$wp_customize->add_control( 'lightning_theme_options[top_sidebar_hidden]', array(
		'label'		=> __( 'Don\'t show sidebar on home page' , 'lightning' ),
		'section'	=> 'lightning_design',
		'settings'  => 'lightning_theme_options[top_sidebar_hidden]',
		'type'		=> 'checkbox',
		'priority'	=> 520,
	));
	$wp_customize->selective_refresh->add_partial( 'lightning_theme_options[top_sidebar_hidden]', array(
		'selector' => '.home .mainSection',
		'render_callback' => '',
	) );


	// top_default_content_hidden
	$wp_customize->add_setting('lightning_theme_options[top_default_content_hidden]', array(
		'default'			=> false,
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	));
	$wp_customize->add_control( 'lightning_theme_options[top_default_content_hidden]', array(
		'label'		=> __( 'Don\'t show default content(Post list or Front page) at home page' , 'lightning' ),
		'section'	=> 'lightning_design',
		'settings'  => 'lightning_theme_options[top_default_content_hidden]',
		'type'		=> 'checkbox',
		'priority'	=> 521,
	));


	// postUpdate_hidden
	$wp_customize->add_setting('lightning_theme_options[postUpdate_hidden]', array(
		'default'			=> false,
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	));
	$wp_customize->add_control( 'lightning_theme_options[postUpdate_hidden]', array(
		'label'		=> __( 'Hide modified date on single pages.' , 'lightning' ),
		'section'	=> 'lightning_design',
		'settings'  => 'lightning_theme_options[postUpdate_hidden]',
		'type'		=> 'checkbox',
		'priority'	=> 522,
	));


	// postAuthor_hidden
	$wp_customize->add_setting('lightning_theme_options[postAuthor_hidden]', array(
		'default'			=> false,
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	));
	$wp_customize->add_control( 'lightning_theme_options[postAuthor_hidden]', array(
		'label'		=> __( 'Don\'t display post author on a single page' , 'lightning' ),
		'section'	=> 'lightning_design',
		'settings'  => 'lightning_theme_options[postAuthor_hidden]',
		'type'		=> 'checkbox',
		'priority'	=> 523,
	));

	// sidebar_child_list_hidden
	// lightning advanced unit 'priority'	=> 550,

	// sidebar_fix
	// lightning advanced unit 'priority'	=> 555,

	/*-------------------------------------------*/
	/*	Top slide show
	/*-------------------------------------------*/
	$wp_customize->add_section( 'lightning_slide', array(
		'title'			=> __('Lightning Home page slide show', 'lightning' ),
		'priority'		=> 600,
		// 'panel'			=> 'lightning_setting',
	) );

	// Slide interval time
	$wp_customize->add_setting( 'lightning_theme_options[top_slide_time]',	array(
		'default' 			=> 4000,
		'type'				=> 'option',
		'capability' 		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_number',
		) );

	$wp_customize->add_control( new Custom_Text_Control( $wp_customize, 'top_slide_time', array(
		'label'     => __( 'Slide interval time', 'lightning' ),
		'section'  => 'lightning_slide',
		'settings' => 'lightning_theme_options[top_slide_time]',
		'type' => 'text',
		'priority' => 605,
		'description' => '',
		'input_after' => __( 'millisecond', 'lightning' )
		) ) );

	// slide image
	$priority = 610;

	$theme_options_default = lightning_theme_options_default();

	for ( $i = 1; $i <= 5; ) {

		$theme_options_customize_default['top_slide_image'] = '';
		switch ($i){
	    case 1:
				$theme_options_customize_default['top_slide_image'] = $theme_options_default['top_slide_image_1'];
				break;
	    case 2:
				$theme_options_customize_default['top_slide_image'] = $theme_options_default['top_slide_image_2'];
				break;
	    case 3:
				$theme_options_customize_default['top_slide_image'] = $theme_options_default['top_slide_image_3'];
				break;
				}


		$wp_customize->selective_refresh->add_partial( 'lightning_theme_options[top_slide_image_'.$i.']', array(
			'selector' => '.item-'.$i.' picture',
			'render_callback' => '',
		) );


		// image
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_image_'.$i.']',  array(
			'default'        	=> $theme_options_customize_default['top_slide_image'],
			'type'           	=> 'option',
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
			) );

		$priority = $priority + 1;
		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			'top_slide_image_'.$i,
			array(
				'label'     => '['.$i.'] '.__('Slide image', 'lightning' ),
				'section'   => 'lightning_slide',
				'settings'  => 'lightning_theme_options[top_slide_image_'.$i.']',
				'priority'  => $priority,
				'description' => __( 'Recommended image size : 1900*600px', 'lightning' ),
			)
		) );


		// image mobile
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_image_mobile_'.$i.']',  array(
			'default'        	=> '',
			'type'           	=> 'option',
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
			) );

		$priority = $priority + 1;
		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			'top_slide_image_mobile_'.$i,
			array(
				'label'     => '['.$i.'] '.__( 'Slide image for mobile', 'lightning' ).' ( '.__( 'optional','lightning' ).' )',
				'section'   => 'lightning_slide',
				'settings'  => 'lightning_theme_options[top_slide_image_mobile_'.$i.']',
				'priority'  => $priority,
				'description' => '',
			)
		) );

		// alt
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_alt_'.$i.']',	array(
			'default' 			=> '',
			'type'				=> 'option',
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			) );

		$priority = $priority + 1;
		$wp_customize->add_control( new Custom_Text_Control( $wp_customize, 'top_slide_alt_'.$i, array(
			'label'     => '['.$i.'] '.__( 'Slide image alt', 'lightning' ),
			'section'  => 'lightning_slide',
			'settings' => 'lightning_theme_options[top_slide_alt_'.$i.']',
			'type' => 'text',
			'priority' => $priority,
			'description' => __( 'This title text is print to alt tag.', 'lightning' ),
			) ) );

		// color
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_cover_color_'.$i.']', array(
			'default'			=> '',
			'type'				=> 'option',
			'capability'		=> 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$priority = $priority + 1;
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'top_slide_cover_color_'.$i.'', array(
			'label'    => '['.$i.'] '.__( 'Slide cover color', 'lightning' ).' ( '.__( 'optional','lightning' ).' )',
			'section'  => 'lightning_slide',
			'settings' => 'lightning_theme_options[top_slide_cover_color_'.$i.']',
			'priority' => $priority,
		)));

		// opacity
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_cover_opacity_'.$i.']',	array(
			'default' 			=> '',
			'type'				=> 'option',
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_number_percentage',
			) );
		$priority = $priority + 1;
		$wp_customize->add_control( new Custom_Text_Control( $wp_customize, 'top_slide_cover_opacity_'.$i, array(
			'label'     => '['.$i.'] '.__( 'Slide cover opacity', 'lightning' ),
			'section'  => 'lightning_slide',
			'settings' => 'lightning_theme_options[top_slide_cover_opacity_'.$i.']',
			'type' => 'text',
			'priority' => $priority,
			'description' => __( 'Please input 0 - 100 number', 'lightning' ),
			'input_after' => '%',
			) ) );

			// url
			$wp_customize->add_setting( 'lightning_theme_options[top_slide_url_'.$i.']',	array(
				'default' 			=> '',
				'type'				=> 'option',
				'capability' 		=> 'edit_theme_options',
				'sanitize_callback' => 'esc_url_raw',
				) );

			$priority = $priority + 1;
			$wp_customize->add_control( 'top_slide_url_'.$i, array(
				'label'     => '['.$i.'] '.__('Slide image link url', 'lightning' ),
				'section'  => 'lightning_slide',
				'settings' => 'lightning_theme_options[top_slide_url_'.$i.']',
				'type' => 'text',
				'priority' => $priority,
				) );


			// link blank
			$wp_customize->add_setting('lightning_theme_options[top_slide_link_blank_'.$i.']', array(
				'default'			=> false,
				'type'				=> 'option',
				'capability'		=> 'edit_theme_options',
				'sanitize_callback' => 'lightning_sanitize_checkbox',
	    ) );

			$priority = $priority + 1;
			$wp_customize->add_control( 'lightning_theme_options[top_slide_link_blank_'.$i.']', array(
				'label'		=> __( 'Open in new window.', 'lightning' ),
				'section'	=> 'lightning_slide',
				'settings'  => 'lightning_theme_options[top_slide_link_blank_'.$i.']',
				'type'		=> 'checkbox',
				'priority'	=> $priority,
				) );


		// text title
    $wp_customize->add_setting( 'lightning_theme_options[top_slide_text_title_'.$i.']',	array(
			'default' 			=> '',
			'type'				=> 'option',
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'wp_kses_data',
		) );

		$priority = $priority + 1;
    $wp_customize->add_control( 'top_slide_text_title_'.$i, array(
      'label'     => '['.$i.'] '.__( 'Slide title', 'lightning' ).' ( '.__( 'optional','lightning' ).' )',
      'section'  => 'lightning_slide',
      'settings' => 'lightning_theme_options[top_slide_text_title_'.$i.']',
      'type' => 'textarea',
      'priority' => $priority,
      'description' => '',
    ) );


		// text caption
    $wp_customize->add_setting( 'lightning_theme_options[top_slide_text_caption_'.$i.']',	array(
      'default' 			=> '',
      'type'				=> 'option',
      'capability' 		=> 'edit_theme_options',
      'sanitize_callback' => 'wp_kses_data',
    ) );

		$priority = $priority + 1;
    $wp_customize->add_control( 'top_slide_text_caption_'.$i, array(
      'label'     => '['.$i.'] '.__( 'Slide text', 'lightning' ).' ( '.__( 'optional','lightning' ).' )',
      'section'  => 'lightning_slide',
      'settings' => 'lightning_theme_options[top_slide_text_caption_'.$i.']',
      'type' => 'textarea',
      'priority' => $priority,
      'description' => '',
    ) );


		// btn text
    $wp_customize->add_setting( 'lightning_theme_options[top_slide_text_btn_'.$i.']',	array(
      'default' 			=> '',
      'type'				=> 'option',
      'capability' 		=> 'edit_theme_options',
      'sanitize_callback' => 'sanitize_text_field',
    ) );

		$priority = $priority + 1;
    $wp_customize->add_control( new Custom_Text_Control( $wp_customize, 'top_slide_text_btn_'.$i, array(
      'label'     => '['.$i.'] '.__( 'Button text', 'lightning' ).' ( '.__( 'optional','lightning' ).' )',
      'section'  => 'lightning_slide',
      'settings' => 'lightning_theme_options[top_slide_text_btn_'.$i.']',
      'type' => 'text',
      'priority' => $priority,
      'description' => __( 'If you do not fill in the link url and button text that, button is do not display.', 'lightning' ),
    ) ) );


		// text position
    $wp_customize->add_setting( 'lightning_theme_options[top_slide_text_align_'.$i.']',  array(
      'default'           => '',
      'type'              => 'option',
      'capability'        => 'edit_theme_options',
      'sanitize_callback' => 'lightning_sanitize_radio',
    ) );

    $priority = $priority + 1;
    $wp_customize->add_control( 'top_slide_text_align_'.$i, array(
  		'label'     => '['.$i.'] '.__( 'Position to display text', 'lightning' ).' ( '.__( 'optional','lightning' ).' )',
  		'section'   => 'lightning_slide',
  		'settings'  => 'lightning_theme_options[top_slide_text_align_'.$i.']',
  		'type' => 'radio',
      'priority' => $priority,
  		'choices' => array(
				'left' => __( 'Left', 'lightning' ),
  			'center' => __( 'Center', 'lightning' ),
  			'right' => __( 'Right', 'lightning' ),
  			),
  	));


		// color
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_text_color_'.$i.']', array(
			'default'			=> '',
			'type'				=> 'option',
			'capability'		=> 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$priority = $priority + 1;
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'top_slide_text_color_'.$i.'', array(
			'label'    => '['.$i.'] '.__( 'Slide text color', 'lightning' ).' ( '.__( 'optional','lightning' ).' )',
			'section'  => 'lightning_slide',
			'settings' => 'lightning_theme_options[top_slide_text_color_'.$i.']',
			'priority' => $priority,
		)));


		// top_slide_text_shadow_use_
		$wp_customize->add_setting('lightning_theme_options[top_slide_text_shadow_use_'.$i.']', array(
			'default'			=> '',
			'type'				=> 'option',
			'capability'		=> 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		));
		$priority = $priority + 1;
		$wp_customize->add_control( 'lightning_theme_options[top_slide_text_shadow_use_'.$i.']', array(
			'label'		=> __( 'Use text shadow', 'lightning' ).' ( '.__( 'optional','lightning' ).' )',
			'section'	=> 'lightning_slide',
			'settings'  => 'lightning_theme_options[top_slide_text_shadow_use_'.$i.']',
			'type'		=> 'checkbox',
			'priority'	=> $priority,
		));

		// top_slide_text_shadow_color_
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_text_shadow_color_'.$i.']', array(
			'default'			=> '',
			'type'				=> 'option',
			'capability'		=> 'edit_theme_options',
			'sanitize_callback' => 'sanitize_hex_color',
		) );
		$priority = $priority + 1;
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'top_slide_text_shadow_color_'.$i.'', array(
			'label'    => '['.$i.'] '.__( 'Text shadow color', 'lightning' ).' ( '.__( 'optional','lightning' ).' )',
			'section'  => 'lightning_slide',
			'settings' => 'lightning_theme_options[top_slide_text_shadow_color_'.$i.']',
			'priority' => $priority,
		)));

		$i++;
	}

}

/*-------------------------------------------*/
/*	Lightning custom color Print head
/*	* This is used for Contents and Plugins and others
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_output_keyColorCss', 5);
function lightning_output_keycolorcss(){
	$options = get_option('lightning_theme_options');
	$corlors_default = array(
		'color_key'       => empty($options['color_key'])? '#337ab7' : $options['color_key'],
		'color_key_dark'  => empty($options['color_key_dark'])? '#2e6da4' : $options['color_key_dark'],
	);
	$corlors = apply_filters('lightning_keycolors', $corlors_default);
	$types = array('_bg'=>'background-color','_txt'=>'color','_border'=>'border-color');
	reset( $corlors );
	echo '<style type="text/css">';
	while( list( $k,$v ) = each( $corlors ) ){
		reset( $types );
		while( list( $kk,$vv ) = each( $types ) ){
			echo ".{$k}{$kk},.{$k}{$kk}_hover:hover{{$vv}: {$v};}";
		}
	}
	echo "</style>\n";
}

/*-------------------------------------------*/
/*	Print head
/*-------------------------------------------*/
add_action( 'wp_head','lightning_print_css_common', 2);
function lightning_print_css_common(){
	$options = get_option('lightning_theme_options');
	$skin_dynamic_css = '';

	if ( isset($options['color_key']) && isset($options['color_key_dark']) ) {
		$color_key = ( !empty($options['color_key']) )? esc_html($options['color_key']) : '#337ab7';
		$color_key_dark = ( !empty($options['color_key_dark'] ) )? esc_html($options['color_key_dark']) : '#2e6da4';
		$skin_dynamic_css .= '
		.veu_color_txt_key { color:'.$color_key_dark.' ; }
		.veu_color_bg_key { background-color:'.$color_key_dark.' ; }
		.veu_color_border_key { border-color:'.$color_key_dark.' ; }
		a { color:'.$color_key_dark.' ; }
		a:hover { color:'.$color_key.' ; }
		.btn-default { border-color:'.$color_key.';color:'.$color_key.';}
		.btn-default:focus,
		.btn-default:hover { border-color:'.$color_key.';background-color: '.$color_key.'; }
		.btn-primary { background-color:'.$color_key.';border-color:'.$color_key_dark.'; }
		.btn-primary:focus,
		.btn-primary:hover { background-color:'.$color_key_dark.';border-color:'.$color_key.'; }
		';
	} // if ( isset($options['color_key'] && isset($options['color_key_dark'] ) {

	if ( $skin_dynamic_css ) {
		// delete br
		$skin_dynamic_css = str_replace(PHP_EOL, '', $skin_dynamic_css);
		// delete tab
		$skin_dynamic_css = preg_replace('/[\n\r\t]/', '', $skin_dynamic_css);
		// multi space convert to single space
		$skin_dynamic_css = preg_replace('/\s(?=\s)/', '', $skin_dynamic_css);

		wp_add_inline_style( 'lightning-design-style', $skin_dynamic_css );

	}

}
