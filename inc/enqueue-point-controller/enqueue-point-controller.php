<?php

add_action( 'after_setup_theme', 'lightning_change_enqueue_point_run_filter',5 );
function lightning_change_enqueue_point_run_filter(){

	// font awesome
	add_filter( 'vkfa_enqueue_point', 'lightning_change_enqueue_point_to_footer');

	// Mobile fix nav ( Mobile Fix Nav css is included in assets/common.scss (Pro version) )
	// add_filter( 'vk_mobile_fix_nav_enqueue_point', 'lightning_change_enqueue_point_to_footer');

	// vk blocks css
	add_filter( 'vkblocks_enqueue_point', 'lightning_change_enqueue_point_to_footer' );

}

function lightning_change_enqueue_point_to_footer( $enqueue_point ) {
	$enqueue_point = 'wp_footer';
	return $enqueue_point;
}