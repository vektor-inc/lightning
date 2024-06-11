<?php

/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Mobile_Nav' ) ) {

	global $default_nav;
	$default_nav = 'Header';

	global $library_url;
	$library_url = get_template_directory_uri() . '/inc/vk-mobile-nav/package';

	global $vk_mobile_nav_inline_style_handle;
	$vk_mobile_nav_inline_style_handle = 'lightning-common-style';

	global $vk_mobile_nav_prefix;
	$vk_mobile_nav_prefix = lightning_get_prefix_customize_panel();

	global $vk_mobile_nav_priority;
	$vk_mobile_nav_priority = 550;

	// Original VK Mobile Nav was printed on wp_footer.
	// But it bring to problem on customize > widget screen that change to lgithning_site_footer_after
	function lightning_change_vk_mobile_nav_hook_point( $vk_mobile_nav_html_hook_point ){
		return 'lightning_footer_after';
	}
	add_filter( 'vk_mobile_nav_html_hook_point', 'lightning_change_vk_mobile_nav_hook_point' );

	require get_parent_theme_file_path( '/inc/vk-mobile-nav/package/class-vk-mobile-nav.php' );

	// vk-mobile-nav.js はもともと main.js に結合していたので、個別に読み込まないように remove していたが、
	// Safari 16 でモバイルナビが開かないため、再度個別に読み込むように 15.23.1 で変更（コメントアウト）
	// Originally, vk-mobile-nav.js was integrated into main.js and removed to avoid loading it individually. 
	// However, since the mobile navigation didn't open on Safari 16, it was changed in version 15.23.1 to load individually again (commented out).
	// remove_action( 'wp_enqueue_scripts', array( 'Vk_Mobile_Nav', 'add_script' ) );

	remove_action( 'wp_enqueue_scripts', array( 'Vk_Mobile_Nav', 'add_css' ) );
}
