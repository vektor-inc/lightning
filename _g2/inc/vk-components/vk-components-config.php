<?php
/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/

if ( ! class_exists( 'VK_Component_Button' ) ) {
	require get_parent_theme_file_path( '/inc/vk-components/package/class-vk-component-button.php' );
}
if ( ! class_exists( 'VK_Component_Mini_Contents' ) ) {
	require get_parent_theme_file_path( '/inc/vk-components/package/class-vk-component-mini-contents.php' );
}
if ( ! class_exists( 'VK_Component_Posts' ) ) {
	require get_parent_theme_file_path( '/inc/vk-components/package/class-vk-component-posts.php' );
}

global $vk_components_textdomain;
$vk_components_textdomain = 'lightning';
