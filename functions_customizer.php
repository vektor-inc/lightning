<?php
/*-------------------------------------------*/
/*	customize_register
/*-------------------------------------------*/
add_action( 'customize_register', 'lightning_customize_register' );
function lightning_customize_register($wp_customize) {

	/*	Add text control description
	/*-------------------------------------------*/
   class Custom_Text_Control extends WP_Customize_Control {
		public $type = 'customtext';
		public $description = ''; // we add this for the extra description
		public function render_content() {
		?>
		<label>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<input type="text" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
			<span><?php echo $this->description; ?></span>
		</label>
		<?php
		}
	}

	/*	Add sanitize checkbox
	/*-------------------------------------------*/
	function lightning_sanitize_checkbox($input){
		if($input==true){
			return true;
		} else {
			return false;
		}
	}

	function lightning_sanitize_radio($input){
		return esc_attr( $input );
	}

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
		'title'				=> _x('Lightning Design settings', 'lightning theme-customizer', 'lightning'),
		'priority'			=> 500,
		// 'panel'				=> 'lightning_setting',
	) );

	// Add setting

	$wp_customize->add_setting( 'lightning_theme_options[head_logo]', array(
		'default'			=> '',
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'esc_url_raw',
	) );
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
	$wp_customize->add_setting('lightning_theme_options[top_sidebar_hidden]', array(
		'default'			=> false,
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	));
	$wp_customize->add_setting('lightning_theme_options[top_default_content_hidden]', array(
		'default'			=> false,
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	));
	$wp_customize->add_setting('lightning_theme_options[postUpdate_hidden]', array(
		'default'			=> false,
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	));
	$wp_customize->add_setting('lightning_theme_options[postAuthor_hidden]', array(
		'default'			=> false,
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'lightning_sanitize_checkbox',
	));

	// Create section UI

	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize,
		'head_logo',
		array(
			'label'     => _x('Header logo image', 'lightning theme-customizer', 'lightning'),
			'section'   => 'lightning_design',
			'settings'  => 'lightning_theme_options[head_logo]',
			'priority'  => 501,
			'description' => __('Recommended image size : 280*60px', 'lightning'),
		)
	) );

	if( apply_filters( 'lightning_show_default_keycolor_customizer', true ) ){
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'color_key', array(
			'label'    => _x('Key color', 'lightning theme-customizer', 'lightning'),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[color_key]',
			'priority' => 502,
		)));
		$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'color_key_dark', array(
			'label'    => _x('Key color(dark)', 'lightning theme-customizer', 'lightning'),
			'section'  => 'lightning_design',
			'settings' => 'lightning_theme_options[color_key_dark]',
			'priority' => 503,
		)));
	}

	$wp_customize->add_control( 'lightning_theme_options[top_sidebar_hidden]', array(
		'label'		=> _x( 'Don\'t show sidebar on home page' ,'lightning theme-customizer', 'lightning' ),
		'section'	=> 'lightning_design',
		'settings'  => 'lightning_theme_options[top_sidebar_hidden]',
		'type'		=> 'checkbox',
		'priority'	=> 520,
	));
	$wp_customize->add_control( 'lightning_theme_options[top_default_content_hidden]', array(
		'label'		=> _x( 'Don\'t show default content(Post list or Front page) at home page' ,'lightning theme-customizer', 'lightning' ),
		'section'	=> 'lightning_design',
		'settings'  => 'lightning_theme_options[top_default_content_hidden]',
		'type'		=> 'checkbox',
		'priority'	=> 521,
	));
	$wp_customize->add_control( 'lightning_theme_options[postUpdate_hidden]', array(
		'label'		=> _x( 'Hide modified date on single pages.' ,'lightning theme-customizer', 'lightning' ),
		'section'	=> 'lightning_design',
		'settings'  => 'lightning_theme_options[postUpdate_hidden]',
		'type'		=> 'checkbox',
		'priority'	=> 522,
	));
	$wp_customize->add_control( 'lightning_theme_options[postAuthor_hidden]', array(
		'label'		=> _x( 'Don\'t display post author on a single page' ,'lightning theme-customizer', 'lightning' ),
		'section'	=> 'lightning_design',
		'settings'  => 'lightning_theme_options[postAuthor_hidden]',
		'type'		=> 'checkbox',
		'priority'	=> 523,
	));


	/*-------------------------------------------*/
	/*	Top slide show
	/*-------------------------------------------*/
	$wp_customize->add_section( 'lightning_slide', array(
		'title'			=> _x('Lightning Home page slide show', 'lightning theme-customizer', 'lightning'),
		'priority'		=> 600,
		// 'panel'			=> 'lightning_setting',
	) );

	// slide image
	$priority = 610;
	$lightning_theme_options = get_option('lightning_theme_options');

	for ( $i = 1; $i <= 5; ) {

		// Default images
		if ($i <= 3) {
			$default_image = get_template_directory_uri().'/images/top_image_'.$i.'.jpg';
		} else {
			$default_image = '';
		}

		// Add setting
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_title_'.$i.']',	array(
			'default' 			=> '',
			'type'				=> 'option',
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			) );
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_image_'.$i.']',  array(
			'default'        	=> $default_image,
			'type'           	=> 'option',
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
			) );
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_image_mobile_'.$i.']',  array(
			'default'        	=> '',
			'type'           	=> 'option',
			'capability'    	=> 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
			) );
		$wp_customize->add_setting( 'lightning_theme_options[top_slide_url_'.$i.']',	array(
			'default' 			=> '',
			'type'				=> 'option',
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
			) );
		$wp_customize->add_setting('lightning_theme_options[top_slide_link_blank_'.$i.']', array(
			'default'			=> false,
			'type'				=> 'option',
			'capability'		=> 'edit_theme_options',
			'sanitize_callback' => 'lightning_sanitize_checkbox',
		));

		// Add control
		$priority = $priority + 1;
		$wp_customize->add_control( new Custom_Text_Control( $wp_customize, 'top_slide_title_'.$i, array(
			'label'     => _x('Slide image title', 'lightning theme-customizer', 'lightning').' '.$i,
			'section'  => 'lightning_slide',
			'settings' => 'lightning_theme_options[top_slide_title_'.$i.']',
			'type' => 'text',
			'priority' => $priority,
			'description' => __('This title text is print to alt tag.', 'lightning'),
			) ) );

		$priority = $priority + 1;
		$wp_customize->add_control( 'top_slide_url_'.$i, array(
			'label'     => _x('Slide image link url', 'lightning theme-customizer', 'lightning').' '.$i,
			'section'  => 'lightning_slide',
			'settings' => 'lightning_theme_options[top_slide_url_'.$i.']',
			'type' => 'text',
			'priority' => $priority,
			) );

		$priority = $priority + 1;
		$wp_customize->add_control( 'lightning_theme_options[top_slide_link_blank_'.$i.']', array(
			'label'		=> _x( 'Open in new window.' ,'lightning theme-customizer', 'lightning' ),
			'section'	=> 'lightning_slide',
			'settings'  => 'lightning_theme_options[top_slide_link_blank_'.$i.']',
			'type'		=> 'checkbox',
			'priority'	=> $priority,
		));

		$priority = $priority + 1;
		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			'top_slide_image_'.$i,
			array(
				'label'     => _x('Slide image', 'lightning theme-customizer', 'lightning').' '.$i,
				'section'   => 'lightning_slide',
				'settings'  => 'lightning_theme_options[top_slide_image_'.$i.']',
				'priority'  => $priority,
				'description' => __('Recommended image size : 1900*500px', 'lightning'),
			)
		) );

		$priority = $priority + 1;
		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			'top_slide_image_mobile_'.$i,
			array(
				'label'     => _x('Slide image for mobile (optional)', 'lightning theme-customizer', 'lightning').' '.$i,
				'section'   => 'lightning_slide',
				'settings'  => 'lightning_theme_options[top_slide_image_mobile_'.$i.']',
				'priority'  => $priority,
				'description' => '',
			)
		) );

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
add_action( 'wp_head','lightning_print_css_common', 150);
function lightning_print_css_common(){
	$options = get_option('lightning_theme_options');
	if ( isset($options['color_key']) && isset($options['color_key_dark']) ) {
	$color_key = ( !empty($options['color_key']) )? esc_html($options['color_key']) : '#337ab7';
	$color_key_dark = ( !empty($options['color_key_dark'] ) )? esc_html($options['color_key_dark']) : '#2e6da4';
	?>
<!-- [ Lightning Common ] -->
<style type="text/css">
.veu_color_txt_key { color:<?php echo $color_key_dark;?> ; }
.veu_color_bg_key { background-color:<?php echo $color_key_dark;?> ; }
.veu_color_border_key { border-color:<?php echo $color_key_dark;?> ; }
a { color:<?php echo $color_key_dark;?> ; }
a:hover { color:<?php echo $color_key;?> ; }
.btn-default { border-color:<?php echo $color_key;?>;color:<?php echo $color_key;?>;}
.btn-default:focus,
.btn-default:hover { border-color:<?php echo $color_key;?>;background-color: <?php echo $color_key;?>; }
.btn-primary { background-color:<?php echo $color_key;?>;border-color:<?php echo $color_key_dark;?>; }
.btn-primary:focus,
.btn-primary:hover { background-color:<?php echo $color_key_dark;?>;border-color:<?php echo $color_key;?>; }
</style>
<!-- [ / Lightning Common ] -->
<?php } // if ( isset($options['color_key'] && isset($options['color_key_dark'] ) {
}
