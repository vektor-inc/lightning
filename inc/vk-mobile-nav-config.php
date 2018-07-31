<?php

/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Mobile_Nav' ) ) {
	require_once( 'vk-mobile-nav/class-vk-mobile-nav.php' );

	global $vk_mobile_nav_textdomain;
	$vk_mobile_nav_textdomain = 'lightning';

	global $default_nav;
	$default_nav = 'Header';

	global $library_url;
	$library_url = get_template_directory_uri() . '/inc/vk-mobile-nav/';

}
