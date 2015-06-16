<?php
/*-------------------------------------------*/
/*	Theme setup
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
add_action('after_setup_theme', 'bvII_theme_setup');

function bvII_theme_setup() {

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

	load_theme_textdomain('bvII', get_template_directory() . '/languages');

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

add_action('wp_head','bvII_addJs');
function bvII_addJs(){
	wp_register_script( 'bvII-js' , get_template_directory_uri().'/js/all.min.js', array('jquery'), '20150603a' );
	wp_enqueue_script( 'bvII-js' );
}

add_action( 'wp_enqueue_scripts', 'bvII_commentJs' );
function bvII_commentJs(){
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

function bvII_css(){
	echo '<link rel="stylesheet" href="'.get_stylesheet_uri().'" type="text/css" media="all" />'."\n";
//	wp_enqueue_style('bvII_style', get_stylesheet_uri(), array(), false);
}
add_action('wp_head', 'bvII_css', 190);

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
function bvII_widgets_init() {
	// sidebar widget area
		register_sidebar( array(
			'name' => 'Sidebar('.__( 'Home', 'bvII' ).')',
			'id' => 'front-side-top-widget-area',
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title subSection-title">',
			'after_title' => '</h1>',
		) );
		register_sidebar( array(
			'name' => 'Sidebar('.__( 'Common top', 'bvII' ).')',
			'id' => 'common-side-top-widget-area',
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title subSection-title">',
			'after_title' => '</h1>',
		) );
		register_sidebar( array(
			'name' => 'Sidebar('.__( 'Common bottom', 'bvII' ).')',
			'id' => 'common-side-bottom-widget-area',
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title subSection-title">',
			'after_title' => '</h1>',
		) );
		register_sidebar( array(
			'name' => 'Sidebar('.__( 'Post contents', 'bvII' ).')',
			'id' => 'post-side-widget-area',
			'description' => __( 'This widget area appears on the post contents page only.', 'bvII' ),
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h1 class="widget-title subSection-title">',
			'after_title' => '</h1>',
		) );

	// footer upper widget area

		register_sidebar( array(
			'name' => __( 'Widget area of upper footer', 'bvII' ),
			'id' => 'footer-upper-widget-1',
			// 'description' => __( 'This widget area appears on the post contents page only.', 'bvII' ),
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		) );

	// footer widget area
	for ( $i = 1; $i <= 3 ;) {
		register_sidebar( array(
			'name' => __( 'Footer widget area ', 'bvII' ).$i,
			'id' => 'footer-widget-'.$i,
			// 'description' => __( 'This widget area appears on the post contents page only.', 'bvII' ),
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget' => '</aside>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		) );
		$i++;
	}
}
add_action( 'widgets_init', 'bvII_widgets_init' );


/*-------------------------------------------*/
/*	Year Artchive list 'year' and count insert to inner </a>
/*-------------------------------------------*/
function bvII_archives_link($html){
  return preg_replace('@</a>(.+?)</li>@', '\1</a></li>', $html);
}
add_filter('get_archives_link', 'bvII_archives_link');

/*-------------------------------------------*/
/*	Category list 'count insert to inner </a>
/*-------------------------------------------*/
function bvII_list_categories( $output, $args ) {
	$output = preg_replace('/<\/a>\s*\((\d+)\)/',' ($1)</a>',$output);
	return $output;
}
add_filter( 'wp_list_categories', 'bvII_list_categories', 10, 2 );