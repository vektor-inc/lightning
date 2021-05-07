<?php
/**
 * Class BlogCardTest
 *
 * @package VK_Component_Button
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

	function test_get_blog_card() {
		$test_array = array(
			// WordPressで作られたサイト トップページ
			array(
				'url'         => 'https://www.vektor-inc.co.jp/',
				'correct'       => '[embed]https://www.vektor-inc.co.jp/[/embed]',
			),
			// WordPressで作られたサイト 下層ページ
			// array(
			// 	'url'         => 'https://www.vektor-inc.co.jp/service/',
			// 	'correct'       => '[embed]https://www.vektor-inc.co.jp/service/[/embed]',
			// ),
			// WordPressでは作られていないサイト
			// array(
			// 	'url'         => 'https://github.com/vektor-inc/lightning',
			// 	'correct'       => '[embed]https://github.com/vektor-inc/lightning[/embed]',
			// ),
		);
		/*
		the_contentをapply_filtersした時と比べてあげる
		*/
		foreach ( $test_array as $key => $value ) {
			$filter_correct = trim( apply_filters( 'the_content',  $value['correct'] ) );
			$filter_correct = str_replace( '<p></p>', '', $filter_correct );
			
			$result = VK_WP_Oembed_Blog_Card::get_blog_card( $value['url'] );
			
			$this->assertEquals( trim( $filter_correct ), trim( $result ) );
		}
	}

}
