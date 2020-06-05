<?php
/**
 * Sidebar Template of Lightning
 *
 * @package Lightning
 */

if ( is_active_sidebar( 'common-side-top-widget-area' ) ) {
	dynamic_sidebar( 'common-side-top-widget-area' );
}

if ( is_front_page() ) {
	if ( is_active_sidebar( 'front-side-top-widget-area' ) ) {
		dynamic_sidebar( 'front-side-top-widget-area' );
	}
} else {
	// Display post type widget area.
	$get_post_type     = lightning_get_post_type();
	$widdget_area_name = $get_post_type['slug'] . '-side-widget-area';
	if ( is_active_sidebar( $widdget_area_name ) ) {
		dynamic_sidebar( $widdget_area_name );
	}
}

if ( is_active_sidebar( 'common-side-bottom-widget-area' ) ) {
	dynamic_sidebar( 'common-side-bottom-widget-area' );
}
