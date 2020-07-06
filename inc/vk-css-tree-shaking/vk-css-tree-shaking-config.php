<?php
/**
 * VK CSS Tree Shaking Config
 *
 * @package Lightning
 */

if ( ! class_exists( 'VK_CSS_Tree_Shaking' ) ) {
	$skin_info = Lightning_Design_Manager::get_current_skin();

	$skin_css_url = ! empty( $skin_info['css_path'] ) ? $skin_info['css_path'] : '';
	$skin_version = ! empty( $skin_info['version'] ) ? $skin_info['version'] : '';

	$bs4_css_url = ( 'bs4' === $skin_info['bootstrap'] ) ? get_template_directory_uri() . '/library/bootstrap-4/css/bootstrap.min.css' : '';
	$bs4_version = ( 'bs4' === $skin_info['bootstrap'] ) ? '4.3.1' : '';

	// 表示位置の配列.
	global $vk_css_tree_shaking_array;
	$vk_css_tree_shaking_array = array(
		array(
			'id'      => 'lightning-common-style',
			'url'     => get_template_directory_uri() . '/assets/css/common.css',
			'version' => LIGHTNING_THEME_VERSION,
		),
	);
	if ( $bs4_css_url && $bs4_version ) {
		$add_array = array(
			'id'      => 'bootstrap-4-style',
			'url'     => $bs4_css_url,
			'version' => $bs4_version,
		);
		array_push( $vk_css_tree_shaking_array, $add_array );
	}
	if ( $skin_css_url && $skin_version ) {
		$add_array = array(
			'id'      => 'lightning-design-style',
			'url'     => $skin_css_url,
			'version' => $skin_version,
		);
		array_push( $vk_css_tree_shaking_array, $add_array );
	}
	require_once dirname( __FILE__ ) . '/package/class-vk-css-tree-shaking.php';
}
