<?php
/**
 * bbPress Extension
 *
 * @package         Lightning
 */


function lightning_bbpress_extension_deactive() {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( 'lightning-bbpress-extension/lightning-bbpress-extension.php' ) ) {
		$active_plugins = get_option( 'active_plugins' );
		$active_plugins = array_diff( $active_plugins, array( 'lightning-bbpress-extension/lightning-bbpress-extension.php' ) );
		$active_plugins = array_values( $active_plugins );
		update_option( 'active_plugins', $active_plugins );
	}
}
add_action( 'admin_init', 'lightning_bbpress_extension_deactive' );

/*
  CSS読み込み
/*-------------------------------------------*/
function lightning_bbp_load_css() {
	wp_enqueue_style( 'lightning-bbp-extension-style', get_template_directory_uri() . '/plugin-support/bbpress/css/style.css', array(), LIGHTNING_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'lightning_bbp_load_css' );

/*
  フォーラムのパンくずリスト書き換え
/*-------------------------------------------*/
add_filter(
	'lightning_panListHtml',
	function( $panListHtml ) {
		if ( function_exists( 'bbp_get_forum_post_type' ) ) {
			$postType = lightning_get_post_type();
			if ( $postType['slug'] == 'topic' ) {

				// Microdata
				// http://schema.org/BreadcrumbList
				/*-------------------------------------------*/
				$microdata_li = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';

				$before_html = '<!-- [ .breadSection ] -->
<div class="section breadSection">
<div class="container">
<div class="row">
<ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList">';

				$after_html = '</ol>
</div>
</div>
</div>
<!-- [ /.breadSection ] -->';

				$args        = array(
					// HTML
					'before'         => $before_html,
					'after'          => $after_html,
					'sep'            => '',
					'crumb_before'   => '<li' . $microdata_li . '><span>',
					'crumb_after'    => '</span></li>',
					'home_text'      => '<i class="fa fa-home"></i> HOME',
					'current_before' => '',
					'current_after'  => '',
				);
				$panListHtml = bbp_get_breadcrumb( $args );
			}
		}
		return $panListHtml;
	}
);

/*
  トピックの内容の前にトピックタイトル追加
/*-------------------------------------------*/
function lightning_bbp_add_topic_title() {
	$skin = get_option( 'lightning_design_skin' );
	if ( $skin != 'Variety' ) {
		echo '<div><h2>' . bbp_get_topic_title() . '</h2></div>';
	}
}
add_action( 'bbp_template_before_single_topic', 'lightning_bbp_add_topic_title' );

/**
 * ユーザープロフィールページのページヘッダー
 */
function lightning_bbp_page_header_title( $page_title ) {
	if ( bbp_is_single_user() ) {
		$page_title = __( 'User Profile', 'lightning' );
	}
	return $page_title;
}
add_filter( 'lightning_pageTitCustom', 'lightning_bbp_page_header_title' );

/**
 * ユーザープロフィールページのパンくず
 */
function lightning_bbp_breadcrumb( $breadcrumb_html ) {
	// Microdata
	// http://schema.org/BreadcrumbList
	/*-------------------------------------------*/
	$microdata_li        = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
	$microdata_li_a      = ' itemprop="item"';
	$microdata_li_a_span = ' itemprop="name"';
	if ( bbp_is_single_user() ) {

		// パンくず開始
		$breadcrumb_html  = '<!-- [ .breadSection ] -->';
		$breadcrumb_html .= '<div class="section breadSection">';
		$breadcrumb_html .= '<div class="container">';
		$breadcrumb_html .= '<div class="row">';
		$breadcrumb_html .= '<ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList">';

		// Home
		$breadcrumb_html .= '<li id="panHome"' . $microdata_li . '>';
		$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . home_url( '/' ) . '">';
		$breadcrumb_html .= '<span' . $microdata_li_a_span . '><i class="fa fa-home"></i> HOME</span>';
		$breadcrumb_html .= '</a>';
		$breadcrumb_html .= '</li>';

		// ユーザー名
		$breadcrumb_html .= '<li>';
		$breadcrumb_html .= '<span>' . bbp_get_displayed_user_field( 'display_name' ) . '</span>';
		$breadcrumb_html .= '</li>';

		// パンくず終了
		$breadcrumb_html .= '</ol>';
		$breadcrumb_html .= '</div>';
		$breadcrumb_html .= '</div>';
		$breadcrumb_html .= '</div>';
		$breadcrumb_html .= '<!-- [ /.breadSection ] -->';
	}
	return $breadcrumb_html;
}
add_filter( 'lightning_panListHtml', 'lightning_bbp_breadcrumb' );

/**
 * ユーザープロフィールページの投稿タイプ
 */
function lightning_bbp_get_post_type( $post_type ) {
	if ( bbp_is_single_user() ) {
		$post_type['slug'] = 'bbp_user';
	}
	return $post_type;
}
add_filter( 'lightning_postType_custom', 'lightning_bbp_get_post_type' );
