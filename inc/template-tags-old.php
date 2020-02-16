<?php

function lightning_get_theme_name_customize_panel() {
	return lightning_get_prefix_customize_panel();
}

/**
 * lightning_is_frontpage_onecolumn() is already not used
 *
 * @return void
 */
function lightning_is_frontpage_onecolumn() {
	// global $lightning_theme_options;
	// $options          = $lightning_theme_options;
	// $options          = get_option( 'lightning_theme_options' );
	// $page_on_front_id = get_option( 'page_on_front' );

	// if ( isset( $options['top_sidebar_hidden'] ) && $options['top_sidebar_hidden'] ) {
	// 	return true;
	// }
	// if ( $page_on_front_id ) {
	// 	$template = get_post_meta( $page_on_front_id, '_wp_page_template', true );
	// 	if ( $template == 'page-onecolumn.php' ) {
	// 		return true;
	// 	}
	// }
	// return false;
}