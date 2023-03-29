<?php
/*
  Redirect new directory file
/*-------------------------------------------*/
/**
 * Case of old file name called by such as child theme.
 * Ex.a)
 *      Child theme index.php call get_template_part('get_template_part_module_loop_post');
 *      Child theme don't have module_loop_post.php
 *
 * @param  string $slug [description]
 * @return [type]       [description]
 */

add_action( 'after_setup_theme', 'lightning_redirect_module' );
function lightning_redirect_module() {

	// module_loop_***.php
	$postTypes = get_post_types( array( 'public' => true ) );
	foreach ( $postTypes as $postType ) {
		add_action(
			'get_template_part_module_loop_' . $postType,
			function( $slug ) {
				/**
				 * note: $slug is provided from do_action( "get_template_part_{$slug}", $slug, $name );
				 */
				$templates   = array();
				$templates[] = "{$slug}.php";
				// If old name file is don't exist that load new name file.
				if ( ! locate_template( $templates, false, false ) ) {
					get_template_part( 'template-parts/post/loop', get_post_type() );
				}
			}
		);
	} // foreach ( $postTypes as $postType ) {

	add_action(
		'get_template_part_module_loop_post_meta',
		function( $slug ) {
			$templates   = array();
			$templates[] = "{$slug}.php";
			if ( ! locate_template( $templates, false, false ) ) {
				get_template_part( 'template-parts/post/meta' );
			}
		}
	);

	add_action(
		'get_template_part_module_slide',
		function( $slug ) {
			$templates   = array();
			$templates[] = "{$slug}.php";
			if ( ! locate_template( $templates, false, false ) ) {
				get_template_part( 'template-parts/slide-bs3' );
			}
		}
	);

	add_action(
		'get_template_part_module_pageTit',
		function( $slug ) {
			$templates   = array();
			$templates[] = "{$slug}.php";
			if ( ! locate_template( $templates, false, false ) ) {
				get_template_part( 'template-parts/page-header' );
			}
		}
	);

	add_action(
		'get_template_part_module_panList',
		function( $slug ) {
			$templates   = array();
			$templates[] = "{$slug}.php";
			if ( ! locate_template( $templates, false, false ) ) {
				get_template_part( 'template-parts/breadcrumb' );
			}
		}
	);

}
