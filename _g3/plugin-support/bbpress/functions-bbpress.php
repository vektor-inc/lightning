<?php
/**
 * bbPress Extension
 *
 * @package         Lightning
 */

function lightning_is_bbpress() {
	$body_class = get_body_class();
	if ( in_array( 'bbpress', $body_class ) ) {
		return true;
	}
}
function lightning_bbp_is_singular( $return ) {
	if ( bbp_is_single_user() || lightning_is_bbpress() ) {
		$return = true;
	}
	return $return;
}
add_filter( 'lightning_is_singular', 'lightning_bbp_is_singular' );

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
		$page_title = '<div class="page-header-title">' . __( 'User Profile', 'lightning' ) . '</div>';
	}
	return $page_title;
}
add_filter( 'lightning_page_header_title_html', 'lightning_bbp_page_header_title' );


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


/**
 * bbPressのユーザーページでだけ使える表示名
 * 
 * @return string
 */
function lightning_get_the_bbp_display_name() {
	global $wp_query;
	$display_name = '';
	if ( ! empty( $wp_query->query['bbp_user'] ) ){
		$user = get_user_by( 'login', $wp_query->query['bbp_user']);
		$display_name = $user->data->display_name;
	}
	return esc_html( $display_name );
}

function lightning_bbp_breadcrumb_array( $array ) {
	if ( bbp_is_single_user() ) {

		global $wp_query;
		$users = get_users( array( 'search' => $wp_query->query['bbp_user'] ) );
		foreach ( $users as $user ) {
			if ( $user->data->user_login === $wp_query->query['bbp_user'] ) {
				$display_name = $user->data->display_name;
			}
		}
		$array[] = array(
			'name'  => lightning_get_the_bbp_display_name(),
			'id'    => '',
			'url'   => '',
			'class' => '',
		);
	}
	return $array;

}
add_filter( 'vk_breadcrumb_array', 'lightning_bbp_breadcrumb_array' );

function lightning_bbp_hide_element( $return ) {
	if ( lightning_is_bbpress() ) {
		$post_type = get_post_type();
		if ( 'topic' === $post_type || 'forum' === $post_type ) {
			$return = false;
		}
	}
	return $return;
}
add_filter( 'lightning_is_next_prev', 'lightning_bbp_hide_element' );
add_filter( 'lightning_is_entry_header', 'lightning_bbp_hide_element' );

function lightning_bbp_get_displayed_user_field( $value, $field, $filter ) {
	if ( 'user_nicename' === $field ) {
		$value = lightning_get_the_bbp_display_name();
	}
	return $value;
}
add_filter( 'bbp_get_displayed_user_field', 'lightning_bbp_get_displayed_user_field', 10, 3 );
