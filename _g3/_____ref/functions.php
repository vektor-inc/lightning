<?php

$current_skin = get_option( 'lightning_design_skin' );
if ( $current_skin === 'origin3' ){
	require get_parent_theme_file_path( '/_g3/functions.php' );
	return;
}


define( 'LIGHTNING_SHORT_NAME', 'LTG THEME' );
/*
  Theme setup
/*
  Load JS
/*
  Load CSS
/*
  Load Theme Customizer additions.
/*
  Load Custom template tags for this theme.
/*
  Load widgets
/*
  Load designskin manager
/*
  Load tga(Plugin install)
/*
  Load Front PR Blocks
/*
  WidgetArea initiate
/*
  Year Artchive list 'year' and count insert to inner </a>
/*
  Category list 'count insert to inner </a>
/*
  Global navigation add cptions
/*
  headfix enable
/*
  Tag Cloud _ Change font size
/*
  HOME _ Default content hidden
/*
  Move jQuery to footer
/*
  disable_tgm_notification_except_admin
/*
  Add defer first aid
/*-------------------------------------------*/


/*
  Theme setup
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'lightning_theme_setup' );
function lightning_theme_setup() {

	get_option();

	global $content_width;


	set_post_thumbnail_size( 320, 180, true );


	load_theme_textdomain( 'lightning', get_template_directory() . '/languages' );

	/*
	  Set content width
	/* 	(Auto set up to media max with.)
	/*-------------------------------------------*/
	if ( ! isset( $content_width ) ) {
		$content_width = 1140;
	}

	/*
	  Option init
	/*-------------------------------------------*/
	/*
	Save default option first time.
	When only customize default that, Can't save default value.
	*/
	$theme_options_default = lightning_theme_options_default();
	if ( ! get_option( 'lightning_theme_options' ) ) {
		add_option( 'lightning_theme_options', $theme_options_default );
		$lightning_theme_options = $theme_options_default;
	}

}







require get_parent_theme_file_path( '/functions-compatible.php' );


/*
  Load tga(Plugin install)
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/tgm-plugin-activation/tgm-config.php' );

/*
  Load Theme Customizer additions.
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/customize/customize.php' );
require get_parent_theme_file_path( '/inc/customize/customize-design.php' );
require get_parent_theme_file_path( '/inc/customize/customize-top-slide.php' );
require get_parent_theme_file_path( '/inc/customize/customize-functions.php' );

/*
  Load allow customize modules
/*-------------------------------------------*/
get_template_part( 'inc/vk-mobile-nav/vk-mobile-nav-config' );

/*
  Load modules
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/package-manager.php' );
require get_parent_theme_file_path( '/inc/class-design-manager.php' );
require get_parent_theme_file_path( '/inc/font-awesome/font-awesome-config.php' );
require get_parent_theme_file_path( '/inc/term-color/term-color-config.php' );
require get_parent_theme_file_path( '/inc/vk-components/vk-components-config.php' );
require get_parent_theme_file_path( '/inc/template-redirect.php' );
require get_parent_theme_file_path( '/inc/layout-controller/layout-controller.php' );
require get_parent_theme_file_path( '/inc/vk-footer-customize/vk-footer-customize-config.php' );
require get_parent_theme_file_path( '/inc/vk-old-options-notice/vk-old-options-notice-config.php' );
require get_parent_theme_file_path( '/inc/vk-css-optimize/vk-css-optimize-config.php' );


/*
  Plugin support
/*-------------------------------------------*/
// Load woocommerce modules
if ( class_exists( 'woocommerce' ) ) {
	require get_parent_theme_file_path( '/plugin-support/woocommerce/functions-woo.php' );
}
// Load polylang modules
include_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( 'polylang/polylang.php' ) ) {
	require get_parent_theme_file_path( '/plugin-support/polylang/functions-polylang.php' );
}
if ( is_plugin_active( 'bbpress/bbpress.php' ) ) {
	require get_parent_theme_file_path( '/plugin-support/bbpress/functions-bbpress.php' );
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
  headfix enable
/*-------------------------------------------*/
add_filter( 'body_class', 'lightning_body_class' );
function lightning_body_class( $class ) {
	// header fix
	if ( apply_filters( 'lightning_headfix_enable', true ) ) {
		$class[] = 'headfix';
	}
	// header height changer
	if ( apply_filters( 'lightning_header_height_changer_enable', true ) ) {
		$class[] = 'header_height_changer';
	}
	return $class;
}

// lightning headfix disabel sample
/*
add_filter( 'lightning_headfix_enable', 'lightning_headfix_disabel');
function lightning_headfix_disabel(){
	return false;
}
*/

// lightning header height changer disabel sample
/*
add_filter( 'lightning_header_height_changer_enable', 'lightning_header_height_changer_disabel');
function lightning_header_height_changer_disabel(){
	return false;
}
*/

/*
  Tag Cloud _ Change font size
/*-------------------------------------------*/
function lightning_tag_cloud_filter( $args ) {
	$args['smallest'] = 10;
	$args['largest']  = 10;
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'lightning_tag_cloud_filter' );

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

/*
  embed card
/*-------------------------------------------*/

remove_action( 'embed_footer', 'print_embed_sharing_dialog' );

function lightning_embed_styles() {
	wp_enqueue_style( 'wp-oembed-embed', get_template_directory_uri() . '/assets/css/wp-embed.css' );
}
add_action( 'embed_head', 'lightning_embed_styles' );