<?php
/**
 * VK CSS Optimize Config
 *
 * @package vektor-inc/lightning
 */

use VektorInc\VK_CSS_Optimize\VkCssOptimize;
new VkCssOptimize();

global $prefix_customize_panel;
$prefix_customize_panel = lightning_get_prefix_customize_panel();

/**
 * Remove the Tree Shaking controls from the Customizer.
 *
 * Tree Shaking は使わない方針のため、ライブラリが登録した設定欄を外す。
 * ライブラリの customize_register（優先度 10）より後に実行する必要があるため、優先度を 20 にしている。
 * Preload の見出し・設定欄は残す。
 *
 * @param WP_Customize_Manager $wp_customize Customizer manager.
 */
function lightning_css_optimize_remove_tree_shaking_controls( $wp_customize ) {
	$tree_shaking_ids = array(
		'tree_shaking_title',
		'vk_css_optimize_options[tree_shaking]',
		'vk_css_optimize_options[tree_shaking_class_exclude]',
	);
	foreach ( $tree_shaking_ids as $tree_shaking_id ) {
		$wp_customize->remove_control( $tree_shaking_id );
		$wp_customize->remove_setting( $tree_shaking_id );
	}
}
add_action( 'customize_register', 'lightning_css_optimize_remove_tree_shaking_controls', 20 );

/**
 * CSS Optimize option default
 *
 * @param array $vk_css_optimize_options_default : receive array.
 * @return array $vk_css_optimize_options_default : return modified array.
 */
function lightning_css_optimize_options_default( $vk_css_optimize_options_default ) {
	$vk_css_optimize_options_default = array(
		'preload' => '',
	);
	return $vk_css_optimize_options_default;
}
add_filter( 'vk_css_optimize_options_default', 'lightning_css_optimize_options_default' );
