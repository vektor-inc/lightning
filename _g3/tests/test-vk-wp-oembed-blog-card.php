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
	 * `the_content` に対する `wpautop` フィルタが set_up 時点で
	 * 登録されていたかどうかを記憶するフラグ。
	 *
	 * 各テストは検証時に `remove_filter( 'the_content', 'wpautop' )` →
	 * 末尾で `add_filter( 'the_content', 'wpautop' )` を呼ぶ運用だが、
	 * アサーション失敗で途中 abort した場合に復元が漏れ、後続テストに
	 * 影響することを防ぐため tear_down で確実に復元する。
	 *
	 * @var bool
	 */
	private $had_wpautop = false;

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
	 *
	 * あわせて、`the_content` に対する `wpautop` フィルタの登録状態を記憶しておく。
	 * 各テストは検証ロジックの直前で `remove_filter` し、末尾で `add_filter` で
	 * 戻す運用だが、テストが途中で失敗した場合は復元が漏れる。
	 * tear_down 側で「元々あったのに今ない」場合のみ復元することで、
	 * 後続テストへの汚染を防ぐ。
	 */
	public function set_up() {
		parent::set_up();
		add_filter( 'pre_http_request', array( $this, 'mock_http_fail' ), 10, 3 );
		$this->had_wpautop = false !== has_filter( 'the_content', 'wpautop' );
	}

	/**
	 * テスト後処理: モック用フィルタを解除し、`wpautop` の状態を必要なら復元する
	 *
	 * 通常はテスト本体側で `add_filter( 'the_content', 'wpautop' )` を戻しているが、
	 * アサーション失敗等で途中終了した場合に備えて、set_up 時点で登録されていた
	 * 場合のみ ここで再登録して状態を揃える。
	 */
	public function tear_down() {
		if ( $this->had_wpautop && false === has_filter( 'the_content', 'wpautop' ) ) {
			add_filter( 'the_content', 'wpautop' );
		}
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
		// クエリ文字列やパスに 'vektor-inc.co.jp' が含まれているケースで誤反応しないよう、
		// `wp_parse_url` で host を厳密に取り出してマッチングする。
		// 末尾一致＋直前が `.` であれば許可することでサブドメイン（例: blog.vektor-inc.co.jp）も拾う。
		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( is_string( $host ) && preg_match( '/(^|\.)vektor-inc\.co\.jp$/i', $host ) ) {
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
