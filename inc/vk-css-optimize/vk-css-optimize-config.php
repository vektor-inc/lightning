<?php
/**
 * VK CSS Tree Shaking Config
 *
 * @package Lightning
 */

require_once dirname( __FILE__ ) . '/vk-css-optimize-admin.php';

/**
 * Optimize CSS.
 */
function lightning_optimize_css() {
	$options = get_option( 'lightning_theme_options' );

	if ( ! isset( $options['optimize_css'] ) ){
		$options['optimize_css'] = 'minimal-bootstrap';
	}

	if ( ! empty( $options['optimize_css'] ) && 'optomize-all-css' === $options['optimize_css'] ) {

		if ( ! class_exists( 'VK_CSS_Optimize' ) ) {
			$skin_info = Lightning_Design_Manager::get_current_skin();
		
			$skin_css_url = ! empty( $skin_info['css_path'] ) ? $skin_info['css_path'] : '';
			$skin_css_path = ! empty( $skin_info['css_sv_path'] ) ? $skin_info['css_sv_path'] : '';
			$skin_version = ! empty( $skin_info['version'] ) ? $skin_info['version'] : '';
		
			$bs4_css_url = ( 'bs4' === $skin_info['bootstrap'] ) ? get_template_directory_uri() . '/library/bootstrap-4/css/bootstrap.min.css' : '';
			$bs4_css_path = ( 'bs4' === $skin_info['bootstrap'] ) ? get_parent_theme_file_path('/library/bootstrap-4/css/bootstrap.min.css') : '';
			$bs4_version = ( 'bs4' === $skin_info['bootstrap'] ) ? '4.5.0' : '';
		
			// 表示位置の配列.
			global $vk_css_tree_shaking_array;
			if ( empty( $vk_css_tree_shaking_array ) ) {
				$vk_css_tree_shaking_array = array(
					array(
						'id'      => 'lightning-common-style',
						'url'     => get_template_directory_uri() . '/assets/css/common.css',
						'path'    => get_parent_theme_file_path('/assets/css/common.css'),
						'version' => LIGHTNING_THEME_VERSION,
					),
				);
			} else {
				$add_array = array(
					'id'      => 'lightning-common-style',
					'url'     => get_template_directory_uri() . '/assets/css/common.css',
					'path'    => get_parent_theme_file_path('/assets/css/common.css'),
					'version' => LIGHTNING_THEME_VERSION,
				);
				array_push( $vk_css_tree_shaking_array, $add_array );
			}

			if ( $bs4_css_url && $bs4_version  ) {
				$add_array = array(
					'id'      => 'bootstrap-4-style',
					'url'     => $bs4_css_url,
					'path'    => $bs4_css_path,
					'version' => $bs4_version,
				);
				array_push( $vk_css_tree_shaking_array, $add_array );
			}
			if ( $skin_css_url && $skin_version ) {
				$add_array = array(
					'id'      => 'lightning-design-style',
					'url'     => $skin_css_url,
					'path'    => $skin_css_path,
					'version' => $skin_version,
				);
				array_push( $vk_css_tree_shaking_array, $add_array );
			}
			$vk_css_tree_shaking_array = apply_filters( 'vk_css_tree_shaking_array', $vk_css_tree_shaking_array );
			require_once dirname( __FILE__ ) . '/package/class-vk-css-optimize.php';
		}
	}
}
add_action( 'after_setup_theme', 'lightning_optimize_css' );