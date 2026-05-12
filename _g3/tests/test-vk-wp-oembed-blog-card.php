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
	 * テスト前処理: vektor-inc.co.jp への HTTP リクエストを失敗固定するモックを登録
	 *
	 * CI 環境から外部 OGP サイトへ接続できるかどうかで `oembed_html` / `maybe_make_link`
	 * の出力が変動し、テストが flaky 化していた。
	 * `pre_http_request` で `WP_Error` を返すことで、常に「OGP 取得失敗時の
	 * URL のみフォールバック表示」ケースを検証できるようにする。
	 *
	 * 期待値生成側（`apply_filters( 'the_content', ... )` 内部の oEmbed discovery）にも
	 * 同じモックを効かせる必要があるため、`set_up` 段階でフィルタを登録している。
	 */
	public function set_up() {
		parent::set_up();
		add_filter( 'pre_http_request', array( $this, 'mock_http_fail' ), 10, 3 );
	}

	/**
	 * テスト後処理: モック用フィルタを解除する
	 */
	public function tear_down() {
		remove_filter( 'pre_http_request', array( $this, 'mock_http_fail' ), 10 );
		parent::tear_down();
	}

	/**
	 * vektor-inc.co.jp 宛ての HTTP リクエストを失敗として固定するためのモック
	 *
	 * `pre_http_request` フィルタは false 以外を返すと実際の HTTP 通信を行わずに
	 * その値を結果として使う仕様。
	 * vektor-inc.co.jp ドメインのリクエストのみ `WP_Error` を返して失敗扱いとし、
	 * YouTube oEmbed や内部リンクなどはそのまま通すために `$pre` を返す。
	 *
	 * @param false|array|WP_Error $pre  既存の pre_http_request の戻り値（通常 false）
	 * @param array                $args HTTP リクエストの引数
	 * @param string               $url  リクエスト先 URL
	 * @return false|array|WP_Error vektor-inc.co.jp なら WP_Error、それ以外は $pre をそのまま返す
	 */
	public function mock_http_fail( $pre, $args, $url ) {
		// vektor-inc.co.jp 宛てのリクエストのみ失敗扱いに固定する
		if ( false !== strpos( $url, 'vektor-inc.co.jp' ) ) {
			return new \WP_Error( 'http_request_failed', 'mocked' );
		}
		// それ以外のドメインは通常通り処理させる
		return $pre;
	}

	/**
	 * oembed_html 内部リンク、WordPressで作られたサイトのテスト
	 * cache は 管理画面URLを貼り付けた時に自動で変換される文字列
	 *
	 * vektor-inc.co.jp の OGP 取得は `mock_http_fail` で失敗固定しているため、
	 * 期待値（`correct`）も実出力もフォールバック HTML（URL のみのリンク）となる。
	 */
	function test_vk_get_post_data_blog_card() {
		// Create test post
		$post    = array(
			'post_title'   => 'test',
			'post_content' => 'content',
			'post_name'    => 'test',
			'post_status'  => 'publish',
		);
		$post_id = wp_insert_post( $post );

		// the_contentのフィルターフックで自動に入るpタグを削除
		remove_filter( 'the_content', 'wpautop' );
		$test_array = array(
			// WordPressで作られたサイト サイト内記事

			array(
				'url'     => get_permalink( $post_id ),
				'cache'   => '[embed]' . get_permalink( $post_id ) . '[/embed]',
				'correct' => apply_filters( 'the_content', '[embed]' . get_permalink( $post_id ) . '[/embed]' ),
			),
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
				'cache'   => '<iframe loading="lazy" title="WordPressテーマ Lightning (G3) クイックスタート【公式】" width="1140" height="641" src="https://www.youtube.com/embed/OCYupuj5HrQ?feature=oembed" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
				'correct' => apply_filters( 'the_content', '[embed]https://youtu.be/OCYupuj5HrQ[/embed]' ),
			),
		);
		foreach ( $test_array as $key => $value ) {
			$result = VK_WP_Oembed_Blog_Card::oembed_html( $value['cache'], $value['url'] );
			if ( function_exists( 'wp_img_tag_add_loading_optimization_attrs' ) ) {
				$result = wp_img_tag_add_loading_optimization_attrs( $result, 'custom' );
			}
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
			$output = '';
			$result = VK_WP_Oembed_Blog_Card::maybe_make_link( $output, $value['url'] );
			if ( function_exists( 'wp_img_tag_add_loading_optimization_attrs' ) ) {
				$result = wp_img_tag_add_loading_optimization_attrs( $result, 'custom' );
			}
			$this->assertEquals( $value['correct'], $result );
		}
		// wpautopフィルターフックを戻す
		add_filter( 'the_content', 'wpautop' );
	}
}
