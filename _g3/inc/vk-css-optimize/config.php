<?php
/**
 * VK CSS Tree Shaking Config
 *
 * @package Lightning
 */

use VektorInc\VK_CSS_Optimize\VkCssOptimize;
new VkCssOptimize();

global $prefix_customize_panel;
$prefix_customize_panel = lightning_get_prefix_customize_panel();

/**
 * Register tree shaking css handles
 *
 * ここに handle を足したら、_g3/tests/test-css-tree-shaking-exclude-pagination.php の
 * SHAKEN_CSS_FILES にも、その handle が読み込む CSS ファイルを足すこと。
 * テストはシェイキング対象のファイルを走査して、実行時に生成されるクラスの除外漏れを
 * 検出している。追加したファイルを載せ忘れると、そのファイルだけ検査されないまま
 * テストは緑のままになる（対象が減ったことに気付けない）。
 *
 * handle と生成ファイルの対応は enqueue のコードを読まないと取れないため、
 * テスト側からの自動導出はしていない。
 *
 * @param array $vk_css_tree_shaking_handles : recieve array.
 * @return array $vk_css_tree_shaking_handles : return modefied array.
 */
function lightning_css_tree_shaking_handles( $vk_css_tree_shaking_handles ) {

	$vk_css_tree_shaking_handles = array_merge(
		$vk_css_tree_shaking_handles,
		array(
			'lightning-common-style',
			'lightning-design-style',
		)
	);
	return $vk_css_tree_shaking_handles;
}
add_filter( 'vk_css_tree_shaking_handles', 'lightning_css_tree_shaking_handles' );


/**
 * CSS Tree Shaking Exclude
 *
 * @param array $inidata CSS Tree Shaking Exclude Paramator.
 */
function lightning_css_tree_shaking_exclude_class( $inidata ) {
	$exclude_classes_array = array(
		'customize-partial-edit-shortcut',
		'customize-partial-edit-shortcuts-shown',
		// Swiper v12 以降がスライダー初期化時に注入する矢印 SVG のクラス.
		// サーバー出力の HTML には存在しないため、除外しないとツリーシェイキングで矢印のスタイルが削除される.
		'swiper-navigation-icon',
		// Swiper がスライダー初期化時に生成するページネーションのドットのクラス.
		// サーバー出力の HTML はドットを含まない空の .swiper-pagination だけなので、
		// 除外しないとツリーシェイキングでドットのスタイルが削除される.
		// -active は現在位置を示すピルの指定で、ドットは全て同じ白・同じ形にしてあり
		// 現在位置の手がかりが形だけのため、消えると現在どのスライドかが判別できなくなる.
		// 無印は幅が変わるときのトランジションの指定で、消えると変化が一段跳びになる.
		'swiper-pagination-bullet',
		'swiper-pagination-bullet-active',
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
