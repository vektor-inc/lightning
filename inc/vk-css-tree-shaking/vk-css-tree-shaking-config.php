<?php
/**
 * VK Campaign Text Config
 *
 * @package Lightning
 */
if ( ! class_exists( 'VK_CSS_Tree_Shaking' ) ) {

	// 表示位置の配列.
	global $vk_css_tree_shaking_array;
	$vk_css_tree_shaking_array = array(
		'lightning_common_style'          => array(
			'id' => array( 'lightning_common_style' ),
			'label'     => __( 'Header Before', 'lightning-pro' ),
		),
		'header_append'           => array(
			'hookpoint' => array( 'lightning_header_append' ),
			'label'     => __( 'Header After', 'lightning-pro' ),
		),
		'slide_page_header_after' => array(
			'hookpoint' => array( 'lightning_top_slide_after', 'lightning_breadcrumb_before' ),
			'label'     => __( 'Slide and Page Header After', 'lightning-pro' ),
		),
	);
}
