<?php
/*-------------------------------------------*/
/*	Theme setup
/*-------------------------------------------*/
/*	Load JS and CSS
/*-------------------------------------------*/
/*	Head logo
/*-------------------------------------------*/
/*	WidgetArea initiate
/*-------------------------------------------*/
/*	Year Artchive list 'year' and count insert to inner </a>
/*-------------------------------------------*/
/*	Category list 'count insert to inner </a>
/*-------------------------------------------*/


/*-------------------------------------------*/
/*	Theme setup
/*-------------------------------------------*/
add_action('after_setup_theme', 'lightning_theme_setup');

function lightning_theme_setup() {

	/*-------------------------------------------*/
	/*	Admin page _ Eye catch
	/*-------------------------------------------*/
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 320, 180, true );

	/*-------------------------------------------*/
	/*	Custom menu
	/*-------------------------------------------*/
	register_nav_menus( array( 'Header' => 'Header Navigation', ) );
	register_nav_menus( array( 'Footer' => 'Footer Navigation', ) );

	load_theme_textdomain('lightning', get_template_directory() . '/languages');

	/*-------------------------------------------*/
	/*	Set content width
	/* 	(Auto set up to media max with.)
	/*-------------------------------------------*/
	if ( ! isset( $content_width ) ) $content_width = 780;

	/*-------------------------------------------*/
	/*	Admin page _ Add editor css
	/*-------------------------------------------*/
	add_editor_style('/css/editor.css');
	add_editor_style('//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.3.0/css/font-awesome.min.css');

	add_theme_support( 'automatic-feed-links' );
}

/*-------------------------------------------*/
/*	Load JS and CSS
/*-------------------------------------------*/

add_action('wp_enqueue_scripts','lightning_addJs');
function lightning_addJs(){
	wp_register_script( 'lightning-js' , get_template_directory_uri().'/js/all.min.js', array('jquery'), '20150619' );
	wp_enqueue_script( 'lightning-js' );
}

add_action( 'wp_enqueue_scripts', 'lightning_commentJs' );
function lightning_commentJs(){
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action('wp_enqueue_scripts', 'lightning_css' );
function lightning_css(){
	wp_enqueue_style( 'lightning-design-style', get_template_directory_uri().'/css/style.css', array(), '20150622' );
	wp_enqueue_style( 'lightning-theme-style', get_stylesheet_uri(), array('lightning-design-style'), '20150622');
}

/*-------------------------------------------*/
/*	Load Theme customizer
/*-------------------------------------------*/
require( get_template_directory() . '/functions_customizer.php' );

/*-------------------------------------------*/
/*	Load helpers
/*-------------------------------------------*/
require( get_template_directory() . '/functions_helpers.php' );


/*-------------------------------------------*/
/*	WidgetArea initiate
/*-------------------------------------------*/
function lightning_widgets_init() {
	// sidebar widget area
		register_sidebar( array(
			'name' => __('Sidebar(Home)', 'lightning' ),
			'id' => 'front-side-top-widget-area',
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title subSection-title">',
			'after_title' => '</h1>',
		) );
		register_sidebar( array(
			'name' => __( 'Sidebar(Common top)', 'lightning' ),
			'id' => 'common-side-top-widget-area',
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title subSection-title">',
			'after_title' => '</h1>',
		) );
		register_sidebar( array(
			'name' => __( 'Sidebar(Common bottom)', 'lightning' ),
			'id' => 'common-side-bottom-widget-area',
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title subSection-title">',
			'after_title' => '</h1>',
		) );
		register_sidebar( array(
			'name' => __( 'Sidebar(Post contents)', 'lightning' ),
			'id' => 'post-side-widget-area',
			'description' => __( 'This widget area appears on the post contents page only.', 'lightning' ),
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title subSection-title">',
			'after_title' => '</h1>',
		) );

	// footer upper widget area

		register_sidebar( array(
			'name' => __( 'Home content top', 'lightning' ),
			'id' => 'home-content-top-widget-area',
			// 'description' => __( 'This widget area appears on the post contents page only.', 'lightning' ),
			'before_widget' => '<div class="widget %2$s" id="%1$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		) );

	// footer upper widget area

		register_sidebar( array(
			'name' => __( 'Widget area of upper footer', 'lightning' ),
			'id' => 'footer-upper-widget-1',
			// 'description' => __( 'This widget area appears on the post contents page only.', 'lightning' ),
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		) );

	// footer widget area
	for ( $i = 1; $i <= 3 ;) {
		register_sidebar( array(
			'name' => __( 'Footer widget area ', 'lightning' ).$i,
			'id' => 'footer-widget-'.$i,
			// 'description' => __( 'This widget area appears on the post contents page only.', 'lightning' ),
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		) );
		$i++;
	}
}
add_action( 'widgets_init', 'lightning_widgets_init' );


/*-------------------------------------------*/
/*	Year Artchive list 'year' and count insert to inner </a>
/*-------------------------------------------*/
function lightning_archives_link($html){
  return preg_replace('@</a>(.+?)</li>@', '\1</a></li>', $html);
}
add_filter('get_archives_link', 'lightning_archives_link');

/*-------------------------------------------*/
/*	Category list 'count insert to inner </a>
/*-------------------------------------------*/
function lightning_list_categories( $output, $args ) {
	$output = preg_replace('/<\/a>\s*\((\d+)\)/',' ($1)</a>',$output);
	return $output;
}
add_filter( 'wp_list_categories', 'lightning_list_categories', 10, 2 );