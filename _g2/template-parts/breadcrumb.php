<?php
/**
 * Lightning Bread Crumb G2
 *
 * @package lightning
 */

if ( ! function_exists( 'lightning_bread_crumb' ) ) {
	/**
	 * パンくずの HTML を組み立てて返す（G2）.
	 *
	 * 表示中のページの種類に応じて項目を並べ、リスト全体を nav で囲み、
	 * 最後の項目（今表示しているページ）の li に aria-current="page" を付ける.
	 * 戻り値は出力用に安全化していないため、表示側で wp_kses() を通すこと.
	 *
	 * @return string パンくずの HTML.
	 */
	function lightning_bread_crumb() {
		global $wp_query;

		/********************************************
		 * Get Post top page info
		 */
		// get_post_type() だとtaxonomyページで該当の投稿がない時に投稿タイプを取得できないため lightning_get_post_type() を使用
		// また、wooCommerceなどはショップトップの名称が投稿タイプ名と異なるので、そのあたりの処理も lightning_get_post_type() で対応済み.
		$post_type_info = lightning_get_post_type();
		$show_on_front  = get_option( 'show_on_front' );
		$page_for_post  = get_option( 'page_for_posts' );
		$post_top_name  = ! empty( $page_for_post ) ? get_the_title( $page_for_post ) : '';
		$post_top_url   = isset( $page_for_post ) ? get_permalink( $page_for_post ) : '';
		$post           = $wp_query->get_queried_object();

		// Microdata
		// https://schema.org/BreadcrumbList ignore:phpcs.
		/*-------------------------------------------*/
		$microdata_li        = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
		$microdata_li_a      = ' itemprop="item"';
		$microdata_li_a_span = ' itemprop="name"';
		$position            = 0;

		$breadcrumb_html  = '<!-- [ .breadSection ] -->';
		$breadcrumb_html .= '<div class="section breadSection">';
		$breadcrumb_html .= '<div class="container">';
		$breadcrumb_html .= '<div class="row">';
		// 読み上げソフトでナビゲーション領域として扱えるよう、リストを nav で囲む.
		// nav は .breadSection の内側に置く。外側に置くと、パンくずを隠したときに中身のない nav だけが残るため.
		$breadcrumb_html .= '<nav aria-label="' . esc_attr__( 'Breadcrumb', 'lightning' ) . '">';
		$breadcrumb_html .= '<ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList">';

		$breadcrumb_html .= '<li id="panHome"' . $microdata_li . '>';
		$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . home_url( '/' ) . '">';
		$breadcrumb_html .= '<span' . $microdata_li_a_span . '><i class="fa-solid fa-house"></i> HOME</span>';
		$breadcrumb_html .= '</a>';
		++$position;
		$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
		$breadcrumb_html .= '</li>';

		/********************************************
		 * Search result
		 */
		if ( is_search() ) {
			if ( ! empty( get_search_query() ) ) {
				// translators: seatch keyword.
				$search_text = sprintf( __( 'Search Results for : %s', 'lightning' ), get_search_query() );
			} else {
				$search_text = __( 'Search Results', 'lightning' );
			}
			$breadcrumb_html .= '<li><span>' . $search_text . '</span>';
			++$position;
			$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
			$breadcrumb_html .= '</li>';

			/********************************************
			 * Post type
			 */
		} elseif ( is_single() || is_page() || is_category() || is_tag() || is_tax() || is_post_type_archive() || is_date() ) {
			if ( 'post' === $post_type_info['slug'] && 'page' === $show_on_front && $page_for_post ) { /* including single-post */
				$breadcrumb_html .= '<li' . $microdata_li . '>';
				$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . esc_url( $post_top_url ) . '">';
				$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . $post_top_name . '</span>';
				$breadcrumb_html .= '</a>';
				++$position;
				$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
				$breadcrumb_html .= '</li>';
			} elseif ( is_post_type_archive() && ! is_date() ) {
				$breadcrumb_html .= '<li>';
				$breadcrumb_html .= '<span>' . wp_kses_post( $post_type_info['name'] ) . '</span>';
				++$position;
				$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
				$breadcrumb_html .= '</li>';
			} elseif ( 'post' !== $post_type_info['slug'] && 'page' !== $post_type_info['slug'] ) {
				$breadcrumb_html .= '<li' . $microdata_li . '>';
				$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_post_type_archive_link( $post_type_info['slug'] ) . '">';
				$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . wp_kses_post( $post_type_info['name'] ) . '</span>';
				$breadcrumb_html .= '</a>';
				++$position;
				$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
				$breadcrumb_html .= '</li>';
			}

			/********************************************
			 * Taxonomis list
			 */
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
				$exclusion = array();
				$exclusion = apply_filters( 'lightning_breadcrumb_exlude_taxonomy', $exclusion );
				$exclusion = apply_filters( 'vk_breadcrumb_taxonomies_exludion', $exclusion );

				// タクソノミーの差分を採用.
				if ( $exclusion ) {
					$taxonomies = array_diff( $taxonomies, $exclusion );
				}

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
							$pan_term         = get_term( $ancestor, $taxonomy );
							$breadcrumb_html .= '<li' . $microdata_li . '>';
							$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_term_link( $ancestor, $taxonomy ) . '">';
							$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . esc_html( $pan_term->name ) . '</span>';
							$breadcrumb_html .= '</a>';
							++$position;
							$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
							$breadcrumb_html .= '</li>';
						}
					}
					$term_url         = get_term_link( $term->term_id, $taxonomy );
					$breadcrumb_html .= '<li' . $microdata_li . '>';
					$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . $term_url . '">';
					$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . $term->name . '</span>';
					$breadcrumb_html .= '</a>';
					++$position;
					$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
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
						++$position;
						$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
						$breadcrumb_html .= '</li>';
					}
				}

				// The Single or Page.
				$breadcrumb_html .= '<li>';
				$breadcrumb_html .= '<span>' . get_the_title() . '</span>';
				++$position;
				$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
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
						$pan_term         = get_term( $ancestor, $now_taxonomy );
						$breadcrumb_html .= '<li' . $microdata_li . '>';
						$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_term_link( $ancestor, $now_taxonomy ) . '">';
						$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . esc_html( $pan_term->name ) . '</span>';
						$breadcrumb_html .= '</a>';
						++$position;
						$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
						$breadcrumb_html .= '</li>';
					}
				}
				$breadcrumb_html .= '<li>';
				$breadcrumb_html .= '<span>' . esc_html( single_cat_title( '', '', false ) ) . '</span>';
				++$position;
				$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
				$breadcrumb_html .= '</li>';
			} elseif ( is_date() ) {
				$breadcrumb_html .= '<li>';
				$breadcrumb_html .= '<span>' . get_the_archive_title() . '</span>';
				++$position;
				$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
				$breadcrumb_html .= '</li>';
			}
		} elseif ( is_home() && ! is_front_page() ) {
			$breadcrumb_html .= '<li>';
			$breadcrumb_html .= '<span>' . $post_top_name . '</span>';
			++$position;
			$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
			$breadcrumb_html .= '</li>';
		} elseif ( is_author() ) {
			$author_id        = get_the_author_meta( 'ID' );
			$author_url       = get_author_posts_url( $author_id );
			$breadcrumb_html .= '<li' . $microdata_li . '>';
			$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . $author_url . '">';
			$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . get_the_archive_title() . '</span>';
			$breadcrumb_html .= '</a>';
			++$position;
			$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
			$breadcrumb_html .= '</li>';
		} elseif ( is_attachment() ) {
			$breadcrumb_html .= '<li' . $microdata_li . '>';
			$breadcrumb_html .= '<a' . $microdata_li_a . ' href="' . get_attachment_link() . '">';
			$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . get_the_title() . '</span>';
			$breadcrumb_html .= '</a>';
			++$position;
			$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
			$breadcrumb_html .= '</li>';
		} elseif ( is_404() ) {
			$breadcrumb_html .= '<li><span>' . __( 'Not found', 'lightning' ) . '</span>';
			++$position;
			$breadcrumb_html .= '<meta itemprop="position" content="' . $position . '" />';
			$breadcrumb_html .= '</li>';
		}
		$breadcrumb_html .= '</ol>';
		$breadcrumb_html .= '</nav>';
		$breadcrumb_html .= '</div>';
		$breadcrumb_html .= '</div>';
		$breadcrumb_html .= '</div>';
		$breadcrumb_html .= '<!-- [ /.breadSection ] -->';

		// 最後の項目（今表示しているページ）の li に aria-current="page" を付ける.
		$breadcrumb_html = lightning_breadcrumb_add_aria_current( $breadcrumb_html );

		// // delete before after space
		// $dynamic_css = trim( $dynamic_css );
		// convert tab and br to space
		$breadcrumb_html = preg_replace( '/[\n\r\t]/', '', $breadcrumb_html );
		// Change multiple spaces to single space.
		$breadcrumb_html = preg_replace( '/\s(?=\s)/', '', $breadcrumb_html );
		return $breadcrumb_html;
	}
}

$breadcrumb_html = lightning_bread_crumb();

$allowed_html = array(
	// パンくずを囲む nav と、その名前（aria-label）が取り除かれないよう許可する.
	'nav'  => array(
		'id'         => array(),
		'class'      => array(),
		'aria-label' => array(),
	),
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
		'id'           => array(),
		'class'        => array(),
		'itemprop'     => array(),
		'itemscope'    => array(),
		'itemtype'     => array(),
		// 今表示しているページを示す aria-current が取り除かれないよう許可する.
		'aria-current' => array(),
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
	'meta' => array(
		'itemprop' => array(),
		'content'  => array(),
	),
	'i'    => array(
		'id'    => array(),
		'class' => array(),
	),
);
$breadcrumb_html = apply_filters( 'lightning_panListHtml', $breadcrumb_html ); // phpcs:ignore
echo wp_kses( $breadcrumb_html, $allowed_html );
