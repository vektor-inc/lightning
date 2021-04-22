<?php
/**
 * VK Swiper
 *
 * @package VK Swiper
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
			add_filter( 'css_tree_shaking_exclude', array( __CLASS__, 'css_tree_shaking_exclude_class' ), 11 );
		}

		/**
		 * Load Swiper
		 */
		public static function load_swiper() {
			global $vk_swiper_url;
			wp_enqueue_style( 'vk-swiper-style', $vk_swiper_url . 'assets/css/swiper.min.css', array(), SWIPER_VERSION );
			wp_enqueue_script( 'vk-swiper-script', $vk_swiper_url . 'assets/js/swiper.min.js', array(), SWIPER_VERSION, true );
		}

		public static function css_tree_shaking_array( $vk_css_tree_shaking_array ){
			global $vk_swiper_url;
			global $vk_swiper_path;
			$vk_css_tree_shaking_array[] = array(
				'id'      => 'vk-swiper-style',
				'url'     => $vk_swiper_url . 'assets/css/swiper.min.css',
				'path'    => $vk_swiper_path . 'assets/css/swiper.min.css',
				'version' => SWIPER_VERSION,
			);
			return $vk_css_tree_shaking_array;
		}

		/**
		 * CSS Tree Shaking Exclude
		 *
		 * @param array $inidata CSS Tree Shaking Exclude Paramator.
		 */
		public static function css_tree_shaking_exclude_class( $inidata ) {
			$exclude_classes_array = array(
				'swiper-container-fade',
				'swiper-container-coverflow',
				'swiper-container-flip',
				'swiper-container-cube',
				'swiper-container-3d',
				'swiper-container-initialized',
				'swiper-container-horizontal',
				'swiper-slide',
				'swiper-slide-prev',
				'swiper-slide-next',
				'swiper-slide-active',
				'swiper-slide-duplicate',
				'swiper-slide-duplicate-prev',
				'swiper-slide-duplicate-next',
				'swiper-slide-duplicate-active',
			);
			$inidata['class']      = array_merge( $inidata['class'], $exclude_classes_array );

			return $inidata;
		}
	}
	VK_Swiper::init();
}

