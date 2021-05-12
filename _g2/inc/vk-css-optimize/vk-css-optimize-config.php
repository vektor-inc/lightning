<?php
/**
 * VK CSS Tree Shaking Config
 *
 * @package Lightning
 */

global $prefix_customize_panel;
$prefix_customize_panel = lightning_get_prefix_customize_panel();

if ( ! class_exists( 'VK_CSS_Optimize' ) ) {
	require_once dirname( __FILE__ ) . '/package/class-vk-css-optimize.php';
}

function lightning_css_tree_shaking_array( $vk_css_tree_shaking_array ){

	$skin_info = Lightning_Design_Manager::get_current_skin();

	$bs4_css_url  = ( ! empty( $skin_info['bootstrap'] ) && 'bs4' === $skin_info['bootstrap'] ) ? get_template_directory_uri() . '/library/bootstrap-4/css/bootstrap.min.css' : '';
	$bs4_css_path = ( ! empty( $skin_info['bootstrap'] ) && 'bs4' === $skin_info['bootstrap'] ) ? get_parent_theme_file_path( '/library/bootstrap-4/css/bootstrap.min.css' ) : '';
	$bs4_version  = ( ! empty( $skin_info['bootstrap'] ) && 'bs4' === $skin_info['bootstrap'] ) ? '4.5.0' : '';

	if ( ! empty( $bs4_css_url ) && ! empty( $bs4_version ) ) {
		$vk_css_tree_shaking_array[] = array(
			'id'      => 'bootstrap-4-style',
			'url'     => $bs4_css_url,
			'path'    => $bs4_css_path,
			'version' => $bs4_version,
		);
	}

	$vk_css_tree_shaking_array[] = array(
		'id'      => 'lightning-common-style',
		'url'     => get_template_directory_uri() . '/assets/css/common.css',
		'path'    => get_parent_theme_file_path( '/assets/css/common.css' ),
		'version' => LIGHTNING_THEME_VERSION,
	);

	$skin_css_url  = ! empty( $skin_info['css_path'] ) ? $skin_info['css_path'] : '';
	$skin_css_path = ! empty( $skin_info['css_sv_path'] ) ? $skin_info['css_sv_path'] : '';
	$skin_version  = ! empty( $skin_info['version'] ) ? $skin_info['version'] : '';

	if ( ! empty( $skin_css_url ) && ! empty( $skin_version ) ) {
		$vk_css_tree_shaking_array[] = array(
			'id'      => 'lightning-design-style',
			'url'     => $skin_css_url,
			'path'    => $skin_css_path,
			'version' => $skin_version,
		);
	}
	return $vk_css_tree_shaking_array;
}
add_filter( 'vk_css_tree_shaking_array', 'lightning_css_tree_shaking_array' );


/**
 * CSS Tree Shaking Exclude
 *
 * @param array $inidata CSS Tree Shaking Exclude Paramator.
 */
function lightning_css_tree_shaking_exclude_class( $inidata ) {
	$exclude_classes_array = array(
		'customize-partial-edit-shortcut',
		'vk_post',
		'card',
		'card-noborder',
		'card-imageRound',
		'vk_post-col-xs-12',
		'vk_post-col-xs-6',
		'vk_post-col-xs-4',
		'vk_post-col-xs-3',
		'vk_post-col-xs-2',
		'vk_post-col-sm-12',
		'vk_post-col-sm-6',
		'vk_post-col-sm-4',
		'vk_post-col-sm-3',
		'vk_post-col-sm-2',
		'vk_post-col-lg-12',
		'vk_post-col-lg-6',
		'vk_post-col-lg-4',
		'vk_post-col-lg-3',
		'vk_post-col-lg-2',
		'vk_post-col-xl-12',
		'vk_post-col-xl-6',
		'vk_post-col-xl-4',
		'vk_post-col-xl-3',
		'vk_post-col-xl-2',
		'vk_post-btn-display',
	);
	$inidata['class']      = array_merge( $inidata['class'], $exclude_classes_array );

	return $inidata;
}
add_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );


function lightning_css_optimize_options_default( $vk_css_optimize_options_default ) {
	$vk_css_optimize_options_default = array(
		'tree_shaking'	=> 'active',
		'preload' 		=> '',
	);
	return $vk_css_optimize_options_default;
}
add_filter( 'vk_css_optimize_options_default', 'lightning_css_optimize_options_default' );