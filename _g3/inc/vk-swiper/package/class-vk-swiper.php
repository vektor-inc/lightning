<?php
/**
 * VK Swiper
 *
 * @package Katawara
 */

if ( ! class_exists( 'VK_Swiper' ) ) {

	// Set version number.
	define( 'SWIPER_VERSION', '5.4.5' );

	/**
	 * VK Swiper
	 */
	class VK_Swiper {
		/**
		 * Init
		 */
		public static function init() {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_swiper' ) );
		}

		/**
		 * Load Swiper
		 */
		public static function load_swiper() {
			global $vk_swiper_url;
			wp_enqueue_style( 'vk-swiper-style', $vk_swiper_url . '/assets/css/swiper.min.css', array(), SWIPER_VERSION );
			wp_enqueue_script( 'vk-swiper-script', $vk_swiper_url . '/assets/js/swiper.min.js', array(), SWIPER_VERSION, true );
		}
	}
	VK_Swiper::init();
}
