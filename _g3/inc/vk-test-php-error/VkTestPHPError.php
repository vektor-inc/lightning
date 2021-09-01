<?php
/**
 * Test for PHP Error
 *
 * @package Lightning G3
 */

namespace vktestphperror;

/**
 * PHP_Fatal_Error_Test
 */
class VkTestPHPError {

	/**
	 * Display post title
	 *
	 * @return void
	 */
	public static function test_title() {
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'PHP Fatal Error Check' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;
	}

	/**
	 * Insert Test Environment Post
	 *
	 * @return array $test_posts : test post data
	 */
	public static function create_test_posts() {
		/*** ↓↓ テスト用事前データ設定 */

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

		$test_posts = array();

		// Create test category.
		$catarr                           = array(
			'cat_name' => 'parent_category',
		);
		$test_posts['parent_category_id'] = wp_insert_category( $catarr );

		$catarr                          = array(
			'cat_name'        => 'child_category',
			'category_parent' => $test_posts['parent_category_id'],
		);
		$test_posts['child_category_id'] = wp_insert_category( $catarr );

		$catarr                            = array(
			'cat_name' => 'no_post_category',
		);
		$test_posts['no_post_category_id'] = wp_insert_category( $catarr );

		// Create test term.
		$args                        = array(
			'slug' => 'event_category_name',
		);
		$term_info                   = wp_insert_term( 'event_category_name', 'event_cat', $args );
		$test_posts['event_term_id'] = $term_info['term_id'];

		// Create test post.
		$post                  = array(
			'post_title'    => 'test',
			'post_status'   => 'publish',
			'post_content'  => 'content',
			'post_category' => array( $test_posts['parent_category_id'] ),
		);
		$test_posts['post_id'] = wp_insert_post( $post );
		// 投稿にカテゴリー指定.
		wp_set_object_terms( $test_posts['post_id'], 'child_category', 'category' );

		// Create test page.
		$post                         = array(
			'post_title'   => 'parent_page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['parent_page_id'] = wp_insert_post( $post );

		$post = array(
			'post_title'   => 'child_page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
			'post_parent'  => $test_posts['parent_page_id'],

		);
		$test_posts['child_page_id'] = wp_insert_post( $post );

		// Create test home page.
		$post                       = array(
			'post_title'   => 'post_top',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['home_page_id'] = wp_insert_post( $post );

		// Create test home page.
		$post                        = array(
			'post_title'   => 'front_page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['front_page_id'] = wp_insert_post( $post );

		// custom post type.
		$post                        = array(
			'post_title'   => 'event-test-post',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_posts['event_post_id'] = wp_insert_post( $post );
		// set event category to event post.
		wp_set_object_terms( $test_posts['event_post_id'], 'event_category_name', 'event_cat' );

		/*** ↑↑ テスト用事前データ設 */

		return $test_posts;
	}

	/****************************************
	 * Test Array
	 */
	public static function get_test_array() {

		$test_posts = self::create_test_posts();

		$test_array = array(

			array(
				'target_url' => home_url(),
			),

			// 404ページ
			array(
				'target_url' => home_url( '/?name=aaaaa' ),
			),
			// 検索結果（検索キーワードなし）.
			array(
				'target_url' => home_url( '/?s=' ),
			),

			// 検索結果（検索キーワード:aaa）.
			array(
				'target_url' => home_url( '/?s=aaa' ),
			),

			// 固定ページ
			// HOME > 固定ページ名.
			array(
				'target_url' => get_permalink( $test_posts['parent_page_id'] ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 固定ページ
			// トップに指定した固定ページ名 > 固定ページ名.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_permalink( $test_posts['parent_page_id'] ),
			),

			// 固定ページの子ページ
			// トップに指定した固定ページ名 > 親ページ > 子ページ.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_permalink( $test_posts['child_page_id'] ),
			),

			// トップページ未指定 / 投稿トップ未指定 / 固定ページの子ページ
			// HOME > 親ページ > 子ページ.
			array(
				'target_url' => get_permalink( $test_posts['child_page_id'] ),
			),

			// トップページに最新の投稿（投稿トップ未指定） / 子カテゴリー
			// HOME > 親カテゴリー > 子カテゴリー.
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_term_link( $test_posts['child_category_id'], 'category' ),
			),

			// トップページに最新の投稿 / 投稿トップページ無指定 / 記事ページ
			// HOME > 親カテゴリー > 子カテゴリー > 記事タイトル.
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_permalink( $test_posts['post_id'] ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定
			// HOME > 投稿トップの固定ページ名.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_permalink( $test_posts['home_page_id'] ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 子カテゴリー
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > 親カテゴリー > 子カテゴリー.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_term_link( $test_posts['child_category_id'], 'category' ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 投稿のないカテゴリーアーカイブページ
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > 投稿のないカテゴリー名.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => get_term_link( $test_posts['no_post_category_id'], 'category' ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 年別アーカイブ
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > アーカイブ名.
			array(
				'options'    => array(
					'page_on_front'  => $test_posts['front_page_id'],
					'show_on_front'  => 'page',
					'page_for_posts' => $test_posts['home_page_id'],
				),
				'target_url' => home_url() . '/?post_type=post&year=' . gmdate( 'Y' ),
			),

			// トップページに固定ページ / 投稿トップページ無指定 / 年別アーカイブ
			// HOME > アーカイブ名.
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => home_url() . '/?post_type=post&year=' . gmdate( 'Y' ),
			),

			// カスタム投稿タイプトップ
			// HOME > 投稿タイプ名.
			array(
				'target_url' => home_url() . '/?post_type=event',
			),

			// カスタム投稿タイプ / カスタム分類アーカイブ
			// HOME > 投稿タイプ名 > カスタム分類.
			array(
				'target_url' => get_term_link( $test_posts['event_term_id'] ),
			),

			// カスタム投稿タイプ / 年別アーカイブ
			// HOME > 投稿タイプ名 > アーカイブ名.
			array(
				'target_url' => home_url() . '/?post_type=event&year=' . gmdate( 'Y' ),
			),

			// カスタム投稿タイプ / 記事詳細
			// HOME > 投稿タイプ名 > カスタム分類 > 記事タイトル.
			array(
				'target_url' => get_permalink( $test_posts['event_post_id'] ),
			),
		);
		return $test_array;
	}

}
