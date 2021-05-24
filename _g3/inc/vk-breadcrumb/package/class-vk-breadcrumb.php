<?php

if ( ! class_exists( 'VK_Breadcrumb' ) ) {

	class VK_Breadcrumb {

		/**
		 *
		 */
		public static function get_array() {

			global $wp_query;

			// Get Post top page info
			/*
			-------------------------------------------*/
			// get_post_type() だとtaxonomyページで該当の投稿がない時に投稿タイプを取得できないため VK_Helpers::get_post_type_info() を使用
			$post_type_info = VK_Helpers::get_post_type_info();
			$post_top_info  = VK_Helpers::get_post_top_info();
			$post_type      = $post_type_info['slug'];
			$show_on_front  = get_option( 'show_on_front' );
			$page_on_front  = get_option( 'page_on_front' );
			$post           = $wp_query->get_queried_object();

			// Home
			$front_page_name = 'HOME';
			$page_on_front   = get_option( 'page_on_front' );

			if ( $page_on_front ) {
				$front_page_name = get_the_title( $page_on_front );
			}

			$breadcrumb_array = array(
				array(
					'name'  => $front_page_name,
					'id'    => '',
					'url'   => home_url(),
					'class' => 'breadcrumb-list__item--home',
					'icon'  => 'fas fa-fw fa-home',
				),
			);

			if ( is_home() && ! is_front_page() ) {
				$breadcrumb_array[] = array(
					'name'  => esc_html( $post_top_info['name'] ),
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);

			} elseif ( is_404() ) {
				$breadcrumb_array[] = array(
					'name'  => __( 'Not found', 'lightning' ),
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);
			} elseif ( is_attachment() ) {
				$breadcrumb_array[] = array(
					'name'  => get_the_title(),
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);

			} elseif ( is_author() ) {
				$user_obj           = get_queried_object();
				$breadcrumb_array[] = array(
					'name'  => $user_obj->display_name,
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);

				// For filter search term & keywords or term & no keyword
			} elseif ( is_search() ) {
				if ( get_search_query() ) {
					$name = sprintf( __( 'Search Results for : %s', 'lightning' ), get_search_query() );
				} else {
					$name = __( 'Search Results', 'lightning' );
				}
				$breadcrumb_array[] = array(
					'name'  => $name,
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);

			} elseif ( is_page() ) {
				$post = $wp_query->get_queried_object();
				// 第一階層
				if ( $post->post_parent == 0 ) {
					$breadcrumb_array[] = array(
						'name'  => strip_tags( apply_filters( 'single_post_title', get_the_title() ) ),
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					);
				} else {
					// 子階層がある場合
					$ancestors = array_reverse( get_post_ancestors( $post->ID ) );
					array_push( $ancestors, $post->ID );
					foreach ( $ancestors as $ancestor ) {
						if ( $ancestor != end( $ancestors ) ) {
							$breadcrumb_array[] = array(
								'name'  => strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ),
								'id'    => '',
								'url'   => get_permalink( $ancestor ),
								'class' => '',
								'icon'  => '',
							);
						} else {
							$breadcrumb_array[] = array(
								'name'  => strip_tags( apply_filters( 'single_post_title', get_the_title( $ancestor ) ) ),
								'id'    => '',
								'url'   => '',
								'class' => '',
								'icon'  => '',
							);
						} // if ( $ancestor != end( $ancestors ) ) {
					} // foreach ( $ancestors as $ancestor ) {
				} // if ( $post->post_parent == 0 ) {

			} elseif ( is_post_type_archive() && ! is_date() ) {
				$breadcrumb_array[] = array(
					'name'  => $post_type_info['name'],
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);
			} else if ( ( is_single() || is_archive() ) || is_date() && ! is_post_type_archive() && ! is_search() ) {
				if ( $post_type_info['slug'] !== 'post' || $post_top_info['use'] ){
					$breadcrumb_array[] = array(
						'name'  => $post_type_info['name'],
						'id'    => '',
						'url'   => $post_type_info['url'],
						'class' => '',
						'icon'  => '',
					);
				}
			}

			if ( is_date() ) {
				$breadcrumb_array[] = array(
					'name'  => get_the_archive_title(),
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);

			} elseif ( is_tag() ) {
				$breadcrumb_array[] = array(
					'name'  => single_tag_title( '', false ),
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);

			} elseif ( is_category() ) {

				/*
				 Category
				/*-------------------------------*/

				// Get category information & insert to $cat
				$cat = get_queried_object();

				// parent != 0  >>>  Parent exist
				if ( $cat->parent != 0 ) {
					// 祖先のカテゴリー情報を逆順で取得
					$ancestors = array_reverse( get_ancestors( $cat->cat_ID, 'category' ) );
					// 祖先階層の配列回数分ループ
					foreach ( $ancestors as $ancestor ) :
						$breadcrumb_array[] = array(
							'name'  => get_cat_name( $ancestor ),
							'id'    => '',
							'url'   => get_category_link( $ancestor ),
							'class' => '',
							'icon'  => '',
						);
					endforeach;
				}
				$breadcrumb_array[] = array(
					'name'  => $cat->cat_name,
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);

			} elseif ( is_tax() ) {

				/*
				 term
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
							'name'  => esc_html( $pan_term->name ),
							'id'    => '',
							'url'   => get_term_link( $ancestor, $now_taxonomy ),
							'class' => '',
							'icon'  => '',
						);
					endforeach;
					endif;
				$breadcrumb_array[] = array(
					'name'  => single_cat_title( '', '', false ),
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);

			} elseif ( is_single() ) {

				/*
				 Single
				/*-------------------------------*/

				// Case of post

				if ( $post_type_info['slug'] == 'post' ) {
					$category = get_the_category();
					// get parent category info
					$parents = array_reverse( get_ancestors( $category[0]->term_id, 'category', 'taxonomy' ) );
					array_push( $parents, $category[0]->term_id );
					foreach ( $parents as $parent_term_id ) {
						$parent_obj         = get_term( $parent_term_id, 'category' );
						$term_url           = get_term_link( $parent_obj->term_id, $parent_obj->taxonomy );
						$breadcrumb_array[] = array(
							'name'  => $parent_obj->name,
							'id'    => '',
							'url'   => $term_url,
							'class' => '',
							'icon'  => '',
						);
					}

					// Case of custom post type

				} else {

					// Taxonomy of Single or Page.
					$get_taxonomies = get_the_taxonomies();

					// 非公開のタクソノミーを自動的に除外
					foreach ( $get_taxonomies as $taxonomy => $value ) {
						$taxonomy_info = get_taxonomy( $taxonomy );
						if ( empty( $taxonomy_info->public ) ) {
							unset( $get_taxonomies[ $taxonomy ] );
						}
					}

					$taxonomies = array();

					// 一旦タクソノミーの文字列配列に変換しないと色々と面倒.
					foreach ( $get_taxonomies as $key => $value ) {
						$taxonomies[] = $key;
					}

					// 除外するタクソノミーの文字列配列.
					$exclusion = array();
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

						// keeps only the first term (categ)
						$term = reset( $terms );
						if ( 0 != $term->parent ) {

							// Get term ancestors info
							$ancestors = array_reverse( get_ancestors( $term->term_id, $taxonomy ) );
							// Print loop term ancestors
							foreach ( $ancestors as $ancestor ) {
								$pan_term           = get_term( $ancestor, $taxonomy );
								$breadcrumb_array[] = array(
									'name'  => $pan_term->name,
									'id'    => '',
									'url'   => get_term_link( $ancestor, $taxonomy ),
									'class' => '',
									'icon'  => '',
								);
							}
						} // if ( 0 != $term->parent ) {
						$term_url           = get_term_link( $term->term_id, $taxonomy );
						$breadcrumb_array[] = array(
							'name'  => $term->name,
							'id'    => '',
							'url'   => $term_url,
							'class' => '',
							'icon'  => '',
						);
					} // if ( $taxonomies ) {
				} // if ( $post_type_info['slug'] == 'post' ) {

				$breadcrumb_array[] = array(
					'name'  => get_the_title(),
					'id'    => '',
					'url'   => '',
					'class' => '',
					'icon'  => '',
				);
			} // is_single

			return $breadcrumb_array = apply_filters( 'vk_breadcrumb_array', $breadcrumb_array );

		}


		/**
		 *
		 */
		public static function the_breadcrumb() {

				$breadcrumb_array = self::get_array();

				global $breadcrumb_options;

				// Microdata
				// http://schema.org/BreadcrumbList
				/*-------------------------------------------*/
				$microdata_li        = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';
				$microdata_li_a      = ' itemprop="item"';
				$microdata_li_a_span = ' itemprop="name"';

				$breadcrumb_html  = '<!-- [ #' . esc_attr( $breadcrumb_options['class_outer'] ) . ' ] -->';
				$breadcrumb_html .= '<div id="' . esc_attr( $breadcrumb_options['class_outer'] ) . '" class="' . esc_attr( $breadcrumb_options['class_outer'] ) . '">';
				$breadcrumb_html .= '<div class="' . esc_attr( $breadcrumb_options['class_inner'] ) . '">';
				$breadcrumb_html .= '<ol class="' . esc_attr( $breadcrumb_options['class_list'] ) . '">';

			foreach ( $breadcrumb_array as $key => $value ) {

				$id = ( $value['id'] ) ? ' id="' . esc_attr( $value['id'] ) . '"' : '';

				$class = ' class="' . esc_attr( $breadcrumb_options['class_list_item'] );
				if ( ! empty( $value['class'] ) ) {
					$class .= ' ' . esc_attr( $value['class'] );
				}
				$class .= '"';

				$breadcrumb_html .= '<li' . $id . $class . $microdata_li . '>';

				if ( $value['url'] ) {
					$breadcrumb_html .= '<a href="' . esc_url( $value['url'] ) . '"' . $microdata_li_a . '>';
				}

				if ( ! empty( $value['icon'] ) ) {
					$breadcrumb_html .= '<i class="' . $value['icon'] . '"></i>';
				}

				$breadcrumb_html .= '<span' . $microdata_li_a_span . '>' . esc_html( $value['name'] ) . '</span>';

				if ( $value['url'] ) {
					$breadcrumb_html .= '</a>';
				}

				$breadcrumb_html .= '</li>';

			}

				$breadcrumb_html .= '</ol>';
				$breadcrumb_html .= '</div>
                </div>
                <!-- [ /#' . esc_attr( $breadcrumb_options['class_outer'] ) . ' ] -->
                ';
				$breadcrumb_html  = apply_filters( 'vk_breadcrumb_html', $breadcrumb_html );

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

				$breadcrumb_html = wp_kses( $breadcrumb_html, $allowed_html );
				echo $breadcrumb_html;

		}

	}

}
