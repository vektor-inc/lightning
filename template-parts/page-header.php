<?php
/**
 * Page Header Template of Lightning
 *
 * @package Lightning
 */

// Set tag weight.
$page_for_posts = lightning_get_page_for_posts();
$the_post_type  = lightning_get_post_type();

// Use post top page（ Archive title wrap to div ）.
if ( $page_for_posts['post_top_use'] ) {
	if ( is_category() || is_tag() || is_author() || is_tax() || is_single() || is_date() ) {
		$page_title_tag = 'div';
	} else {
		$page_title_tag = 'h1';
	}
	// Don't use post top（　Archive title wrap to h1　）.
} else {
	if ( ! is_single() ) {
		$page_title_tag = 'h1';
	} else {
		$page_title_tag = 'div';
	}
}
// Set wrap tags.
$page_title_html_before  = '<div class="section page-header"><div class="container"><div class="row"><div class="col-md-12">' . "\n";
$page_title_html_before .= '<' . $page_title_tag . ' class="page-header_pageTitle">' . "\n";
$page_title_html_after   = '</' . $page_title_tag . '>' . "\n";
$page_title_html_after  .= '</div></div></div></div><!-- [ /.page-header ] -->' . "\n";

// Set display title name.
$page_title = '';

if ( is_search() ) {
	// translators: Title of Search Result Page.
	$page_title = sprintf( __( 'Search Results for : %s', 'lightning' ), get_search_query() );
} elseif ( ! empty( $wp_query->query_vars['bbp_search'] ) ) {
	$bbp_search = esc_html( urldecode( $wp_query->query_vars['bbp_search'] ) );
	// translators: Title of Search Result Page for bbpress.
	$page_title = sprintf( __( 'Search Results for : %s', 'lightning' ), $bbp_search );
} elseif ( is_404() ) {
	$page_title = __( 'Not found', 'lightning' );
} elseif ( is_category() || is_tag() || is_tax() || is_home() || is_author() || is_archive() || is_single() ) {

	// Case of post type == 'post'.
	if ( 'post' === $the_post_type['slug'] ) {
		// Case of use post top page.
		if ( $page_for_posts['post_top_use'] ) {
			$page_title = $page_for_posts['post_top_name'];

			// Case of don't use post top page.
		} else {

			if ( is_single() ) {

				$taxonomies = get_the_taxonomies();
				if ( $taxonomies ) {
					$the_taxonomy = key( $taxonomies );
					$taxo_cates   = get_the_terms( get_the_ID(), $the_taxonomy );
					$page_title   = esc_html( $taxo_cates[0]->name );
				} else {
					// Case of no category.
					$page_title = $the_post_type['name'];
				}
			} else {
				$page_title = get_the_archive_title();
			}
		}
		// Case of custom post type.
	} else {
		$page_title = $the_post_type['name'];
	}
} elseif ( is_page() || is_attachment() ) {
	$page_title = get_the_title();
}
$page_title = apply_filters( 'lightning_pageTitCustom', $page_title );

// print.
$page_title_html = $page_title_html_before;

// allow tags.
$allowed_html = array(
	'h1'     => array(
		'class' => array(),
	),
	'div'    => array(
		'class' => array(),
	),
	'i'      => array(
		'class' => array(),
	),
	'br'     => array(),
	'strong' => array(),
);

$page_title_html .= $page_title;
$page_title_html .= $page_title_html_after;
$page_title_html  = wp_kses( $page_title_html, $allowed_html );
$page_title_html  = apply_filters( 'lightning_pageTitHtml', $page_title_html );
echo $page_title_html;
