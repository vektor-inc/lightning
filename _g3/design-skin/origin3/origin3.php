<?php
/**
 * Load font function
 *
 * @return void
 */
function lightning_origin3_load_fonts() {
	$version = wp_get_theme( get_template() )->get( 'Version' );
	wp_enqueue_style( 'add_google_fonts_Lato', '//fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap&subset=japanese', false, $version );
	wp_enqueue_style( 'add_google_fonts_noto_sans', '//fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap&subset=japanese', false, $version );
}
add_action( 'wp_footer', 'lightning_origin3_load_fonts' );
add_action( 'admin_footer', 'lightning_origin3_load_fonts' );
