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
	 * テスト前処理: 外部サイトへの HTTP リクエストを固定レスポンスに差し替えるモックを登録
	 *
	 * CI 環境から外部 OGP サイトへ接続できるかどうか、および接続できた場合でも
	 * 1リクエスト毎にレスポンスが変わり得ることで `oembed_html` / `maybe_make_link`
	 * の出力が変動し、テストが flaky 化していた。
	 * `pre_http_request` でドメイン毎の固定レスポンスを返すことで、
	 * 実通信なしで各ケースの検証経路を安定させる。
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
		add_filter( 'pre_http_request', array( $this, 'mock_http_request' ), 10, 3 );
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
		remove_filter( 'pre_http_request', array( $this, 'mock_http_request' ), 10 );
		parent::tear_down();
	}

	/**
	 * 外部サイト宛ての HTTP リクエストをドメイン毎の固定レスポンスに差し替えるモック
	 *
	 * `pre_http_request` フィルタは false 以外を返すと実際の HTTP 通信を行わずに
	 * その値を結果として使う仕様。
	 * これを利用して、テストが依存する外部ドメインについてのみ固定値を返し、
	 * それ以外（サイト内リンクや YouTube oEmbed など）はそのまま通すために `$pre` を返す。
	 *
	 * ドメイン毎の固定内容は以下の通りで、各テストケースが検証したい経路を維持している。
	 *
	 * - vektor-inc.co.jp    : `WP_Error`（取得失敗 => URL のみのフォールバック表示）
	 * - whitehouse.gov      : `WP_Error`（外部からの接続を拒否しているサイトの想定）
	 * - github.com          : 200 + UTF-8 の OGP 入り HTML（ブログカード生成経路）
	 * - abehiroshi.la.coocan.jp : 200 + Shift_JIS の HTML（`encode()` の文字コード変換）
	 *
	 * 返す配列は `wp_remote_get()` の戻り値と同じ形にしておく必要がある
	 * （`VK_WP_Oembed_Blog_Card::vk_get_blog_card()` が
	 * `$response['response']['code']` と `$response['body']` を参照するため）。
	 *
	 * @param false|array|WP_Error $pre  既存の pre_http_request の戻り値（通常 false）.
	 * @param array                $args HTTP リクエストの引数.
	 * @param string               $url  リクエスト先 URL.
	 * @return false|array|WP_Error 対象ドメインなら固定レスポンス、それ以外は $pre をそのまま返す
	 */
	public function mock_http_request( $pre, $args, $url ) {
		// クエリ文字列やパスにドメイン名が含まれているケースで誤反応しないよう、
		// `wp_parse_url` で host を厳密に取り出してマッチングする.
		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( ! is_string( $host ) ) {
			return $pre;
		}

		// 取得失敗（WP_Error）を返すドメイン
		// vektor-inc.co.jp は test_vk_get_post_data_blog_card() 用、
		// whitehouse.gov は「外部からの接続を拒否しているサイト」のケース用.
		$error_domains = array( 'vektor-inc.co.jp', 'whitehouse.gov' );
		foreach ( $error_domains as $domain ) {
			if ( $this->is_matched_host( $host, $domain ) ) {
				return new \WP_Error( 'http_request_failed', 'mocked' );
			}
		}

		// WordPress で作られていないサイトのブログカード生成用の固定 HTML（UTF-8）
		// oEmbed discovery に拾われて経路が変わらないよう、
		// `application/json+oembed` の link 要素はあえて含めていない.
		if ( $this->is_matched_host( $host, 'github.com' ) ) {
			$body = '<!DOCTYPE html><html lang="en"><head>'
				. '<meta charset="utf-8">'
				. '<title>GitHub - vektor-inc/lightning: Lightning is a WordPress theme.</title>'
				. '<meta property="og:description" content="Lightning is a WordPress theme.">'
				. '<meta property="og:image" content="https://github.com/vektor-inc/lightning/og-image.png">'
				. '<meta property="og:site_name" content="GitHub">'
				. '<link rel="icon" href="https://github.com/favicon.ico">'
				. '</head><body></body></html>';
			return $this->get_mock_response( $body );
		}

		// HTML の文字コードが UTF-8 でないサイト用の固定 HTML
		// `VK_WP_Oembed_Blog_Card::encode()` による文字コード変換を検証するため、
		// 日本語を含む HTML を Shift_JIS に変換して返す.
		if ( $this->is_matched_host( $host, 'abehiroshi.la.coocan.jp' ) ) {
			$body = '<!DOCTYPE html><html lang="ja"><head>'
				. '<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">'
				. '<title>阿部寛のホームページ</title>'
				. '<meta property="og:description" content="阿部寛の公式ホームページです。">'
				. '<meta property="og:site_name" content="阿部寛のホームページ">'
				. '</head><body></body></html>';
			// mb_convert_encoding が無い環境では変換できないため UTF-8 のまま返す.
			if ( function_exists( 'mb_convert_encoding' ) ) {
				$body = mb_convert_encoding( $body, 'SJIS', 'UTF-8' );
			}
			return $this->get_mock_response( $body );
		}

		// それ以外のドメインは通常通り処理させる.
		return $pre;
	}

	/**
	 * host が対象ドメイン（サブドメイン含む）に一致するか判定する
	 *
	 * 完全一致に加えて、末尾一致かつ直前が `.` の場合も一致とみなすことで
	 * サブドメイン（例: www.vektor-inc.co.jp）も拾う。
	 *
	 * @param string $host   `wp_parse_url()` で取り出した host.
	 * @param string $domain 判定対象のドメイン（例: vektor-inc.co.jp）.
	 * @return bool 一致すれば true。
	 */
	private function is_matched_host( $host, $domain ) {
		return (bool) preg_match( '/(^|\.)' . preg_quote( $domain, '/' ) . '$/i', $host );
	}

	/**
	 * `wp_remote_get()` の戻り値と同じ形の、ステータス 200 の固定レスポンスを組み立てる
	 *
	 * @param string $body レスポンスボディとして返す HTML.
	 * @return array wp_remote_get() 互換のレスポンス配列。
	 */
	private function get_mock_response( $body ) {
		return array(
			'headers'  => array(),
			'body'     => $body,
			'response' => array(
				'code'    => 200,
				'message' => 'OK',
			),
			'cookies'  => array(),
			'filename' => null,
		);
	}

	/**
	 * oembed_html 内部リンク、WordPressで作られたサイトのテスト
	 * cache は 管理画面URLを貼り付けた時に自動で変換される文字列
	 *
	 * vektor-inc.co.jp の OGP 取得は `mock_http_request` で失敗固定しているため、
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
	 *
	 * 各 URL のレスポンスは `mock_http_request` で固定しているため、
	 * 外部サイトの状態に依存せず、期待値（`correct`）と実出力が同じ経路を通る。
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
