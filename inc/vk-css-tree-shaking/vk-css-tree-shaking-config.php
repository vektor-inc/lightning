<?php
/**
 * VK CSS Tree Shaking Config
 *
 * @package Lightning
 */

if ( ! class_exists( 'VK_CSS_Tree_Shaking' ) ) {
	$skin_info = Lightning_Design_Manager::get_current_skin();
	$skin_css_url = '';
	$bs4_css_url = '';
	if ( ! empty( $skin_info['css_path'] ) ) {
		$skin_css_url = $skin_info['css_path'];
	}
	if ( 'bs4' === $skin_info['bootstrap'] ) {
		$bs4_css_url = get_template_directory_uri() . '/library/bootstrap-4/css/bootstrap.min.css';
	}

	// 表示位置の配列.
	global $vk_css_tree_shaking_array;
	$vk_css_tree_shaking_array = array(
		array(
			'id'  => 'lightning_common_style',
			'url' => get_template_directory_uri() . '/assets/css/common.css',
		),
		array(
			'id'  => 'bootstrap-4-style',
			'url' => $bs4_css_url,
		),
		array(
			'id'  => 'lightning-design-style',
			'url' => $skin_css_url,
		),
	);
}
