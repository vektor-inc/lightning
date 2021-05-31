<?php
/**
 * Class BlogCardTest
 *
 * @package VK_WP_Oembed_Blog_Card
 *
 * cd /app
 * bash setup-phpunit.sh
 * source ~/.bashrc
 * 
 * cd $(wp theme path --dir lightning)
 * あるいは
 * cd wp-content/themes/lightning
 * 
 * phpunit
 */

/**
 * Sample test case.
 */
class BlogCardTest extends WP_UnitTestCase {

	/**
	 * oembed_html 内部リンク、WordPressで作られたサイトのテスト 
	 */
	function test_vk_get_post_data_blog_card() {
		// Create test post
		$post    = array(
			'post_title'    => 'test',
			'post_content'  => 'content',
			'post_name' => 'test',
			'post_status'   => 'publish',
		);
		$post_id = wp_insert_post( $post );

		// the_contentのフィルターフックで自動に入るpタグを削除
		remove_filter( 'the_content', 'wpautop' );
		$test_array = array(
			// WordPressで作られたサイト サイト内記事
			array(
				'url'     => get_permalink( $post_id ),
				'correct' => apply_filters( 'the_content', '[embed]' . get_permalink( $post_id ) .'[/embed]' ),
			),
			// WordPressで作られたサイト トップページ
			array(
				'url'     => 'https://www.vektor-inc.co.jp/',
				'correct' => apply_filters( 'the_content', '[embed]https://www.vektor-inc.co.jp/[/embed]' ),
			),
			// WordPressで作られたサイト 下層ページ
			array(
				'url'     => 'https://www.vektor-inc.co.jp/service/',
				'correct' => apply_filters( 'the_content', '[embed]https://www.vektor-inc.co.jp/service/[/embed]' ),
			),
		);
		foreach ( $test_array as $key => $value ) {
			$cache = "";
			$result = VK_WP_Oembed_Blog_Card::oembed_html( $cache, $value['url'] );
			$this->assertEquals( $value['correct'], $result );
		}
		// wpautopフィルターフックを戻す
		add_filter( 'the_content', 'wpautop' );
		wp_delete_post( $post_id );
	}

	/**
	 * embed_maybe_make_link 外部リンクのテスト
	 */
	function test_vk_get_blog_card() {
		// the_contentのフィルターフックで自動に入るpタグを削除
		remove_filter( 'the_content', 'wpautop' );
		$test_array = array(
			// WordPressでは作られていないサイト
			array(
				'url'     => 'https://github.com/vektor-inc/lightning',
				'correct' => apply_filters( 'the_content', '[embed]https://github.com/vektor-inc/lightning[/embed]' ),
			),
			// 外部からの接続を拒否しているサイト
			array(
				'url'     => 'https://www.whitehouse.gov/',
				'correct' => apply_filters( 'the_content', '[embed]https://www.whitehouse.gov/[/embed]' ),
			),
			// HTMLの文字コードが異なるサイト
			array(
				'url'     => 'http://abehiroshi.la.coocan.jp/',
				'correct' => apply_filters( 'the_content', '[embed]http://abehiroshi.la.coocan.jp/[/embed]' ),
			),
		);
		foreach ( $test_array as $key => $value ) {
			$result = VK_WP_Oembed_Blog_Card::vk_get_blog_card( $value['url'] );
			$this->assertEquals( $value['correct'], $result );
		}
		// wpautopフィルターフックを戻す
		add_filter( 'the_content', 'wpautop' );
	}

}
