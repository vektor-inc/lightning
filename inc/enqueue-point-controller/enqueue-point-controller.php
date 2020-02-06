<?php

add_action( 'after_setup_theme', 'lightning_change_enqueue_point_run_fulter',5 );
function lightning_change_enqueue_point_run_fulter(){
	// font awesome
	add_filter( 'vkfa_enqueue_point', 'lightning_change_enqueue_point_to_footer');
}
// add_filter( 'vkfa_enqueue_point', 'lightning_change_enqueue_point_to_footer');
function lightning_change_enqueue_point_to_footer( $enqueue_point ) {
	$enqueue_point = 'wp_footer';
	return $enqueue_point;
}