<?php
/**
 * VK CSS Optimize
 *
 * @package Lightning
 */

/**
 * VK CSS Tree Shaking Class
 */
if ( ! class_exists( 'VK_CSS_Optimize' ) ) {
	class VK_CSS_Optimize {

		public function __construct() {
			add_action( 'wp_head', array( __CLASS__, 'get_html_start' ), 0 );
			add_action( 'wp_footer', array( __CLASS__, 'get_html_end' ), 9999 );
			add_filter( 'css_tree_shaking_exclude', array( __CLASS__, 'css_tree_shaking_exclude' ) );
		}

		public static function css_tree_shaking_exclude( $inidata ) {
			$options = get_option( 'lightning_theme_options' );

			if ( ! empty( $options['tree_shaking_class_exclude'] ) ) {
				$exclude_clssses = explode( ',', $options['tree_shaking_class_exclude'] );
			}

			$inidata['class'] = $inidata['class'] + $exclude_clssses;

			return $inidata;
		}

		public static function get_html_start() {
			ob_start( 'VK_CSS_Optimize::css_optimize' );
		}

		public static function get_html_end() {
			ob_end_flush();
		}

		public static function css_optimize( $buffer ) {
			// CSS Tree Shaking.
			require_once dirname( __FILE__ ) . '/class-css-tree-shaking.php';
			global $vk_css_tree_shaking_array;
			foreach ( $vk_css_tree_shaking_array as $vk_css_array ) {
				$options['ssl']['verify_peer']      = false;
				$options['ssl']['verify_peer_name'] = false;
				$css                                = file_get_contents( $vk_css_array['url'], true, stream_context_create( $options ) );
				$css                                = celtislab\CSS_tree_shaking::extended_minify( $css, $buffer );
				$buffer                             = str_replace(
					'<link rel=\'stylesheet\' id=\'' . $vk_css_array['id'] . '-css\'  href=\'' . $vk_css_array['url'] . '?ver=' . $vk_css_array['version'] . '\' type=\'text/css\' media=\'all\' />',
					'<style id=\'' . $vk_css_array['id'] . '-css\' type=\'text/css\'>' . $css . '</style>',
					$buffer
				);

			}

			// CSS Preload.
			$buffer = str_replace(
				'link rel=\'stylesheet\'',
				'link rel="preload" as="style" onload="this.onload=null;this.rel=\'stylesheet\'"',
				$buffer
			);

			return $buffer;
		}

	}
	new VK_CSS_Optimize();
}
