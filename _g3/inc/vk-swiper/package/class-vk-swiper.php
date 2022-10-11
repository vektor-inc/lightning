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
	define( 'SWIPER_VERSION', '6.8.0' );

	/**
	 * VK Swiper
	 */
	class VK_Swiper {
		/**
		 * Init
		 */
		public static function init() {
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_swiper' ) );
			add_filter( 'vk_css_simple_minify_array', array( __CLASS__, 'css_simple_minify_array' ) );
		}

		/**
		 * Load Swiper
		 */
		public static function register_swiper() {
			global $vk_swiper_url;
			wp_register_style( 'vk-swiper-style', $vk_swiper_url . 'assets/css/swiper-bundle.min.css', array(), SWIPER_VERSION );
			wp_register_script( 'vk-swiper-script', $vk_swiper_url . 'assets/js/swiper-bundle.min.js', array(), SWIPER_VERSION, true );
		}

		/**
		 * Enque Swiper
		 * テーマなどの vk-swiper/config.php から必要に応じて読み込む
		 */
		public static function enqueue_swiper() {
			add_action(
				'wp_enqueue_scripts',
				function() {
					wp_enqueue_style( 'vk-swiper-style' );
					wp_enqueue_script( 'vk-swiper-script' );
				}
			);
		}

		/**
		 * Simple Minify Array
		 */
		public static function css_simple_minify_array( $vk_css_simple_minify_array ) {
			global $vk_swiper_url;
			global $vk_swiper_path;
			$vk_css_simple_minify_array[] = array(
				'id'      => 'vk-swiper-style',
				'url'     => $vk_swiper_url . 'assets/css/swiper-bundle.min.css',
				'path'    => $vk_swiper_path . 'assets/css/swiper-bundle.min.css',
				'version' => SWIPER_VERSION,
			);
			return $vk_css_simple_minify_array;

		}
	}
	VK_Swiper::init();
}

