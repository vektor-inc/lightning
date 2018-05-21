<?php

/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {
	require_once( 'font-awesome/class-vk-font-awesome-versions.php' );

	global $font_awesome_directory_uri;
	$font_awesome_directory_uri = get_template_directory_uri() . '/inc/font-awesome/';

	global $vk_font_awesome_version_textdomain;
	$vk_font_awesome_version_textdomain = 'lightning';
}
