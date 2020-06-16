<?php
/**
 * VK CSS Tree Shaking
 *
 * @package Lightning
 */

/**
 * VK CSS Tree Shaking Class
 */
class VK_CSS_Tree_Shaking {

	public  function __construct() {
		add_action( 'wp_head', array( __CLASS__ , 'get_html_start' ), 0 );
		add_action( 'wp_footer', array( __CLASS__ , 'get_html_end' ), 9999 );
	}

	public static function get_html_start() {
		ob_start('VK_CSS_Tree_Shaking::css_tree_shaking');
	}

	public static function get_html_end() {
		ob_end_flush();
	}

	public static function css_tree_shaking( $buffer ) {


		return $buffer;
	}

}