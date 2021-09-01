<?php
/**
 * Test for PHP Error
 *
 * @package Lightning G3
 */

/**
 * PHP_Fatal_Error_Test
 */
class PHP_Fatal_Error_Test extends WP_UnitTestCase {

	/**
	 * Check Fatal Error
	 *
	 * @return void
	 */
	public function test_php_faral_error() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'PHP Fatal Error Check' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print PHP_EOL;

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

		// Create test category.
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

		// Create test term.
		$args          = array(
			'slug' => 'event_category_name',
		);
		$term_info     = wp_insert_term( 'event_category_name', 'event_cat', $args );
		$event_term_id = $term_info['term_id'];

		// Create test post.
		$post    = array(
			'post_title'    => 'test',
			'post_status'   => 'publish',
			'post_content'  => 'content',
			'post_category' => array( $parent_category_id ),
		);
		$post_id = wp_insert_post( $post );
		// 投稿にカテゴリー指定.
		wp_set_object_terms( $post_id, 'child_category', 'category' );

		// Create test page.
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

		// Create test home page.
		$post         = array(
			'post_title'   => 'post_top',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$home_page_id = wp_insert_post( $post );

		// Create test home page.
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
		// set event category to event post.
		wp_set_object_terms( $event_post_id, 'event_category_name', 'event_cat' );

		/*** ↑↑ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) */

		/****************************************
		 * Test Array
		 */

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
				'target_url' => get_permalink( $parent_page_id ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 固定ページ
			// トップに指定した固定ページ名 > 固定ページ名.
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $parent_page_id ),
			),

			// 固定ページの子ページ
			// トップに指定した固定ページ名 > 親ページ > 子ページ.
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $child_page_id ),
			),

			// トップページ未指定 / 投稿トップ未指定 / 固定ページの子ページ
			// HOME > 親ページ > 子ページ.
			array(
				'target_url' => get_permalink( $child_page_id ),
			),

			// トップページに最新の投稿（投稿トップ未指定） / 子カテゴリー
			// HOME > 親カテゴリー > 子カテゴリー.
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_term_link( $child_category_id, 'category' ),
			),

			// トップページに最新の投稿 / 投稿トップページ無指定 / 記事ページ
			// HOME > 親カテゴリー > 子カテゴリー > 記事タイトル.
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_permalink( $post_id ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定
			// HOME > 投稿トップの固定ページ名.
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $home_page_id ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 子カテゴリー
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > 親カテゴリー > 子カテゴリー.
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_term_link( $child_category_id, 'category' ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 投稿のないカテゴリーアーカイブページ
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > 投稿のないカテゴリー名.
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_term_link( $no_post_category_id, 'category' ),
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 年別アーカイブ
			// トップに指定した固定ページ名 > 投稿トップの固定ページ名 > アーカイブ名.
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
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
				'target_url' => get_term_link( $event_term_id ),
			),

			// カスタム投稿タイプ / 年別アーカイブ
			// HOME > 投稿タイプ名 > アーカイブ名.
			array(
				'target_url' => home_url() . '/?post_type=event&year=' . gmdate( 'Y' ),
			),

			// カスタム投稿タイプ / 記事詳細
			// HOME > 投稿タイプ名 > カスタム分類 > 記事タイトル.
			array(
				'target_url' => get_permalink( $event_post_id ),
			),
		);

		foreach ( $test_array as $value ) {
			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}
			$vk_css_tree_shaking_array = array();
			lightning_css_tree_shaking_array( $vk_css_tree_shaking_array );

			// Move to test page.
			$this->go_to( $value['target_url'] );

			print PHP_EOL;
			print '-------------------' . PHP_EOL;
			print esc_url( $value['target_url'] ) . PHP_EOL;
			print '-------------------' . PHP_EOL;
			print PHP_EOL;
			require get_theme_file_path( 'index.php' );

			// PHPのエラーが発生するかどうかのチェックなので本当は不要.
			$this->assertEquals( true, true );

			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					delete_option( $option_key );
				}
			}
		}

	}
}
