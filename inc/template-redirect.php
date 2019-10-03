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

add_action( 'after_setup_theme', 'lightning_redirect_template_module_loop' );
function lightning_redirect_template_module_loop() {
	$postTypes = get_post_types( array( 'public' => true ) );
	foreach ( $postTypes as $postType ) {
		add_action(
			'get_template_part_module_loop_' . $postType, function( $slug ) {

				$templates[] = "{$slug}.php";

				// If old name file is don't exist that load new name file.
				if ( ! locate_template( $templates, false, false ) ) {
					get_template_part( 'template-parts/loop', get_post_type() );
					// $templates[] = 'template-parts/loop-post.php';
					// locate_template( $templates, true, false );
				}
			}
		);
	} // foreach ( $postTypes as $postType ) {
}
