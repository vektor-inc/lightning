<?php

/*-------------------------------------------*/
/*	Redirect new directory file
/*-------------------------------------------*/
/**
 * Case of old file name called by such as child theme.
 * Ex.a)
 *      Child theme index.php call get_template_part('get_template_part_module_loop_post');
 *      Child theme don't have module_loop_post.php
 * @param  string $slug [description]
 * @return [type]       [description]
 */
function lightning_change_module_loop_post( $slug ) {

	$templates[] = "{$slug}.php";

	// If old name file is don't exist that load new name file.
	if ( ! locate_template( $templates, false, false ) ) {
		$templates[] = 'template-parts/loop-post.php';
		locate_template( $templates, true, false );
	}
}
add_action( 'get_template_part_module_loop_post', 'lightning_change_module_loop_post' );
