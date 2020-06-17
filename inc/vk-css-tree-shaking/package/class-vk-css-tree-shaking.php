<?php
/**
 * VK CSS Tree Shaking
 *
 * @package Lightning
 */

/**
 * VK CSS Tree Shaking Class
 */
if ( ! class_exists( 'VK_CSS_Tree_Shaking' ) ) {
	class VK_CSS_Tree_Shaking {

		public function __construct() {
			add_action( 'wp_head', array( __CLASS__, 'get_html_start' ), 0 );
			add_action( 'wp_footer', array( __CLASS__, 'get_html_end' ), 9999 );
		}

		public static function get_html_start() {
			ob_start( 'VK_CSS_Tree_Shaking::css_tree_shaking' );
		}

		public static function get_html_end() {
			ob_end_flush();
		}

		public static function css_tree_shaking( $buffer ) {
			require_once dirname( __FILE__ ) . '/class-css-tree-shaking.php';
			global $vk_css_tree_shaking_array;
			foreach ( $vk_css_tree_shaking_array as $vk_css_array  ) {

				$css    = file_get_contents( $vk_css_array['url'], true );
				$css    = celtislab\CSS_tree_shaking::extended_minify( $css, $buffer );
				$buffer = str_replace(
					'<link rel=\'stylesheet\' id=\'' . $vk_css_array['id'] . '-css\'  href=\'' . $vk_css_array['url'] . '?ver='. $vk_css_array['version'] . '\' type=\'text/css\' media=\'all\' />',
					'<style id=\'' . $vk_css_array['id'] . '-css\' type=\'text/css\'>' . $css . '</style>',
					$buffer
				);

			}

			return $buffer;
		}

	}
	new VK_CSS_Tree_Shaking();
}
