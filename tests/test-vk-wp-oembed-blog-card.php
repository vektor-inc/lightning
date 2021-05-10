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

	function test_vk_get_blog_card() {
		// the_contentのフィルターフックで自動に入るpタグを削除
		remove_filter( 'the_content', 'wpautop' );
		$test_array = array(
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
			// WordPressでは作られていないサイト
			array(
				'url'     => 'https://github.com/vektor-inc/lightning',
				'correct' => apply_filters( 'the_content', '[embed]https://github.com/vektor-inc/lightning[/embed]' ),
			),
		);
		/*
		the_contentをapply_filtersした時と比べてあげる
		WordPressが自動で入れるpタグを除外
		*/
		foreach ( $test_array as $key => $value ) {
			$result = VK_WP_Oembed_Blog_Card::vk_get_blog_card( $value['url'] );
			$this->assertEquals( $value['correct'], $result );
		}
		// wpautopフィルターフックを戻す
		add_filter( 'the_content', 'wpautop' );
	}

}
