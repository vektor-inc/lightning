<?php
use VektorInc\VK_Breadcrumb\VkBreadcrumb;
new VkBreadcrumb();

class VkBreadcrumbTest extends WP_UnitTestCase {

	function test_lightning_bread_crumb() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'vk_bread_crumb' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

		$before_option         = get_option( 'lightning_theme_options' );
		$before_page_for_posts = get_option( 'page_for_posts' ); // 投稿トップに指定するページ
		$before_page_on_front  = get_option( 'page_on_front' ); // フロントに指定する固定ページ
		$before_show_on_front  = get_option( 'show_on_front' ); // トップページ指定するかどうか page or posts

		/*** ↓↓ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) */

		register_post_type(
			'event',
			array(
				'label'       => 'Event',
				'has_archive' => true,
				'public'      => true,
			)
		);
		register_taxonomy(
			'event_cat',
			'event',
			array(
				'label'        => 'Event Category',
				'rewrite'      => array( 'slug' => 'event_cat' ),
				'hierarchical' => true,
			)
		);

		// Create test category
		$catarr             = array(
			'cat_name' => 'parent_category',
		);
		$parent_category_id = wp_insert_category( $catarr );

		$catarr            = array(
			'cat_name'        => 'child_category',
			'category_parent' => $parent_category_id,
		);
		$child_category_id = wp_insert_category( $catarr );

		$catarr              = array(
			'cat_name' => 'no_post_category',
		);
		$no_post_category_id = wp_insert_category( $catarr );

		// Create test term
		$args          = array(
			'slug' => 'event_category_name',
		);
		$term_info     = wp_insert_term( 'event_category_name', 'event_cat', $args );
		$event_term_id = $term_info['term_id'];

		// Create test post
		$post    = array(
			'post_title'    => 'test',
			'post_status'   => 'publish',
			'post_content'  => 'content',
			'post_category' => array( $parent_category_id ),
		);
		$post_id = wp_insert_post( $post );
		// 投稿にカテゴリー指定
		wp_set_object_terms( $post_id, 'child_category', 'category' );

		// Create test page
		$post           = array(
			'post_title'   => 'parent_page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$parent_page_id = wp_insert_post( $post );

		$post = array(
			'post_title'   => 'child_page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
			'post_parent'  => $parent_page_id,

		);
		$child_page_id = wp_insert_post( $post );

		// Create test home page
		$post         = array(
			'post_title'   => 'post_top',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$home_page_id = wp_insert_post( $post );

		// Create test home page
		$post          = array(
			'post_title'   => 'front_page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$front_page_id = wp_insert_post( $post );

		// custom post type.
		$post          = array(
			'post_title'   => 'event-test-post',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$event_post_id = wp_insert_post( $post );
		// set event category to event post
		wp_set_object_terms( $event_post_id, 'event_category_name', 'event_cat' );

		/*** ↑↑ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) */

		/*
		 Test Array
		/*--------------------------------*/
		$test_array = array(

			// 404ページ
			array(
				'target_url' => home_url( '/?name=aaaaa' ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => __( 'Not found', 'lightning' ),
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),
			// 検索結果（検索キーワードなし）
			array(
				'target_url' => home_url( '/?s=' ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => __( 'Search Results', 'lightning' ),
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// 検索結果（検索キーワード:aaa）
			array(
				'target_url' => home_url( '/?s=aaa' ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => sprintf( __( 'Search Results for : %s', 'lightning' ), 'aaa' ),
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページに固定ページ
			// トップページに指定した固定ページ名
			array(
				'target_url' => home_url(),
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'correct'    => array(
					array(
						'name'  => 'front_page',
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
				),
			),
			// 固定ページ
			// HOME > 固定ページ名.
			array(
				'target_url' => get_permalink( $parent_page_id ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'parent_page',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 固定ページ
			// トップページに固定ページ > 固定ページ名
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $parent_page_id ),
				'correct'    => array(
					array(
						'name'  => 'front_page',
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'parent_page',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// 固定ページの子ページ
			// トップに指定した固定ページ名 > 親ページ > 子ページ
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $child_page_id ),
				'correct'    => array(
					array(
						'name'  => 'front_page',
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'parent_page',
						'id'    => '',
						'url'   => get_permalink( $parent_page_id ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'child_page',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページ未指定 / 投稿トップ未指定 / 固定ページの子ページ
			// HOME > 親ページ > 子ページ
			array(
				'target_url' => get_permalink( $child_page_id ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'parent_page',
						'id'    => '',
						'url'   => get_permalink( $parent_page_id ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'child_page',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページに最新の投稿（投稿トップ未指定） / 子カテゴリー
			// HOME > 親カテゴリー > 子カテゴリー
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_term_link( $child_category_id, 'category' ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'parent_category',
						'id'    => '',
						'url'   => get_term_link( $parent_category_id, 'category' ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'child_category',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページに最新の投稿 / 投稿トップページ無指定 / 記事ページ
			// HOME > 親カテゴリー > 子カテゴリー > 記事タイトル
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_permalink( $post_id ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'parent_category',
						'id'    => '',
						'url'   => get_term_link( $parent_category_id, 'category' ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'child_category',
						'id'    => '',
						'url'   => get_term_link( $child_category_id, 'category' ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'test',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定
			// HOME > 投稿トップの固定ページ名
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $home_page_id ),
				'correct'    => array(
					array(
						'name'  => 'front_page',
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'post_top',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 子カテゴリー
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > 親カテゴリー > 子カテゴリー
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_term_link( $child_category_id, 'category' ),
				'correct'    => array(
					array(
						'name'  => 'front_page',
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'post_top',
						'id'    => '',
						'url'   => get_permalink( $home_page_id ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'parent_category',
						'id'    => '',
						'url'   => get_term_link( $parent_category_id, 'category' ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'child_category',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 投稿のないカテゴリーアーカイブページ
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > 投稿のないカテゴリー名
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_term_link( $no_post_category_id, 'category' ),
				'correct'    => array(
					array(
						'name'  => 'front_page',
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'post_top',
						'id'    => '',
						'url'   => get_permalink( $home_page_id ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'no_post_category',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 年別アーカイブ
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > アーカイブ名
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => home_url() . '/?post_type=post&year=' . date( 'Y' ),
				'correct'    => array(
					array(
						'name'  => 'front_page',
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'post_top',
						'id'    => '',
						'url'   => get_permalink( $home_page_id ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'Year: <span>' . date( 'Y' ) . '</span>',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// トップページに固定ページ / 投稿トップページ無指定 / 年別アーカイブ
			// HOME > アーカイブ名
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => home_url() . '/?post_type=post&year=' . date( 'Y' ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'Year: <span>' . date( 'Y' ) . '</span>',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// カスタム投稿タイプトップ
			// HOME > 投稿タイプ名
			array(
				'target_url' => home_url() . '/?post_type=event',
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'Event',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// カスタム投稿タイプ / カスタム分類アーカイブ
			// HOME > 投稿タイプ名 > カスタム分類
			array(
				'target_url' => get_term_link( $event_term_id ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'Event',
						'id'    => '',
						'url'   => get_post_type_archive_link( 'event' ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'event_category_name',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// カスタム投稿タイプ / 年別アーカイブ
			// HOME > 投稿タイプ名 > アーカイブ名
			array(
				'target_url' => home_url() . '/?post_type=event&year=' . date( 'Y' ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'Event',
						'id'    => '',
						'url'   => get_post_type_archive_link( 'event' ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'Year: <span>' . date( 'Y' ) . '</span>',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// カスタム投稿タイプ / 記事詳細
			// HOME > 投稿タイプ名 > カスタム分類 > 記事タイトル
			array(
				'target_url' => get_permalink( $event_post_id ),
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'Event',
						'id'    => '',
						'url'   => get_post_type_archive_link( 'event' ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'event_category_name',
						'id'    => '',
						'url'   => get_term_link( $event_term_id ),
						'class' => '',
						'icon'  => '',
					),
					array(
						'name'  => 'event-test-post',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

			// カテゴリーとキーワードでの絞り込み検索
			// HOME > "test"の検索結果 .
			array(
				'target_url' => home_url() . '/?category_name=child_category&s=test',
				'correct'    => array(
					array(
						'name'  => __( 'HOME', 'lightning' ),
						'id'    => '',
						'url'   => home_url(),
						'class' => 'breadcrumb-list__item--home',
						'icon'  => 'fas fa-fw fa-home',
					),
					array(
						'name'  => 'Search Results for : test',
						'id'    => '',
						'url'   => '',
						'class' => '',
						'icon'  => '',
					),
				),
			),

		);

		foreach ( $test_array as $value ) {
			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}

			// Move to test page
			$this->go_to( $value['target_url'] );
			// $return = $vk_breadcrumb->get_array();
			$return = VkBreadcrumb::get_array();

			global $wp_query;
			print '<pre style="text-align:left">';
			print_r( $wp_query->query );
			print '</pre>';

			print PHP_EOL;
			print $value['target_url'] . PHP_EOL;
			print 'return------------------------------------' . PHP_EOL;
			var_dump( $return ) . PHP_EOL;
			print 'correct------------------------------------' . PHP_EOL;
			var_dump( $value['correct'] ) . PHP_EOL;
			print '------------------------------------' . PHP_EOL;

			$this->assertEquals( $value['correct'], $return );

			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					delete_option( $option_key );
				}
			}
		}

		/*
		 テスト前の値に戻す
		/*--------------------------------*/
		wp_delete_post( $post_id );
		wp_delete_post( $home_page_id );
		$parent_category_id = wp_delete_category( $catarr );
		update_option( 'lightning_theme_options', $before_option );
		update_option( 'page_for_posts', $before_page_for_posts );
		update_option( 'page_on_front', $before_page_on_front );
		update_option( 'show_on_front', $before_show_on_front );
	}
}
