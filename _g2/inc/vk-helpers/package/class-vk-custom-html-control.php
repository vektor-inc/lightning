<?php
/**
 * VK Custom HTML Control
 *
 * @package VK Helpers
 */

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。
修正の際は上記リポジトリのデータを修正してください。
編集権限を持っていない方で何か修正要望などありましたら
各プラグインのリポジトリにプルリクエストで結構です。
*/

if ( ! class_exists( 'VK_Custom_Html_Control' ) ) {
	class VK_Custom_Html_Control extends WP_Customize_Control {
		public $type             = 'customtext';
		public $custom_title_sub = ''; // we add this for the extra custom_html
		public $custom_html      = ''; // we add this for the extra custom_html
		public function render_content() {
			if ( $this->label ) {
				// echo '<h2 class="admin-custom-h2">' . wp_kses_post( $this->label ) . '</h2>';
				echo '<h2 class="admin-custom-h2">' . wp_kses_post( $this->label ) . '</h2>';
			}
			if ( $this->custom_title_sub ) {
				echo '<h3 class="admin-custom-h3">' . wp_kses_post( $this->custom_title_sub ) . '</h3>';
			}
			if ( $this->custom_html ) {
				echo '<div>' . wp_kses_post( $this->custom_html ) . '</div>';
			}
		} // public function render_content() {
	} // class Custom_Html_Control extends WP_Customize_Control {
}
