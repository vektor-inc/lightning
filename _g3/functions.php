<?php

$theme_opt = wp_get_theme( get_template() );

define( 'LIGHTNING_THEME_VERSION', $theme_opt->Version );

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
	$args = array(
		'default-color' => '#ffffff',
	);
	add_theme_support( 'custom-background', $args );

	// Block Editor line height @since WordPress 5.5
	add_theme_support( 'custom-line-height' );
	// Block Editor custom unit @since WordPress 5.5
	add_theme_support( 'custom-units', 'px', 'em', 'rem', 'vw', 'vh' );

	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name' => esc_attr__( 'Small', 'lightning' ),
				'size' => 12,
				'slug' => 'small',
			),
			array(
				'name' => esc_attr__( 'Regular', 'lightning' ),
				'size' => 16,
				'slug' => 'regular',
			),
			array(
				'name' => esc_attr__( 'Large', 'lightning' ),
				'size' => 18,
				'slug' => 'large',
			),
			array(
				'name' => esc_attr__( 'Huge', 'lightning' ),
				'size' => 21,
				'slug' => 'huge',
			),
		)
	);

	add_theme_support(
		'editor-gradient-presets',
		array(
			array(
				'name'     => esc_attr__( 'Vivid cyan blue to vivid purple', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%)',
				'slug'     => 'vivid-cyan-blue-to-vivid-purple',
			),
			array(
				'name'     => esc_attr__( 'Vivid green cyan to vivid cyan blue', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgba(0,208,132,1) 0%,rgba(6,147,227,1) 100%)',
				'slug'     => 'vivid-green-cyan-to-vivid-cyan-blue',
			),
			array(
				'name'     => esc_attr__( 'Light green cyan to vivid green cyan', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%)',
				'slug'     => 'light-green-cyan-to-vivid-green-cyan',
			),
			array(
				'name'     => esc_attr__( 'Luminous vivid amber to luminous vivid orange', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%)',
				'slug'     => 'luminous-vivid-amber-to-luminous-vivid-orange',
			),
			array(
				'name'     => esc_attr__( 'Luminous vivid orange to vivid red', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%)',
				'slug'     => 'luminous-vivid-orange-to-vivid-red',
			),
		)
	);

	add_theme_support( 'custom-spacing' );

	/*
	 cope with responsive-embeds
	/*-------------------------------------------*/
	add_theme_support( 'responsive-embeds' );

	/*
	  cope with page excerpt
	/*-------------------------------------------*/
	add_post_type_support( 'page', 'excerpt' );

	/*
	  Admin page _ Eye catch
	/*-------------------------------------------*/
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 320, 180, true );

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

	/*
	  Set content width
	/* 	(Auto set up to media max with.)
	/*-------------------------------------------*/
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 1140;
	}

	load_theme_textdomain( 'lightning', get_template_directory() . '/languages' );

}

require dirname( __FILE__ ) . '/inc/vk-helpers/config.php';
require dirname( __FILE__ ) . '/inc/class-design-manager.php';
require dirname( __FILE__ ) . '/inc/class-vk-description-walker.php';
require dirname( __FILE__ ) . '/inc/template-tags.php';
require dirname( __FILE__ ) . '/inc/customize/customize-design.php';
require dirname( __FILE__ ) . '/inc/layout-controller/layout-controller.php';
require dirname( __FILE__ ) . '/inc/vk-components/config.php';
require dirname( __FILE__ ) . '/inc/vk-mobile-nav/config.php';
require dirname( __FILE__ ) . '/inc/vk-breadcrumb/config.php';
require dirname( __FILE__ ) . '/inc/widget-area.php';
require dirname( __FILE__ ) . '/inc/term-color/config.php';
require dirname( __FILE__ ) . '/inc/vk-css-optimize/config.php';
require dirname( __FILE__ ) . '/inc/vk-swiper/config.php';
require dirname( __FILE__ ) . '/inc/ltg-g3-slider/config.php';

require dirname( __FILE__ ) . '/inc/starter-content.php';

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
	wp_enqueue_style( 'lightning-common-style', get_template_directory_uri() . '/assets/css/style.css', array(), LIGHTNING_THEME_VERSION );
}
function lightning_theme_style() {
	wp_enqueue_style( 'lightning-theme-style', get_stylesheet_uri(), array(), LIGHTNING_THEME_VERSION );
}

/*
  Load Editor CSS
/*-------------------------------------------*/
add_action( 'admin_enqueue_scripts', 'lightning_load_common_editor_css' );
function lightning_load_common_editor_css() {
	/*
	 Notice : Use url then if you use local environment https has error that bring to get css error and don't refrected */
	/* Notice : add_editor_style() is only one args. */
	/* add_editor_style is for Classic Editor Only. */
	global $post;
	if ( ! function_exists( 'use_block_editor_for_post' ) || ! use_block_editor_for_post( $post ) ) {
		add_editor_style( LIG_G3_DIR . '/assets/css/editor.css' );
	}
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
}

// fix global menu
add_filter( 'lightning_localize_options', 'lightning_global_nav_fix', 10, 1 );
function lightning_global_nav_fix( $options ) {
	$options['header_scrool']            = true;
	$options['add_header_offset_margin'] = true;
	return $options;
}

add_action( 'wp_enqueue_scripts', 'lightning_comment_js' );
function lightning_comment_js() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/*
  Year Artchive list 'year' and count insert to inner </a>
/*-------------------------------------------*/
function lightning_archives_link( $html ) {
	return preg_replace( '@</a>(.+?)</li>@', '\1</a></li>', $html );
}
add_filter( 'get_archives_link', 'lightning_archives_link' );

/*
  Category list count insert to inner </a>
/*-------------------------------------------*/
function lightning_list_categories( $output, $args ) {
	$output = preg_replace( '/<\/a>\s*\((\d+)\)/', ' ($1)</a>', $output );
	return $output;
}
add_filter( 'wp_list_categories', 'lightning_list_categories', 10, 2 );


/*
  Load embed card css
/*-------------------------------------------*/

remove_action( 'embed_footer', 'print_embed_sharing_dialog' );

function lightning_embed_styles() {
	wp_enqueue_style( 'wp-oembed-embed', get_template_directory_uri() . '/assets/css/wp-embed.css' );
}
add_action( 'embed_head', 'lightning_embed_styles' );

/*
  Plugin support
/*
-------------------------------------------*/
// Load woocommerce modules
if ( class_exists( 'woocommerce' ) ) {
	require dirname( __FILE__ ) . '/plugin-support/woocommerce/functions-woo.php';
}
// Load polylang modules
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( 'polylang/polylang.php' ) ) {
	require dirname( __FILE__ ) . '/plugin-support/polylang/functions-polylang.php';
}
if ( is_plugin_active( 'bbpress/bbpress.php' ) ) {
	require dirname( __FILE__ ) . '/plugin-support/bbpress/functions-bbpress.php';
}

/*
  disable_tgm_notification_except_admin
/*-------------------------------------------*/
add_action( 'admin_head', 'lightning_disable_tgm_notification_except_admin' );
function lightning_disable_tgm_notification_except_admin() {
	if ( ! current_user_can( 'administrator' ) ) {
		$allowed_html = array(
			'style' => array( 'type' => array() ),
		);
		$text         = '<style>#setting-error-tgmpa { display:none; }</style>';
		echo wp_kses( $text, $allowed_html );
	}
}
