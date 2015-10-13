<?php
/*-------------------------------------------*/
/*	Set tag weight
/*-------------------------------------------*/
$page_for_posts = lightning_get_page_for_posts();
// Use post top page（ Archive title wrap to div ）
if ( $page_for_posts['post_top_use'] ) {
	if ( is_home() || is_page() || is_attachment() || is_search() || is_404() ) {
		$pageTitTag = 'h1';
	} else if ( is_category() || is_tag() || is_author() || is_tax() || is_archive() || is_single() ) {
		$pageTitTag = 'div';
	}
// Don't use post top（　Archive title wrap to h1　）
} else {
	if ( !is_single() ) {
		$pageTitTag = 'h1';
	} else {
		$pageTitTag = 'div';
	}
}

/*-------------------------------------------*/
/*	Set wrap tags
/*-------------------------------------------*/
$pageTitHtml_before = '<div class="section page-header"><div class="container"><div class="row"><div class="col-md-12">'."\n";
$pageTitHtml_before .= '<'.$pageTitTag.' class="page-header_pageTitle">'."\n";
$pageTitHtml_after = '</'.$pageTitTag.'>'."\n";
$pageTitHtml_after .= '</div></div></div></div><!-- [ /.page-header ] -->'."\n";

/*-------------------------------------------*/
/*	Set display title name
/*-------------------------------------------*/
$pageTitle = '';
if (is_page() || is_attachment()) {
	$pageTitle = get_the_title();
} else if ( is_category() || is_tag() || is_tax() || is_home() || is_author() || is_archive() || is_single() ) {
	$page_for_posts = lightning_get_page_for_posts();
	// Case of use post top page
	if ( $page_for_posts['post_top_use'] ) {
		// get post type
		$postType = get_post_type();
		// Case of post content
		// It is nesessary to is_category() & is_tag() & is_home() when no posts.
		if ( $postType == 'post' || is_category() || is_tag() || is_home() ) {
			$pageTitle = $page_for_posts['post_top_name'];
		// If post type isn't post,  get post type name.
		} else {
			// Can get post_type sucusessfully
			if ( $postType ) {
				$pageTitle = get_post_type_object($postType)->labels->name;
			} elseif( is_archive() ) {
				global $wp_query;
				$pageTitle = $wp_query->queried_object->label;
			}
		} 
	}

	// Case of don't use post top page
	else {
		if ( is_single() ){

			$taxonomies = get_the_taxonomies();

			if ($taxonomies){
				$taxonomy = key( $taxonomies );
				$taxo_cates  = get_the_terms( get_the_ID(),$taxonomy );

				$pageTitle	= esc_html($taxo_cates[0]->name);
			}

		} else {
			$pageTitle = get_the_archive_title();
		}
	}
} else if (is_search()) {
	$pageTitle = sprintf(__('Search Results for : %s', 'lightning'),get_search_query());
} else if (is_404()){
	$pageTitle = __('Not found', 'lightning');
}
$pageTitle = apply_filters( 'lightning_pageTitCustom', $pageTitle );

/*-------------------------------------------*/
/*	print
/*-------------------------------------------*/
$pageTitHtml = $pageTitHtml_before;
// allow tags
$allowed_html = array(
    'i' => array(
    		'class' => array (),
    	),
    'br' => array(),
    'strong' => array()
);
$pageTitHtml .= wp_kses($pageTitle,$allowed_html);
$pageTitHtml .= $pageTitHtml_after;
$pageTitHtml = apply_filters( 'lightning_pageTitHtml', $pageTitHtml );
echo $pageTitHtml;