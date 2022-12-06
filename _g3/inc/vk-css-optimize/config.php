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

/**
 * Register tree shaking css
 *
 * @param array $vk_css_tree_shaking_array : recieve array.
 * @return array $vk_css_tree_shaking_array : return modefied array.
 */
function lightning_css_tree_shaking_array( $vk_css_tree_shaking_array ) {

	// Register common css.
	$vk_css_tree_shaking_array = array_merge(
		$vk_css_tree_shaking_array,
		array(
			'lightning-common-style',
			'lightning-design-style'
		)
	);

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
		'customize-partial-edit-shortcuts-shown',
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

/**
 * CSS Optimize option default
 *
 * @param array $vk_css_optimize_options_default : recieve array.
 * @return array $vk_css_optimize_options_default : return modefied array.
 */
function lightning_css_optimize_options_default( $vk_css_optimize_options_default ) {
	$vk_css_optimize_options_default = array(
		'tree_shaking' => 'active',
		'preload'      => '',
	);
	return $vk_css_optimize_options_default;
}
add_filter( 'vk_css_optimize_options_default', 'lightning_css_optimize_options_default' );
