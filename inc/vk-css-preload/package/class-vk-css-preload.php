<?php
/**
 * VK CSS Preload
 *
 * @package Lightning
 */

if ( ! class_exists( 'VK_CSS_Preload' ) ) {
	/**
 	 * VK CSS Tree Shaking Class
 	 */
	class VK_CSS_Preload {

		public function __construct() {
			add_action( 'wp_head', array( __CLASS__, 'get_html_start' ), 0 );
			add_action( 'wp_footer', array( __CLASS__, 'get_html_end' ), 99999 );
		}

		public static function get_html_start() {
			ob_start( 'VK_CSS_Preload::css_preload' );
		}

		public static function get_html_end() {
			ob_end_flush();
		}

		public static function css_preload( $buffer ) {
				$buffer = str_replace(
					'link rel=\'stylesheet\'',
					'link rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"',
					$buffer
				);
			return $buffer;
		}

	}
	new VK_CSS_Preload();
}
