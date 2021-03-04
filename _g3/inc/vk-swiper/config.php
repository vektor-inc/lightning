<?php
/**
 * VK Swiper Config
 *
 * @package Lightning G3
 */

if ( ! class_exists( 'VK_Swiper' ) ) {

	/**
	 * Swiper is used only on the front page.
	 * If you do not use the slider on the front page, Swiper will not be loaded.
	 *  */ 
	$options = get_option( 'lightning_theme_options' );
	if ( 'hide' === isset( $options['top_slide_display'] ) ){
		return;
	}

	global $vk_swiper_url;
	$vk_swiper_url = get_parent_theme_file_uri( 'inc/vk-swiper/package' );
	require_once dirname( __FILE__ ) . '/package/class-vk-swiper.php';
}

