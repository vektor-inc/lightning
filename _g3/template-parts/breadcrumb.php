<?php

function lightning_bread_crumb() {
	/*-------------------------------------------*/
	/*  Lightning BreadCrumb
	/*-------------------------------------------*/

	global $wp_query;

	// Get Post top page info
	/*-------------------------------------------*/
	// get_post_type() だとtaxonomyページで該当の投稿がない時に投稿タイプを取得できないため VK_Helpers::get_post_type_info() を使用
	$post_type_info		= VK_Helpers::get_post_type_info();
	$page_for_post		= VK_Helpers::get_page_for_posts();
	$post_type			= $post_type_info['slug'];
	$show_on_front		= get_option( 'show_on_front' );
	$page_for_post		= get_option( 'page_for_posts' );
	$post_top_name		= ! empty( $page_for_post ) ? get_the_title( $page_for_post ) : '';
	$post_top_url		= isset( $page_for_post ) ? get_permalink( $page_for_post ) : '';
	$post				= $wp_query->get_queried_object();

	// Microdata
	// http://schema.org/BreadcrumbList
	/*-------------------------------------------*/
	$microdata_li        = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
	$microdata_li_a      = ' itemprop="item"';
	$microdata_li_a_span = ' itemprop="name"';

	// $breadcrumb_html  = '<!-- [ .breadSection ] -->';
	// $breadcrumb_html .= '<div class="section breadSection">';
	// $breadcrumb_html .= '<div class="container">';
	// $breadcrumb_html .= '<div class="row">';
	// $breadcrumb_html .= '<ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList">';

	// $breadcrumb_html .= '<li id="panHome"' . $microdata_li . '>';
	// $breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . home_url( '/' ) . '">';
	// $breadcrumb_html .= '<span' . $microdata_li_a_span . '><i class="fa fa-home"></i> HOME</span>';
	// $breadcrumb_html .= '</a>';
	// $breadcrumb_html .= '</li>';



	// Home
	$front_page_name = 'HOME';
	$page_on_front = get_option( 'page_on_front' );
	if ( $page_on_front ){
		$front_page_name = get_the_title( '$page_on_front' );
	}
	$breadcrumb_array = array(
		array(
			'name'             => $front_page_name,
			'id'               => 'breadcrumb__home',
			'url'              => home_url(),
			'class_additional' => 'breadcrumb__home',
		),
	);

	if ( is_home() ) {
		$breadcrumb_array[] = array(
			'name'             => esc_html( $page_for_posts['post_top_name'] ),
			'id'               => '',
			'url'              => '',
			'class_additional' => '',
		);
	} elseif ( is_404() ) {
		$breadcrumb_array[] = array(
			'name'             => __( 'Not found', 'lightning' ),
			'id'               => '',
			'url'              => '',
			'class_additional' => '',
		);
	} elseif ( is_search() ) {
		$breadcrumb_array[] = array(
			'name'             => sprintf( __( 'Search Results for : %s', 'lightning' ), get_search_query() ),
			'id'               => '',
			'url'              => '',
			'class_additional' => '',
		);
	} elseif ( is_attachment() ) {
		$breadcrumb_array[] = array(
			'name'             => get_the_title(),
			'id'               => '',
			'url'              => '',
			'class_additional' => '',
		);
	} elseif ( is_author() ) {
		$user_obj           = get_queried_object();
		$breadcrumb_array[] = array(
			'name'             => $user_obj->display_name,
			'id'               => '',
			'url'              => '',
			'class_additional' => '',
		);
	} elseif ( is_page() ) {
		$post = $wp_query->get_queried_object();
		// 第一階層
		if ( $post->post_parent == 0 ) {
			$breadcrumb_array[] = array(
				'name'             => strip_tags( apply_filters( 'single_post_title', get_the_title() ) ),
				'id'               => '',
				'url'              => '',
				'class_additional' => '',
			);
		} else {
			// 子階層がある場合
			$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
			array_push( $ancestors, $post->ID );
			foreach ( $ancestors as $ancestor ) {
				if ( $ancestor != end( $ancestors ) ) {
					$breadcrumb_array[] = array(
						'name'             => strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ),
						'id'               => '',
						'url'              => get_permalink( $ancestor ),
						'class_additional' => '',
					);
				} else {
					$breadcrumb_array[] = array(
						'name'             => strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ),
						'id'               => '',
						'url'              => '',
						'class_additional' => '',
					);
				} // if ( $ancestor != end( $ancestors ) ) {
			} // foreach ( $ancestors as $ancestor ) {
		} // if ( $post->post_parent == 0 ) {
	} elseif ( is_post_type_archive() ) {
		$breadcrumb_array[] = array(
			'name'             => $post_type_info['name'],
			'id'               => '',
			'url'              => '',
			'class_additional' => '',
		);
	}


$excrude_post_types = array( 'page', 'attachment' );
// if ( ! in_array( $post_type['slug'], $excrude_post_types ) ) {
if ( ( is_single() || is_archive() ) && ! is_post_type_archive() ) {
$breadcrumb_array[] = array(
	'name'             => $post_type['name'],
	'id'               => '',
	'url'              => $post_type['url'],
	'class_additional' => '',
);
}

if ( is_date() ) {
$breadcrumb_array[] = array(
	'name'             => get_the_archive_title(),
	'id'               => '',
	'url'              => '',
	'class_additional' => '',
);
} elseif ( is_tag() ) {
$breadcrumb_array[] = array(
	'name'             => single_tag_title( '', false ),
	'id'               => '',
	'url'              => '',
	'class_additional' => '',
);
} elseif ( is_category() ) {

/* Category
/*-------------------------------*/

// Get category information & insert to $cat
$cat = get_queried_object();

// parent != 0  >>>  Parent exist
if ( $cat->parent != 0 ) :
	// 祖先のカテゴリー情報を逆順で取得
	$ancestors = array_reverse( get_ancestors( $cat->cat_ID, 'category' ) );
	// 祖先階層の配列回数分ループ
	foreach ( $ancestors as $ancestor ) :
		$breadcrumb_array[] = array(
			'name'             => get_cat_name( $ancestor ),
			'id'               => '',
			'url'              => get_category_link( $ancestor ),
			'class_additional' => '',
		);
	endforeach;
	endif;
	$breadcrumb_array[] = array(
		'name'             => $cat->cat_name,
		'id'               => '',
		'url'              => '',
		'class_additional' => '',
	);

} elseif ( is_tax() ) {

/* term
/*-------------------------------*/
$now_term        = $wp_query->queried_object->term_id;
$now_term_parent = $wp_query->queried_object->parent;
$now_taxonomy    = $wp_query->queried_object->taxonomy;

// parent が !0 の場合 = 親カテゴリーが存在する場合
if ( $now_term_parent != 0 ) :
	// 祖先のカテゴリー情報を逆順で取得
	$ancestors = array_reverse( get_ancestors( $now_term, $now_taxonomy ) );
	// 祖先階層の配列回数分ループ
	foreach ( $ancestors as $ancestor ) :
		$pan_term           = get_term( $ancestor, $now_taxonomy );
		$breadcrumb_array[] = array(
			'name'             => esc_html( $pan_term->name ),
			'id'               => '',
			'url'              => get_term_link( $ancestor, $now_taxonomy ),
			'class_additional' => '',
		);
	endforeach;
	endif;
$breadcrumb_array[] = array(
	'name'             => single_cat_title( '', '', false ),
	'id'               => '',
	'url'              => '',
	'class_additional' => '',
);
} elseif ( is_single() ) {

/* Single
/*-------------------------------*/

// Case of post

if ( $post_type['slug'] == 'post' ) {
	$category = get_the_category();
	// get parent category info
	$parents = array_reverse( get_ancestors( $category[0]->term_id, 'category', 'taxonomy' ) );
	array_push( $parents, $category[0]->term_id );
	foreach ( $parents as $parent_term_id ) {
		$parent_obj         = get_term( $parent_term_id, 'category' );
		$term_url           = get_term_link( $parent_obj->term_id, $parent_obj->taxonomy );
		$breadcrumb_array[] = array(
			'name'             => $parent_obj->name,
			'id'               => '',
			'url'              => $term_url,
			'class_additional' => '',
		);
	}

	// Case of custom post type

} else {
	$taxonomies = get_the_taxonomies();
	$taxonomy   = key( $taxonomies );
	if ( $post_type['slug'] == 'info' ) {
		$taxonomy = 'info-cat';
	}

	if ( $taxonomies ) {
		$terms = get_the_terms( get_the_ID(), $taxonomy );

		//keeps only the first term (categ)
		$term = reset( $terms );
		if ( 0 != $term->parent ) {

			// Get term ancestors info
			$ancestors = array_reverse( get_ancestors( $term->term_id, $taxonomy ) );
			// Print loop term ancestors
			foreach ( $ancestors as $ancestor ) :
				$pan_term           = get_term( $ancestor, $taxonomy );
				$breadcrumb_array[] = array(
					'name'             => $pan_term->name,
					'id'               => '',
					'url'              => get_term_link( $ancestor, $taxonomy ),
					'class_additional' => '',
				);
			endforeach;
		} // if ( 0 != $term->parent ) {
		$term_url           = get_term_link( $term->term_id, $taxonomy );
		$breadcrumb_array[] = array(
			'name'             => $term->name,
			'id'               => '',
			'url'              => $term_url,
			'class_additional' => '',
		);
	} // if ( $taxonomies ) {
} // if ( $post_type['slug'] == 'post' ) {
$breadcrumb_array[] = array(
	'name'             => get_the_title(),
	'id'               => '',
	'url'              => '',
	'class_additional' => '',
);
}

$breadcrumb_array = apply_filters( 'bizvektor_panList_array', $breadcrumb_array );



	/* Post type
	/*-------------------------------*/
	} elseif ( is_single() || is_page() ||is_category() || is_tag() || is_tax() || is_post_type_archive() || is_date() ) {

		if ( 'post' === $post_type && 'page' === $show_on_front && $page_for_post ) { /* including single-post */
			$breadcrumb_html .= '<li' . $microdata_li . '>';
			$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . esc_url( $post_top_url ) . '">';
			$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . $post_top_name . '</span>';
			$breadcrumb_html .= '</a>';
			$breadcrumb_html .= '</li>';
		} elseif ( is_post_type_archive() && ! is_date() ) {
			$breadcrumb_html .= '<li>';
			$breadcrumb_html .= '<span>' . get_the_archive_title() . '</span>';
			$breadcrumb_html .= '</li>';
		} elseif ( 'post' !== $post_type && 'page' !== $post_type ) {
			$breadcrumb_html .= '<li' . $microdata_li . '>';
			$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_post_type_archive_link( $post_type ) . '">';
			$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . $post_type_info['name'] . '</span>';
			$breadcrumb_html .= '</a>';
			$breadcrumb_html .= '</li>';
		}

		/* taxonomis list
		/*-------------------------------*/
		if ( is_single() ) {
			/**
			 * Single or Page.
			 */
			// Taxonomy of Single or Page.
			$get_taxonomies = get_the_taxonomies();
			$taxonomies     = array();

			// 一旦タクソノミーの文字列配列に変換しないと色々と面倒.
			foreach ( $get_taxonomies as $key => $value ) {
				$taxonomies[] = $key;
			}

			// 除外するタクソノミーの文字列配列.
			$exclude_taxonomies = array(
				'product_type',
				'language', // Polylang その１.
				'post_translations',// Polylang その２.
			);
			$exclude_taxonomies = apply_filters( 'lightning_breadcrumb_exlude_taxonomy', $exclude_taxonomies );

			// タクソノミーの差分を採用.
			$taxonomies         = array_diff( $taxonomies, $exclude_taxonomies );

			if ( $taxonomies ) {
				foreach ( $taxonomies as $key ) {
					$taxonomy = $key;
					break;
				}
				$terms = get_the_terms( get_the_ID(), $taxonomy );
				// keeps only the first term (categ).
				$term = reset( $terms );
				if ( 0 !== $term->parent ) {

					// Get term ancestors info.
					$ancestors = array_reverse( get_ancestors( $term->term_id, $taxonomy ) );
					// Print loop term ancestors.
					foreach ( $ancestors as $ancestor ) {
						$pan_term           = get_term( $ancestor, $taxonomy );
						$breadcrumb_html .= '<li' . $microdata_li . '>';
						$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_term_link( $ancestor, $taxonomy ) . '">';
						$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . esc_html( $pan_term->name ) . '</span>';
						$breadcrumb_html .= '</a>';
						$breadcrumb_html .= '</li>';
					}
				}
				$term_url           = get_term_link( $term->term_id, $taxonomy );
				$breadcrumb_html .= '<li' . $microdata_li . '>';
				$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' .$term_url . '">';
				$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . $term->name . '</span>';
				$breadcrumb_html .= '</a>';
				$breadcrumb_html .= '</li>';
			}
		}

		if ( is_single() || is_page() ) {

			// Parent of Page or Single.
			if ( 0 !== $post->post_parent ) {
				$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
				foreach ( $ancestors as $ancestor ) {
					$breadcrumb_html .= '<li' . $microdata_li . '>';
					$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_permalink( $ancestor ) . '">';
					$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . get_the_title( $ancestor ) . '</span>';
					$breadcrumb_html .= '</a>';
					$breadcrumb_html .= '</li>';
				}
			}

			// The Single or Page.
			$breadcrumb_html .= '<li>';
			// $breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_permalink() . '">';
			$breadcrumb_html .= '<span>' . get_the_title() . '</span>';
			// $breadcrumb_html .= '</a>';
			$breadcrumb_html .= '</li>';

		} elseif ( is_category() || is_tag() || is_tax() ) {
			/**
			 * Taxonomy Archive.
			 */
			$now_term        = $wp_query->queried_object->term_id;
			$now_term_parent = $wp_query->queried_object->parent;
			$now_taxonomy    = $wp_query->queried_object->taxonomy;

			if ( 0 !== $now_term_parent ) {
				// Get Ancecter Taxonomy Reverse.
				$ancestors = array_reverse( get_ancestors( $now_term, $now_taxonomy ) );
				// Parent Taxonomy.
				foreach ( $ancestors as $ancestor ) {
					$pan_term           = get_term( $ancestor, $now_taxonomy );
					$breadcrumb_html .= '<li' . $microdata_li . '>';
					$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_term_link( $ancestor, $now_taxonomy ) . '">';
					$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . esc_html( $pan_term->name ) . '</span>';
					$breadcrumb_html .= '</a>';
					$breadcrumb_html .= '</li>';
				}
			}
			$breadcrumb_html .= '<li>';
			// $breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_term_link( $now_term, $now_taxonomy ) . '">';
			$breadcrumb_html .= '<span>' . esc_html( single_cat_title( '', '', false ) ) . '</span>';
			// $breadcrumb_html .= '</a>';
			$breadcrumb_html .= '</li>';
		} elseif ( is_date() ) {
			$breadcrumb_html .= '<li>';
			$breadcrumb_html .= '<span>' . get_the_archive_title() . '</span>';
			$breadcrumb_html .= '</li>';
		}
	}  elseif ( is_home() && ! is_front_page() ) {
		$breadcrumb_html .= '<li>';
		$breadcrumb_html .= '<span>' . $post_top_name . '</span>';
		$breadcrumb_html .= '</li>';
	} elseif ( is_author() ) {
		$author_id          = get_the_author_meta( 'ID' );
		$author_url         = get_author_posts_url( $author_id );
		$breadcrumb_html .= '<li' . $microdata_li . '>';
		$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . $author_url . '">';
		$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . get_the_archive_title() . '</span>';
		$breadcrumb_html .= '</a>';
		$breadcrumb_html .= '</li>';
	} elseif ( is_attachment() ) {
		$breadcrumb_html .= '<li' . $microdata_li . '>';
		$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_attachment_link() . '">';
		$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . get_the_title() . '</span>';
		$breadcrumb_html .= '</a>';
		$breadcrumb_html .= '</li>';
	} elseif ( is_404() ) {
		$breadcrumb_html .= '<li><span>' . __( 'Not found', 'lightning' ) . '</span></li>';
	}
	$breadcrumb_html .= '</ol>';
	$breadcrumb_html .= '</div>';
	$breadcrumb_html .= '</div>';
	$breadcrumb_html .= '</div>';
	$breadcrumb_html .= '<!-- [ /.breadSection ] -->';
		// // delete before after space
		// $dynamic_css = trim( $dynamic_css );
		// convert tab and br to space
		$breadcrumb_html = preg_replace( '/[\n\r\t]/', '', $breadcrumb_html );
		// Change multiple spaces to single space
		$breadcrumb_html = preg_replace( '/\s(?=\s)/', '', $breadcrumb_html );
	return $breadcrumb_html;
}

$breadcrumb_html = lightning_bread_crumb();

$allowed_html = array(
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
$breadcrumb_html  = apply_filters( 'lightning_panListHtml', $breadcrumb_html );
$breadcrumb_html  = wp_kses( $breadcrumb_html, $allowed_html );
echo $breadcrumb_html;
