<?php

define( 'LIG_G3_DIR_PATH', get_parent_theme_file_path( '_g3/' ) );
define( 'LIG_G3_DIR', '_g3' );

$theme_opt = wp_get_theme( get_template() );

define( 'LIGHTNING_THEME_VERSION', $theme_opt->Version );

require get_parent_theme_file_path( LIG_G3_DIR . '/inc/class-vk-helpers.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/class-design-manager.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/class-vk-description-walker.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/class-ltg-template-redirect.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/template-tags.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/customize/customize.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/customize/customize-design.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/layout-controller/layout-controller.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/vk-components/config.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/vk-mobile-nav/config.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/vk-breadcrumb/config.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/widget-area.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/font-awesome/config.php' );

/*
  Theme setup
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'lightning_theme_setup' );
function lightning_theme_setup() {

	/*
	  Title tag
	/*-------------------------------------------*/
	add_theme_support( 'title-tag' );

	/*
	  editor-styles
	/*-------------------------------------------*/
	add_theme_support( 'editor-styles' );

	// When this support that printed front css and it's overwrite skin table style and so on
	// add_theme_support( 'wp-block-styles' );

    add_theme_support( 'align-wide' );

	/*
	  custom-background
	/*-------------------------------------------*/
	add_theme_support( 'custom-background' );

	// Block Editor line height @since WordPress 5.5
	add_theme_support( 'custom-line-height' );
	// Block Editor custom unit @since WordPress 5.5
	add_theme_support( 'custom-units', 'px', 'em', 'rem', 'vw', 'vh' );

	/*
	  cope with page excerpt
	/*-------------------------------------------*/
	add_post_type_support( 'page', 'excerpt' );

	/*
	  Admin page _ Eye catch
	/*-------------------------------------------*/
	add_theme_support( 'post-thumbnails' );

	/*
	  Custom menu
	/*-------------------------------------------*/
	register_nav_menus( array( 'global-nav' => 'Header Navigation' ) );
	register_nav_menus( array( 'footer-nav' => 'Footer Navigation' ) );

	/*
	  Add theme support for selective refresh for widgets.
	/*-------------------------------------------*/
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	  Feed Links
	/*-------------------------------------------*/
	add_theme_support( 'automatic-feed-links' );

	/*
	  WooCommerce
	/*-------------------------------------------*/
	add_theme_support( 'woocommerce' );


}


/*
  Load CSS
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'lightning_load_css_action' );
function lightning_load_css_action() {
	add_action( 'wp_enqueue_scripts', 'lightning_common_style' );
	add_action( 'wp_enqueue_scripts', 'lightning_theme_style' );
}

function lightning_common_style() {
	// wp_enqueue_style( 'lightning-common-style', get_template_directory_uri() . '/assets/css/style.css', array(), LIGHTNING_THEME_VERSION );
	wp_enqueue_style( 'lightning-common-style', get_template_directory_uri() . '/assets/css/style.css', array(), date("YmdHis") );
}
function lightning_theme_style() {
	wp_enqueue_style( 'lightning-theme-style', get_stylesheet_uri(), array(), LIGHTNING_THEME_VERSION );
}

/*
  Load Editor CSS
/*-------------------------------------------*/
// add_action( 'after_setup_theme', 'lightning_load_common_editor_css' );
function lightning_load_common_editor_css() {
	/*
	 Notice : Use url then if you use local environment https has error that bring to get css error and don't refrected */
	/* Notice : add_editor_style() is only one args. */
	add_editor_style( 'assets/css/editor.css' );
}

/*
Already add_editor_style() is used but reload css by wp_enqueue_style() reason is
use to wp_add_inline_style()
*/
add_action( 'enqueue_block_editor_assets', 'lightning_load_common_editor_css_to_gutenberg' );
function lightning_load_common_editor_css_to_gutenberg() {

	wp_enqueue_style(
		'lightning-common-editor-gutenberg',
		// If not full path that can't load in editor screen
		get_template_directory_uri() . '/assets/css/editor.css',
		array( 'wp-edit-blocks' ),
		LIGHTNING_THEME_VERSION
	);
}


/*
  Load JS
/*-------------------------------------------*/

add_action( 'wp_enqueue_scripts', 'lightning_addJs' );
function lightning_addJs() {
	wp_register_script( 'lightning-js', get_template_directory_uri() . '/assets/js/main.js', array(), LIGHTNING_THEME_VERSION, true );
	wp_localize_script( 'lightning-js', 'lightningOpt', apply_filters( 'lightning_localize_options', array() ) );
	wp_enqueue_script( 'lightning-js' );
	// jsのjQuery依存はもう無いが、一応追加しておく
	wp_enqueue_script( 'jquery' );
}

// fix global menu
add_filter( 'lightning_localize_options', 'lightning_global_nav_fix', 10, 1 );
function lightning_global_nav_fix( $options ) {
	$options['header_scrool'] = true;
	return $options;
}

add_action( 'wp_enqueue_scripts', 'lightning_comment_js' );
function lightning_comment_js() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

function lightning_load_fonts(){
	echo '<link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Noto+Sans+JP:wght@400;700&display=swap" rel="stylesheet">';
}
add_action( 'wp_head', 'lightning_load_fonts' );