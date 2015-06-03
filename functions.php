<?php
add_action( 'init', 'add_post_type_event', 0 );
function add_post_type_event() {
	register_post_type( 'event', // カスタム投稿タイプのスラッグ
		array(
			'labels' => array(
				'name' => 'イベント情報',
				'singular_name' => 'イベント情報'
			),
		'public' => true,
		'menu_position' =>5,
		'has_archive' => true,
		'supports' => array('title','editor','excerpt','thumbnail','author')
		)
	);
}
add_action( 'init', 'add_custom_taxonomy_event', 0 );
function add_custom_taxonomy_event() {
	register_taxonomy(
		'event-cat', // カテゴリーの識別子
		'event', // 対象の投稿タイプ
		array(
			'hierarchical' => true,
			'update_count_callback' => '_update_post_term_count',
			'label' => 'イベントカテゴリー',
			'singular_label' => 'イベント情報カテゴリー',
			'public' => true,
			'show_ui' => true,
		)
	);
}



/*-------------------------------------------*/
/*	Theme setup
/*-------------------------------------------*/
/*	Head logo
/*-------------------------------------------*/
/*	WidgetArea initiate



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
}

add_action('wp_head','bvII_addJs');
function bvII_addJs(){
	wp_register_script( 'bvII-js' , get_template_directory_uri().'/js/all.min.js', array('jquery'), '20150603a' );
	wp_enqueue_script( 'bvII-js' );
}

function biz_vektor_wp_css(){
	echo '<link rel="stylesheet" href="'.get_stylesheet_uri().'" type="text/css" media="all" />'."\n";
//	wp_enqueue_style('Biz_Vektor_style', get_stylesheet_uri(), array(), false);
}
add_action('wp_head', 'biz_vektor_wp_css', 190);

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
			'name' => 'Sidebar('.__( 'Common top', 'bvII' ).')',
			'id' => 'common-side-top-widget-area',
			'before_widget' => '<div class="widget %2$s" id="%1$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title subSection-title">',
			'after_title' => '</h3>',
		) );
		register_sidebar( array(
			'name' => 'Sidebar('.__( 'Common bottom', 'bvII' ).')',
			'id' => 'common-side-bottom-widget-area',
			'before_widget' => '<div class="widget %2$s" id="%1$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title subSection-title">',
			'after_title' => '</h3>',
		) );
		register_sidebar( array(
			'name' => 'Sidebar('.__( 'Post contents', 'bvII' ).')',
			'id' => 'post-side-widget-area',
			'description' => __( 'This widget area appears on the post contents page only.', 'bvII' ),
			'before_widget' => '<div class="widget %2$s" id="%1$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title subSection-title">',
			'after_title' => '</h3>',
		) );

	// footer upper widget area

		register_sidebar( array(
			'name' => __( 'Widget area of upper footer', 'bvII' ),
			'id' => 'footer-upper-widget-1',
			// 'description' => __( 'This widget area appears on the post contents page only.', 'bvII' ),
			'before_widget' => '<div class="widget %2$s" id="%1$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		) );

	// footer widget area
	for ( $i = 1; $i <= 3 ;) {
		register_sidebar( array(
			'name' => __( 'Footer widget area ', 'bvII' ).$i,
			'id' => 'footer-widget-'.$i,
			// 'description' => __( 'This widget area appears on the post contents page only.', 'bvII' ),
			'before_widget' => '<div class="widget %2$s" id="%1$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3>',
			'after_title' => '</h3>',
		) );
		$i++;
	}
}
add_action( 'widgets_init', 'bvII_widgets_init' );