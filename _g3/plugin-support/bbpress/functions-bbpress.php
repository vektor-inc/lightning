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
  トピックの内容の前にトピックタイトル追加
/*-------------------------------------------*/
function lightning_bbp_add_topic_title() {
	$skin = get_option( 'lightning_design_skin' );
	echo '<div><h2>' . get_the_title() . '</h2></div>';
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
 * ユーザープロフィールページの投稿タイプ
 */
function lightning_bbp_get_post_type( $post_type ) {
	if ( bbp_is_single_user() ) {
		$post_type['slug'] = 'bbp_user';
	}
	return $post_type;
}
add_filter( 'vk_get_post_type_info', 'lightning_bbp_get_post_type' );