<?php

define( 'LIG_G3_DIR_PATH', get_parent_theme_file_path( '_g3/' ) );
define( 'LIG_G3_DIR', '_g3' );

$theme_opt = wp_get_theme( get_template() );

define( 'LIGHTNING_THEME_VERSION', $theme_opt->Version );

require get_parent_theme_file_path( LIG_G3_DIR . '/inc/class-vk-helpers.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/class-vk-description-walker.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/class-ltg-template-redirect.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/template-tags.php' );

require get_parent_theme_file_path( LIG_G3_DIR . '/inc/layout-controller/layout-controller.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/vk-breadcrumb/config.php' );


/*
  Load CSS
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'lightning_load_css_action' );
function lightning_load_css_action() {
	add_action( 'wp_enqueue_scripts', 'lightning_common_style' );
	add_action( 'wp_enqueue_scripts', 'lightning_theme_style' );
}

function lightning_common_style() {
	wp_enqueue_style( 'lightning-common-style', get_template_directory_uri() . '/assets/css/style.css', array(), LIGHTNING_THEME_VERSION );
}
function lightning_theme_style() {
	wp_enqueue_style( 'lightning-theme-style', get_stylesheet_uri(), array(), LIGHTNING_THEME_VERSION );
}