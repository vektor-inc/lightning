<?php
/**
 * Test for No Posts Message
 *
 * @package Lightning G3
 */

/**
 * Get_No_Post_Test_Test
 */
class Get_No_Post_Test_Test extends WP_UnitTestCase {

	/**
	 * Check lightning_get_no_post_text()
	 *
	 * @return void
	 */
	public function test_get_no_post_text() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_get_no_post_text()' . PHP_EOL;
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
			'post_status'   => 'draft',
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
			'post_status'  => 'draft',
			'post_content' => 'content',
		);
		$event_post_id = wp_insert_post( $post );
		// set event category to event post.
		wp_set_object_terms( $event_post_id, 'event_category_name', 'event_cat' );

		/*** ↑↑ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) */

		$test_array = array(

			// Post top no post.
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $home_page_id ),
				'correct'    => 'There are no post_tops.',
			),
			// Event top no post.
			array(
				'target_url' => get_post_type_archive_link( 'event' ),
				'correct'    => 'There are no Events.',
			),
			// keyword search.
			array(
				'target_url' => home_url() . '/?s=aaa',
				'correct'    => 'There is no corresponding no Post.',
			),
			// Filter search with keywords.
			array(
				'target_url' => home_url() . '/?s=aaaa&post_type=event',
				'correct'    => 'There is no corresponding no Event.',
			),

		);
		foreach ( $test_array as $value ) {
			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}

			// Move to test page.
			$this->go_to( $value['target_url'] );

			$return = lightning_get_no_post_text();
			print 'return  :' . esc_html( $return ) . PHP_EOL;
			print 'correct :' . esc_html( $value['correct'] ) . PHP_EOL;
			$this->assertEquals( $return, $value['correct'] );

		}
	}
}
