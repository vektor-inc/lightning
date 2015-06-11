<?php
add_action( 'customize_register', 'bvII_customize_register' );
function bvII_customize_register($wp_customize) {

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
            <span><?php echo esc_html( $this->description ); ?></span>
        </label>
        <?php
        }
    }

    /*-------------------------------------------*/
	/*	Design setting
	/*-------------------------------------------*/
    $wp_customize->add_section( 'bvII_design', array(
        'title'          => _x('Design settings', 'bvII theme-customizer', 'bvII'),
        'priority'       => 500,
    ) );

    // Add setting

    $wp_customize->add_setting( 'bvII_theme_options[head_logo]', array(
        'default'			=> '',
        'type'				=> 'option',
        'capability'		=> 'edit_theme_options',
        'sanitize_callback' => 'esc_url_raw',
    ) );
	$wp_customize->add_setting( 'bvII_theme_options[color_key]', array(
		'default'			=> '#337ab7',
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_setting( 'bvII_theme_options[color_key_dark]', array(
		'default'			=> '#2e6da4',
		'type'				=> 'option',
		'capability'		=> 'edit_theme_options',
		'sanitize_callback' => 'sanitize_hex_color',
	) );

	// Create section UI

	$wp_customize->add_control( new WP_Customize_Image_Control(
		$wp_customize,
		'head_logo',
		array(
			'label'     => _x('Header logo image', 'bvII theme-customizer', 'bvII'),
			'section'   => 'bvII_design',
			'settings'  => 'bvII_theme_options[head_logo]',
			'priority'  => 501,
		)
	) );

	$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'color_key', array(
		'label'    => __('Key color', 'bvII'),
		'section'  => 'bvII_design',
		'settings' => 'bvII_theme_options[color_key]',
		'priority' => 502,
	)));
	$wp_customize->add_control( new WP_Customize_Color_Control($wp_customize, 'color_key_dark', array(
		'label'    => __('Key color(dark)', 'bvII'),
		'section'  => 'bvII_design',
		'settings' => 'bvII_theme_options[color_key_dark]',
		'priority' => 503,
	)));

	/*-------------------------------------------*/
	/*	Top slide show
	/*-------------------------------------------*/
    $wp_customize->add_section( 'bvII_slide', array(
        'title'          => _x('Slide show', 'bvII theme-customizer', 'bvII'),
        'priority'       => 600,
    ) );

    // slide count
	$wp_customize->add_setting( 'bvII_theme_options[top_slide_count]',	array(
		'default' 			=> 3,
		'type'				=> 'option',
		'capability' 		=> 'edit_theme_options',
		'sanitize_callback' => 'sanitize_text_field',
		) );
	$wp_customize->add_control( new Custom_Text_Control( $wp_customize, 'slide_count',array(
		'label'     => __('Slide image count', 'bvII theme-customizer', 'bvII'),
		'section'  => 'bvII_slide',
		'settings' => 'bvII_theme_options[top_slide_count]',
		'type' => 'text',
		'priority' => 601,
		'description' => __('If you change this count,please save & reload this page.', 'bvII'),
		) ) );

	// slide image
	$priority = 610;
	$bvII_theme_options = get_option('bvII_theme_options');
	$top_slide_count = ( isset( $bvII_theme_options['top_slide_count'] ) ) ? $bvII_theme_options['top_slide_count'] : 3 ;
    for ( $i = 1; $i <= $top_slide_count; ) {

    	// Default images
    	if ($i <= 3) {
    		$default_image = get_template_directory_uri().'/images/top_image_'.$i.'.jpg';
    	} else {
    		$default_image = '';
    	}

    	// Add setting
		$wp_customize->add_setting( 'bvII_theme_options[top_slide_title_'.$i.']',	array(
			'default' 			=> '',
			'type'				=> 'option',
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'sanitize_text_field',
			) );
	    $wp_customize->add_setting( 'bvII_theme_options[top_slide_image_'.$i.']',  array(
	        'default'        	=> $default_image,
	        'type'           	=> 'option',
	        'capability'    	=> 'edit_theme_options',
	        'sanitize_callback' => 'esc_url_raw',
	    	) );
		$wp_customize->add_setting( 'bvII_theme_options[top_slide_url_'.$i.']',	array(
			'default' 			=> '',
			'type'				=> 'option',
			'capability' 		=> 'edit_theme_options',
			'sanitize_callback' => 'esc_url_raw',
			) );

	    // Add control
		$priority = $priority + 1;
		$wp_customize->add_control( 'top_slide_title_'.$i, array(
			'label'     => __('Slide image title', 'bvII theme-customizer', 'bvII').' '.$i,
			'section'  => 'bvII_slide',
			'settings' => 'bvII_theme_options[top_slide_title_'.$i.']',
			'type' => 'text',
			'priority' => $priority,
			) );

		$priority = $priority + 1;
		$wp_customize->add_control( 'top_slide_url_'.$i, array(
			'label'     => __('Slide image url', 'bvII theme-customizer', 'bvII').' '.$i,
			'section'  => 'bvII_slide',
			'settings' => 'bvII_theme_options[top_slide_url_'.$i.']',
			'type' => 'text',
			'priority' => $priority,
			) );

	    $priority = $priority + 1;
		$wp_customize->add_control( new WP_Customize_Image_Control(
			$wp_customize,
			'top_slide_image_'.$i,
			array(
				'label'     => _x('Slide image', 'bvII theme-customizer', 'bvII').' '.$i,
				'section'   => 'bvII_slide',
				'settings'  => 'bvII_theme_options[top_slide_image_'.$i.']',
				'priority'  => $priority,
			)
		) );

		// Slide title

        $i++;
    }

}


/*-------------------------------------------*/
/*	Print head
/*-------------------------------------------*/
add_action( 'wp_head','bvII_print_css', 150);
function bvII_print_css(){
	$options = get_option('bvII_theme_options');
	$color_key = esc_html($options['color_key']);
	$color_key_dark = esc_html($options['color_key_dark']);
	?>
<style type="text/css">
a { color:<?php echo $color_key_dark;?> ; }
a:hover { color:<?php echo $color_key;?> ; }
h1.entry-title:first-letter { color:<?php echo $color_key;?>; }
h2 { border-left-color:<?php echo $color_key;?>}
h3:after,
.subSection-title:after { border-bottom-color:1px solid $color_key; }
h5 { color:<?php echo $color_key;?>; }

ul.gMenu a:hover { color:<?php echo $color_key;?>; }
.page-header { background-color:<?php echo $color_key;?>; }
.btn-default { border-color:<?php echo $color_key;?>;color:<?php echo $color_key;?>;}
.btn-default:hover { border-color:<?php echo $color_key;?>;background-color: <?php echo $color_key;?>; color:#fff; }
.btn-primary { background-color:<?php echo $color_key;?>;border-color:<?php echo $color_key_dark;?>; }
.btn-primary:hover { background-color:<?php echo $color_key_dark;?>;border-color:<?php echo $color_key;?>; }
.media .media-body .media-heading a:hover { color: <?php echo $color_key;?>;}
ul.page-numbers li span.page-numbers.current { background-color:<?php echo $color_key;?>; }
.pager li > a { border-color:<?php echo $color_key;?>;color:<?php echo $color_key;?>;}
.pager li > a:hover { background-color:<?php echo $color_key;?>;color:#fff;}
footer { border-top-color:<?php echo $color_key	;?> }

@media (min-width: 768px){
  ul.gMenu > li > a:hover:after, ul.gMenu > li.current-menu-item > a:after, ul.gMenu > li.current_page_parent > a:after, ul.gMenu > li.current-menu-ancestor > a:after, ul.gMenu > li.current_page_ancestor > a:after { border-bottom-color: <?php echo $color_key ;?> }
} /* @media (min-width: 768px) */
</style>
<?php }