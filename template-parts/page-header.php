<?php
/*-------------------------------------------*/
/*	Set tag weight
/*-------------------------------------------*/
$page_for_posts = lightning_get_page_for_posts();
$postType       = lightning_get_post_type();

// Use post top page（ Archive title wrap to div ）
if ( $page_for_posts['post_top_use'] ) {
	if ( is_category() || is_tag() || is_author() || is_tax() || is_single() || is_date() ) {
		$pageTitTag = 'div';
	} else {
		$pageTitTag = 'h1';
	}
	// Don't use post top（　Archive title wrap to h1　）
} else {
	if ( ! is_single() ) {
		$pageTitTag = 'h1';
	} else {
		$pageTitTag = 'div';
	}
}

/*-------------------------------------------*/
/*	Set wrap tags
/*-------------------------------------------*/
$pageTitHtml_before  = '<div class="section page-header"><div class="container"><div class="row"><div class="col-md-12">' . "\n";
$pageTitHtml_before .= '<' . $pageTitTag . ' class="page-header_pageTitle">' . "\n";
$pageTitHtml_after   = '</' . $pageTitTag . '>' . "\n";
$pageTitHtml_after  .= '</div></div></div></div><!-- [ /.page-header ] -->' . "\n";

/*-------------------------------------------*/
/*	Set display title name
/*-------------------------------------------*/
$pageTitle = '';

if ( is_search() ) {
	$pageTitle = sprintf( __( 'Search Results for : %s', 'lightning' ), get_search_query() );
} elseif ( ! empty( $wp_query->query_vars['bbp_search'] ) ) {
	$bbp_search = esc_html( urldecode( $wp_query->query_vars['bbp_search'] ) );
	$pageTitle  = sprintf( __( 'Search Results for : %s', 'lightning' ), $bbp_search );
} elseif ( is_404() ) {
	$pageTitle = __( 'Not found', 'lightning' );
} elseif ( is_category() || is_tag() || is_tax() || is_home() || is_author() || is_archive() || is_single() ) {

	// Case of post type == 'post'
	if ( $postType['slug'] == 'post' ) {
		// Case of use post top page
		if ( $page_for_posts['post_top_use'] ) {
			$pageTitle = $page_for_posts['post_top_name'];

			// Case of don't use post top page
		} else {

			if ( is_single() ) {

				$taxonomies = get_the_taxonomies();
				if ( $taxonomies ) {
					$taxonomy   = key( $taxonomies );
					$taxo_cates = get_the_terms( get_the_ID(), $taxonomy );
					$pageTitle  = esc_html( $taxo_cates[0]->name );
				} else {
					// Case of no category
					$pageTitle = $postType['name'];
				}
			} else {
				$pageTitle = get_the_archive_title();
			}
		} // if ( $page_for_posts['post_top_use'] ) {
		// Case of custom post type
	} else {
		$pageTitle = $postType['name'];
	}
} elseif ( is_page() || is_attachment() ) {
	$pageTitle = get_the_title();
}
$pageTitle = apply_filters( 'lightning_pageTitCustom', $pageTitle );

/*-------------------------------------------*/
/*	print
/*-------------------------------------------*/
$pageTitHtml = $pageTitHtml_before;
// allow tags
$allowed_html = array(
	'i'      => array(
		'class' => array(),
	),
	'br'     => array(),
	'strong' => array(),
);
$pageTitHtml .= wp_kses( $pageTitle, $allowed_html );
$pageTitHtml .= $pageTitHtml_after;
$pageTitHtml  = apply_filters( 'lightning_pageTitHtml', $pageTitHtml );
echo $pageTitHtml;
