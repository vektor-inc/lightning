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
			add_action( 'wp_head', array( __CLASS__ , 'get_html_start' ), 0 );
			add_action( 'wp_footer', array( __CLASS__ , 'get_html_end' ), 9999 );
		}

		public static function get_html_start() {
			ob_start( array( 'VK_CSS_Tree_Shaking', 'css_tree_shaking' ) );
		}

		public static function get_html_end() {
			ob_end_flush();
		}

		public static function css_tree_shaking( $buffer ) {
			require_once dirname( __FILE__ ) . '/class-css-tree-shaking.php';
			$skin_info = Lightning_Design_Manager::get_current_skin();
			$skin_css_url = '';
			$bs4_css_url = '';
			if ( ! empty( $skin_info['css_path'] ) ) {
				$skin_css_url = $skin_info['css_path'];
			}
			if ( 'bs4' === $skin_info['bootstrap'] ) {
				$bs4_css_url = get_template_directory_uri() . '/library/bootstrap-4/css/bootstrap.min.css';
			}
			// global $vk_css_tree_shaking_array;
			// foreach ( $vk_css_tree_shaking_array as $vk_css_tree_shaking[$i] ) {
				$css = file_get_contents( $bs4_css_url, true );
				$css = celtislab\CSS_tree_shaking::extended_minify($css, $buffer);
				$buffer = str_replace( '<link rel=\'stylesheet\' id=\'bootstrap-4-style-css\'  href=\'http://develop.local/wp-content/themes/Lightning/library/bootstrap-4/css/bootstrap.min.css?ver=4.3.1\' type=\'text/css\' media=\'all\' />', '<style id=\'bootstrap-4-style-inline-css\' type=\'text/css\'>' . $css . '</style>', $buffer );
			//	$i++;
			// }
			return $buffer;
		}

	}
	new VK_CSS_Tree_Shaking();
}