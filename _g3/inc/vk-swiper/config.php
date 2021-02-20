<?php
/**
 * VK Swiper Config
 *
 * @package Katawara
 */

if ( ! class_exists( 'VK_Swiper' ) ) {
	global $vk_swiper_url;
	$vk_swiper_url = get_parent_theme_file_uri( 'inc/vk-swiper/package' );
	require_once dirname( __FILE__ ) . '/package/class-vk-swiper.php';
}

