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
	 * cache は 管理画面URLを貼り付けた時に自動で変換される文字列
	 */
	function test_vk_get_post_data_blog_card() {

		// the_contentのフィルターフックで自動に入るpタグを削除
		remove_filter( 'the_content', 'wpautop' );
		$test_array = array(
			// WordPressで作られたサイト トップページ
			array(
				'url'     => 'https://www.vektor-inc.co.jp/',
				'cache'   => '[embed]https://www.vektor-inc.co.jp/[/embed]',
				'correct' => apply_filters( 'the_content', '[embed]https://www.vektor-inc.co.jp/[/embed]' ),
			),
			// WordPressで作られたサイト 下層ページ
			array(
				'url'     => 'https://www.vektor-inc.co.jp/service/',
				'cache'   => '[embed]https://www.vektor-inc.co.jp/service/[/embed]',
				'correct' => apply_filters( 'the_content', '[embed]https://www.vektor-inc.co.jp/service/[/embed]' ),
			),
			// WordPressが許可しているプロバイダ−の場合
			array(
				'url'     => 'https://youtu.be/OCYupuj5HrQ',
				'cache'   => '<iframe loading="lazy" title="Lightning G3 クイックスタート" width="1140" height="641" src="https://www.youtube.com/embed/OCYupuj5HrQ?feature=oembed" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>',
				'correct' => apply_filters( 'the_content', '[embed]https://youtu.be/OCYupuj5HrQ[/embed]' ),
			),
		);
		foreach ( $test_array as $key => $value ) {
			$result = VK_WP_Oembed_Blog_Card::oembed_html( $value['cache'], $value['url'] );
			$this->assertEquals( $value['correct'], $result );
		}
		// wpautopフィルターフックを戻す
		add_filter( 'the_content', 'wpautop' );
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
			$output = "";
			$result = VK_WP_Oembed_Blog_Card::maybe_make_link( $output, $value['url'] );
			$this->assertEquals( $value['correct'], $result );
		}
		// wpautopフィルターフックを戻す
		add_filter( 'the_content', 'wpautop' );
	}

}
