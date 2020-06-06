<?php
/**
 * BreadCrumb Template of Lightning
 *
 * @package Lightning
 */

/**
 * Lightning BreadCrumb
 */
function lightning_bread_crumb() {

	global $wp_query;

	// Get Post type info.
	$the_post_type = lightning_get_post_type();

	// Get Post top page info.
	$page_for_posts = lightning_get_page_for_posts();

	/*
	Microdata.
	http://schema.org/BreadcrumbList
	*/
	$microdata_li        = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
	$microdata_li_a      = ' itemprop="item"';
	$microdata_li_a_span = ' itemprop="name"';

	$breadclumb_html  = '<!-- [ .breadSection ] -->';
	$breadclumb_html .= '<div class="section breadSection">';
	$breadclumb_html .= '<div class="container">';
	$breadclumb_html .= '<div class="row">';
	$breadclumb_html .= '<ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList">';

	$breadclumb_html .= '<li id="panHome"' . $microdata_li . '><a' . $microdata_li_a . ' href="' . home_url( '/' ) . '"><span' . $microdata_li_a_span . '><i class="fa fa-home"></i> HOME</span></a></li>';

	// Post type.
	if ( is_archive() || ( is_single() && ! is_attachment() ) ) {

		if ( 'post' === $the_post_type['slug'] || is_category() || is_tag() ) { /* including single-post */
			if ( $page_for_posts['post_top_use'] ) {
				if ( ! is_home() ) {
					$breadclumb_html .= '<li' . $microdata_li . '><a' . $microdata_li_a . ' href="' . esc_url( $the_post_type['url'] ) . '"><span' . $microdata_li_a_span . '>' . $the_post_type['name'] . '</span></a></li>';
				} else {
					$breadclumb_html .= '<li><span>' . get_the_title( '', '', false ) . '</span></li>';
				}
			}
		} else {
			if ( is_single() || is_date() || is_tax() || is_author() ) {
				$breadclumb_html .= '<li' . $microdata_li . '><a' . $microdata_li_a . ' href="' . esc_url( $the_post_type['url'] ) . '"><span' . $microdata_li_a_span . '>' . $the_post_type['name'] . '</span></a></li>';
			} else {
				$breadclumb_html .= '<li><span>' . $the_post_type['name'] . '</span></li>';
			}
		}
	}

	if ( is_home() ) {

		// When use to post top page.
		// When "is_page()" that post top is don't display.
		if ( isset( $the_post_type['name'] ) && $the_post_type['name'] ) {
			$breadclumb_html .= '<li><span>' . $the_post_type['name'] . '</span></li>';
		}
	} elseif ( is_category() ) {

		// Category.
		// Get category information & insert to $cat.
		$cat = get_queried_object();

		// parent != 0  >>>  Parent exist.
		if ( 0 !== $cat->parent ) {
			// 祖先のカテゴリー情報を逆順で取得.
			$ancestors = array_reverse( get_ancestors( $cat->cat_ID, 'category' ) );
			// 祖先階層の配列回数分ループ.
			foreach ( $ancestors as $ancestor ) {
				$breadclumb_html .= '<li' . $microdata_li . '><a' . $microdata_li_a . ' href="' . get_category_link( $ancestor ) . '"><span' . $microdata_li_a_span . '>' . esc_html( get_cat_name( $ancestor ) ) . '</span></a></li>';
			}
		}
		$breadclumb_html .= '<li><span>' . $cat->cat_name . '</span></li>';

	} elseif ( is_tag() ) {

		// Tag.
		$tag_title        = single_tag_title( '', false );
		$breadclumb_html .= '<li><span>' . $tag_title . '</span></li>';

	} elseif ( is_tax() ) {

		// Term.
		$now_term        = $wp_query->queried_object->term_id;
		$now_term_parent = $wp_query->queried_object->parent;
		$now_taxonomy    = $wp_query->queried_object->taxonomy;

		// parent が !0 の場合 = 親カテゴリーが存在する場合.
		if ( 0 !== $now_term_parent ) {
			// 祖先のカテゴリー情報を逆順で取得.
			$ancestors = array_reverse( get_ancestors( $now_term, $now_taxonomy ) );
			// 祖先階層の配列回数分ループ.
			foreach ( $ancestors as $ancestor ) {
				$pan_term         = get_term( $ancestor, $now_taxonomy );
				$breadclumb_html .= '<li' . $microdata_li . '><a' . $microdata_li_a . ' href="' . get_term_link( $ancestor, $now_taxonomy ) . '"><span' . $microdata_li_a_span . '>' . esc_html( $pan_term->name ) . '</a></li>';
			}
		}

		$breadclumb_html .= '<li><span>' . esc_html( single_cat_title( '', '', false ) ) . '</span></li>';

	} elseif ( is_author() ) {

		// Author.
		$user_object      = get_queried_object();
		$breadclumb_html .= '<li><span>' . esc_html( $user_object->display_name ) . '</span></li>';

	} elseif ( is_archive() && ( ! is_category() || ! is_tax() ) ) {

		// Year / Month / Day.
		if ( is_date() ) {
			$breadclumb_html .= '<li><span>' . esc_html( get_the_archive_title() ) . '</span></li>';
		}
	} elseif ( is_single() ) {

		// Single.

		// Case of post.
		if ( 'post' === $the_post_type['slug'] ) {
			$category = get_the_category();
			if ( $category ) {
				// get parent category info.
				$parents = array_reverse( get_ancestors( $category[0]->term_id, 'category', 'taxonomy' ) );
				array_push( $parents, $category[0]->term_id );
				foreach ( $parents as $parent_term_id ) {
					$parent_obj       = get_term( $parent_term_id, 'category' );
					$term_url         = get_term_link( $parent_obj->term_id, $parent_obj->taxonomy );
					$breadclumb_html .= '<li' . $microdata_li . '><a' . $microdata_li_a . ' href="' . $term_url . '"><span' . $microdata_li_a_span . '>' . esc_html( $parent_obj->name ) . '</span></a></li>';
				}
			}

			// Case of custom post type.

		} else {
			$taxonomies = get_the_taxonomies();

			// To avoid WooCommerce default tax.
			foreach ( $taxonomies as $key => $value ) {
				if ( 'product_type' !== $key ) {
					$taxonomy = $key;
					break;
				}
			}

			if ( $taxonomies ) {
				$terms = get_the_terms( get_the_ID(), $taxonomy );

				// keeps only the first term (categ).
				$term = reset( $terms );
				if ( 0 !== $term->parent ) {

					// Get term ancestors info.
					$ancestors = array_reverse( get_ancestors( $term->term_id, $taxonomy ) );
					// Print loop term ancestors.
					foreach ( $ancestors as $ancestor ) {
						$pan_term         = get_term( $ancestor, $taxonomy );
						$breadclumb_html .= '<li' . $microdata_li . '><a' . $microdata_li_a . ' href="' . get_term_link( $ancestor, $taxonomy ) . '"><span' . $microdata_li_a_span . '>' . esc_html( $pan_term->name ) . '</span></a></li>';
					}
				}
				$term_url         = get_term_link( $term->term_id, $taxonomy );
				$breadclumb_html .= '<li' . $microdata_li . '><a' . $microdata_li_a . ' href="' . $term_url . '"><span' . $microdata_li_a_span . '>' . esc_html( $term->name ) . '</span></a></li>';
			}
		}
		$breadclumb_html .= '<li><span>' . get_the_title() . '</span></li>';

	} elseif ( is_page() ) {

		// Page.
		$post = $wp_query->get_queried_object();
		if ( 0 === $post->post_parent ) {
			$breadclumb_html .= '<li><span>' . get_the_title() . '</span></li>';
		} else {
			$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
			array_push( $ancestors, $post->ID );
			foreach ( $ancestors as $ancestor ) {
				if ( end( $ancestors ) !== $ancestor ) {
					$breadclumb_html .= '<li' . $microdata_li . '><a' . $microdata_li_a . ' href="' . get_permalink( $ancestor ) . '"><span' . $microdata_li_a_span . '>' . wp_strip_all_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) . '</span></a></li>';
				} else {
					$breadclumb_html .= '<li><span>' . wp_strip_all_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ) . '</span></li>';
				}
			}
		}
	} elseif ( is_404() ) {

		// 404.
		$breadclumb_html .= '<li><span>' . __( 'Not found', 'lightning' ) . '</span></li>';

	} elseif ( is_search() ) {

		// Search result.
		// translators: Search Result Text.
		$breadclumb_html .= '<li><span>' . sprintf( __( 'Search Results for : %s', 'lightning' ), get_search_query() ) . '</span></li>';

	} elseif ( is_attachment() ) {

		// Attachment.
		$breadclumb_html .= '<li><span>' . get_the_title( '', '', false ) . '</span></li>';

	}
	$breadclumb_html .= '</ol></div></div></div><!-- [ /.breadSection ] -->';
	return $breadclumb_html;
}
$breadclumb_html = lightning_bread_crumb();

$allowed_html    = array(
	'div'  => array(
		'id'        => array(),
		'class'     => array(),
		'itemprop'  => array(),
		'itemscope' => array(),
		'itemtype'  => array(),
	),
	'ol'   => array(
		'id'        => array(),
		'class'     => array(),
		'itemprop'  => array(),
		'itemscope' => array(),
		'itemtype'  => array(),
	),
	'li'   => array(
		'id'        => array(),
		'class'     => array(),
		'itemprop'  => array(),
		'itemscope' => array(),
		'itemtype'  => array(),
	),
	'a'    => array(
		'id'       => array(),
		'class'    => array(),
		'href'     => array(),
		'target'   => array(),
		'itemprop' => array(),
	),
	'span' => array(
		'id'        => array(),
		'class'     => array(),
		'itemprop'  => array(),
		'itemscope' => array(),
		'itemtype'  => array(),
	),
	'i'    => array(
		'id'    => array(),
		'class' => array(),
	),
);
$breadclumb_html = apply_filters( 'lightning_panListHtml', $breadclumb_html );
echo wp_kses( $breadclumb_html, $allowed_html );
