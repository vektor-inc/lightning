<?php

if ( ! class_exists( 'VK_Breadcrumb' ) ) {

    class VK_Breadcrumb {

        /**
         * 
         */
        public static function get_array(){

            global $wp_query;

            // Get Post top page info
            /*-------------------------------------------*/
            // get_post_type() だとtaxonomyページで該当の投稿がない時に投稿タイプを取得できないため VK_Helpers::get_post_type_info() を使用
            $post_type_info		= VK_Helpers::get_post_type_info();
            $post_top_info		= VK_Helpers::get_post_top_info();
            $post_type			= $post_type_info['slug'];
            $show_on_front		= get_option( 'show_on_front' );
            $page_on_front		= get_option( 'page_on_front' );
            $post				= $wp_query->get_queried_object();        
        
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
        
            if ( is_home() && ! is_front_page() ) {
                $breadcrumb_array[] = array(
                    'name'             => esc_html( $post_top_info['name'] ),
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
        
            // /* Single
            // /*-------------------------------*/
            
            // // Case of post
            
            // if ( $post_type['slug'] == 'post' ) {
            //     $category = get_the_category();
            //     // get parent category info
            //     $parents = array_reverse( get_ancestors( $category[0]->term_id, 'category', 'taxonomy' ) );
            //     array_push( $parents, $category[0]->term_id );
            //     foreach ( $parents as $parent_term_id ) {
            //         $parent_obj         = get_term( $parent_term_id, 'category' );
            //         $term_url           = get_term_link( $parent_obj->term_id, $parent_obj->taxonomy );
            //         $breadcrumb_array[] = array(
            //             'name'             => $parent_obj->name,
            //             'id'               => '',
            //             'url'              => $term_url,
            //             'class_additional' => '',
            //         );
            //     }
            
            // // Case of custom post type
            
            // } else {

            //     $taxonomies = get_the_taxonomies();
            //     $taxonomy   = key( $taxonomies );
            
            //     if ( $taxonomies ) {
            //         $terms = get_the_terms( get_the_ID(), $taxonomy );
            
            //         //keeps only the first term (categ)
            //         $term = reset( $terms );
            //         if ( 0 != $term->parent ) {
            
            //             // Get term ancestors info
            //             $ancestors = array_reverse( get_ancestors( $term->term_id, $taxonomy ) );
            //             // Print loop term ancestors
            //             foreach ( $ancestors as $ancestor ) :
            //                 $pan_term           = get_term( $ancestor, $taxonomy );
            //                 $breadcrumb_array[] = array(
            //                     'name'             => $pan_term->name,
            //                     'id'               => '',
            //                     'url'              => get_term_link( $ancestor, $taxonomy ),
            //                     'class_additional' => '',
            //                 );
            //             endforeach;
            //         } // if ( 0 != $term->parent ) {
            //         $term_url           = get_term_link( $term->term_id, $taxonomy );
            //         $breadcrumb_array[] = array(
            //             'name'             => $term->name,
            //             'id'               => '',
            //             'url'              => $term_url,
            //             'class_additional' => '',
            //         );
            //     } // if ( $taxonomies ) {
            // } // if ( $post_type['slug'] == 'post' ) {

            // $breadcrumb_array[] = array(
            //     'name'             => get_the_title(),
            //     'id'               => '',
            //     'url'              => '',
            //     'class_additional' => '',
            // );
            } // is_single

            return $breadcrumb_array = apply_filters( 'bizvektor_panList_array', $breadcrumb_array );

        }
        
        
        /**
         * 
         */
        public static function the_breadcrumb(){

                $breadcrumb_array = self::get_array();

                print '<pre style="text-align:left">';print_r($breadcrumb_array);print '</pre>';

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
        }

    }

}