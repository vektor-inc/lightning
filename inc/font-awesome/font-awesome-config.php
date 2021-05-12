<?php
/*
  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {
	require_once dirname( __FILE__ ) . '/package/class-vk-font-awesome-versions.php';

	global $font_awesome_directory_uri;
	$template                   = 'lightning';
	$theme_root_uri             = get_theme_root_uri( $template );
	$font_awesome_directory_uri = "$theme_root_uri/$template/inc/font-awesome/package/";

	global $vk_font_awesome_version_prefix_customize_panel;
	$vk_font_awesome_version_prefix_customize_panel = 'Lightning ';

	global $set_enqueue_handle_style;
	$set_enqueue_handle_style = 'lightning-design-style';

	global $vk_font_awesome_version_priority;
	$vk_font_awesome_version_priority = 560;

}
