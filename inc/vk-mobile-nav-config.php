<?php

/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Mobile_Nav' ) ) {
	require_once( 'vk-mobile-nav/class-vk-mobile-nav.php' );

	global $default_nav;
	$default_nav = 'Header';

	global $library_url;
	$library_url = get_template_directory_uri() . '/inc/vk-mobile-nav/';

	// Default Vk Mobile Nav HTML was exported to footer.
	// But Originally it is desirable to output with a header
	remove_action( 'wp_footer', array( 'Vk_Mobile_Nav', 'menu_set_html' ) );
	add_action( 'lightning_header_before', array( 'Vk_Mobile_Nav', 'menu_set_html' ) );
}
