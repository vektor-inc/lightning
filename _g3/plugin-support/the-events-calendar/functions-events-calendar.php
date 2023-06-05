<?php
/**
 * Lightning The Events Calendar Support
 *
 * @package vektor-inc/lightning
 */

use VektorInc\VK_Helpers\VkHelpers;

/**
 * Load header
 */
function lightning_g3_tec_load_header() {
	$vk_helpers     = new VkHelpers();
	$post_type_info = $vk_helpers->get_post_type_info();
	if ( 'tribe_events' === $post_type_info['slug'] ) {
		lightning_get_template_part( 'template-parts/site-header' );
	}
}
add_action( 'wp_body_open', 'lightning_g3_tec_load_header' );

/**
 * Load footer
 */
function lightning_g3_tec_load_footer() {
	$vk_helpers     = new VkHelpers();
	$post_type_info = $vk_helpers->get_post_type_info();
	if ( 'tribe_events' === $post_type_info['slug'] ) {
		lightning_get_template_part( 'template-parts/site-footer' );
	}
}
add_action( 'wp_footer', 'lightning_g3_tec_load_footer', 1 );

/**
 * Load CSS
 */
function lightning_tec_load_css() {
	wp_enqueue_style( 'lightning-tec-extension-style', get_template_directory_uri() . '/plugin-support/the-events-calendar/css/style.css', array(), LIGHTNING_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'lightning_tec_load_css' );
