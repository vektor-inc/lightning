<?php
/**
 * VK Swiper
 *
 * @package Katawara
 */

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。
修正の際は上記リポジトリのデータを修正してください。
編集権限を持っていない方で何か修正要望などありましたら
各プラグインのリポジトリにプルリクエストで結構です。
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
			add_filter( 'vk_css_tree_shaking_array', array( __CLASS__, 'css_tree_shaking_array' ), 11 );
		}

		/**
		 * Load Swiper
		 */
		public static function load_swiper() {
			wp_enqueue_style( 'vk-swiper-style', plugin_dir_url( __FILE__ ) . 'assets/css/swiper.min.css', array(), SWIPER_VERSION );
			wp_enqueue_script( 'vk-swiper-script', plugin_dir_url( __FILE__ ) . 'assets/js/swiper.min.js', array(), SWIPER_VERSION, true );
		}

		public static function css_tree_shaking_array( $vk_css_tree_shaking_array ){
			$vk_css_tree_shaking_array[] = array(
				'id'      => 'vk-swiper-style',
				'url'     => plugin_dir_url( __FILE__ ) . 'assets/css/swiper.min.css',
				'path'    => plugin_dir_path( __FILE__ ) . 'assets/css/swiper.min.css',
				'version' => SWIPER_VERSION,
			);
			return $vk_css_tree_shaking_array;
		}
	}
	VK_Swiper::init();
}

