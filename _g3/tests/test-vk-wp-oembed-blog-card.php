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
	 * CI 環境から外部サイトへ接続できるかどうか、および接続できた場合でも
	 * 1リクエスト毎にレスポンスが変わり得ることで `oembed_html` / `maybe_make_link`
	 * の出力が変動し、テストが flaky 化していた。
	 * `pre_http_request` でドメイン毎の固定レスポンスを返すことで、
	 * 実通信なしで各ケースの検証経路を安定させる。
	 *
	 * 期待値生成側（`apply_filters( 'the_content', ... )` 内部の oEmbed 取得）にも
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
	 * その値を結果として使う仕様.
	 * これを利用して、テストが依存する外部ドメインについてのみ固定値を返し、
	 * それ以外のドメインはそのまま通すために `$pre` を返す.
	 *
	 * どのドメインに何を返すかは `get_mock_http_map()` を唯一の定義元とする.
	 *
	 * @param false|array|WP_Error $pre  既存の pre_http_request の戻り値（通常 false）.
	 * @param array                $args HTTP リクエストの引数.
	 * @param string               $url  リクエスト先 URL.
	 * @return false|array|WP_Error 対象ドメインなら固定レスポンス、それ以外は $pre をそのまま返す.
	 */
	public function mock_http_request( $pre, $args, $url ) {
		// クエリ文字列やパスにドメイン名が含まれているケースで誤反応しないよう、
		// `wp_parse_url` で host を厳密に取り出してマッチングする.
		$host = wp_parse_url( $url, PHP_URL_HOST );
		if ( ! is_string( $host ) ) {
			return $pre;
		}
		// 末尾ドット付きの絶対 FQDN 表記（例: github.com.）も同一ホストとして扱う.
		$host = rtrim( $host, '.' );

		foreach ( $this->get_mock_http_map() as $mock ) {
			foreach ( $mock['domains'] as $domain ) {
				if ( $this->is_matched_host( $host, $domain ) ) {
					return $mock['response'];
				}
			}
		}

		// それ以外のドメインは通常通り処理させる.
		return $pre;
	}

	/**
	 * モックするドメインと、そのドメインに返す固定レスポンスの対応表を返す
	 *
	 * 各要素は以下のキーを持つ.
	 *
	 * - `domains`  : 対象ドメインの配列（サブドメインも一致する）.
	 * - `response` : `wp_remote_get()` の戻り値と同じ形の配列、または `WP_Error`.
	 *
	 * `VK_WP_Oembed_Blog_Card::vk_get_blog_card()` は
	 * `$response['response']['code']` と `$response['body']` を参照するため、
	 * 配列を返す場合は `get_mock_response()` で組み立てる.
	 *
	 * @return array ドメインと固定レスポンスの対応表.
	 */
	private function get_mock_http_map() {
		// WordPress で作られていないサイトのブログカード生成用の固定 HTML（UTF-8）.
		// oEmbed discovery に拾われて経路が変わらないよう、
		// `application/json+oembed` の link 要素はあえて含めていない.
		$github_html = '<!DOCTYPE html><html lang="en"><head>'
			. '<meta charset="utf-8">'
			. '<title>GitHub - vektor-inc/lightning: Lightning is a WordPress theme.</title>'
			. '<meta property="og:description" content="Lightning is a WordPress theme.">'
			. '<meta property="og:image" content="https://github.com/vektor-inc/lightning/og-image.png">'
			. '<meta property="og:site_name" content="GitHub">'
			. '<link rel="icon" href="https://github.com/favicon.ico">'
			. '</head><body></body></html>';

		// HTML の文字コードが UTF-8 でないサイト用の固定 HTML.
		// `VK_WP_Oembed_Blog_Card::encode()` による文字コード変換を検証するため、
		// 日本語を含む HTML を Shift_JIS に変換して返す.
		$sjis_html = '<!DOCTYPE html><html lang="ja"><head>'
			. '<meta http-equiv="Content-Type" content="text/html; charset=Shift_JIS">'
			. '<title>阿部寛のホームページ</title>'
			. '<meta property="og:description" content="阿部寛の公式ホームページです。">'
			. '<meta property="og:site_name" content="阿部寛のホームページ">'
			. '</head><body></body></html>';
		// mb_convert_encoding が無い環境では変換できないため UTF-8 のまま返す.
		if ( function_exists( 'mb_convert_encoding' ) ) {
			$sjis_html = mb_convert_encoding( $sjis_html, 'SJIS', 'UTF-8' );
		}

		// YouTube の oEmbed レスポンス.
		// youtu.be は WordPress に登録済みのプロバイダーのため、
		// discovery ではなく oEmbed エンドポイントが直接叩かれる.
		// `WP_oEmbed::_fetch_with_format()` は content-type を見ずに body を JSON としてパースする.
		// 出力される iframe の `loading="lazy"` と `title=` はコア側の
		// `wp_filter_oembed_result()` が付与するため、ここでは含めない.
		$youtube_json = wp_json_encode(
			array(
				'type'          => 'video',
				'version'       => '1.0',
				'title'         => 'WordPressテーマ Lightning (G3) クイックスタート【公式】',
				'provider_name' => 'YouTube',
				'provider_url'  => 'https://www.youtube.com/',
				'width'         => 1140,
				'height'        => 641,
				'html'          => '<iframe width="1140" height="641" src="https://www.youtube.com/embed/OCYupuj5HrQ?feature=oembed" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
			)
		);

		return array(
			// 接続自体に失敗するサイト（WP_Error）.
			// test_vk_get_post_data_blog_card() の外部 URL ケース用.
			array(
				'domains'  => array( 'vektor-inc.co.jp' ),
				'response' => new WP_Error( 'http_request_failed', 'mocked' ),
			),
			// 外部からの接続を拒否しているサイト.
			// `vk_get_blog_card()` の「200 / 304 以外のステータスコード」分岐を通すため、
			// WP_Error ではなく 403 のレスポンスを返す.
			array(
				'domains'  => array( 'whitehouse.gov' ),
				'response' => $this->get_mock_response( '', 403 ),
			),
			// WordPress で作られていないサイト（ブログカード生成経路）.
			array(
				'domains'  => array( 'github.com' ),
				'response' => $this->get_mock_response( $github_html ),
			),
			// HTML の文字コードが UTF-8 でないサイト（`encode()` の文字コード変換）.
			array(
				'domains'  => array( 'abehiroshi.la.coocan.jp' ),
				'response' => $this->get_mock_response( $sjis_html, 200, 'text/html; charset=Shift_JIS' ),
			),
			// WordPress が許可しているプロバイダー（YouTube）.
			array(
				'domains'  => array( 'youtube.com', 'youtu.be' ),
				'response' => $this->get_mock_response( $youtube_json, 200, 'application/json' ),
			),
		);
	}

	/**
	 * host が対象ドメイン（サブドメイン含む）に一致するか判定する
	 *
	 * 完全一致に加えて、末尾一致かつ直前が `.` の場合も一致とみなすことで
	 * サブドメイン（例: www.vektor-inc.co.jp）も拾う.
	 * 末尾ドット付きの host は呼び出し側で除去済みである前提.
	 *
	 * @param string $host   `wp_parse_url()` で取り出した host.
	 * @param string $domain 判定対象のドメイン（例: vektor-inc.co.jp）.
	 * @return bool 一致すれば true.
	 */
	private function is_matched_host( $host, $domain ) {
		return (bool) preg_match( '/(^|\.)' . preg_quote( $domain, '/' ) . '$/i', $host );
	}

	/**
	 * `wp_remote_get()` の戻り値と同じ形の固定レスポンスを組み立てる
	 *
	 * @param string $body         レスポンスボディ.
	 * @param int    $code         HTTP レスポンスステータスコード.
	 * @param string $content_type Content-Type ヘッダの値.
	 * @return array wp_remote_get() 互換のレスポンス配列.
	 */
	private function get_mock_response( $body, $code = 200, $content_type = 'text/html; charset=UTF-8' ) {
		// ステータスコードに対応する reason phrase.
		$messages = array(
			200 => 'OK',
			403 => 'Forbidden',
		);
		return array(
			'headers'  => array( 'content-type' => $content_type ),
			'body'     => $body,
			'response' => array(
				'code'    => $code,
				'message' => isset( $messages[ $code ] ) ? $messages[ $code ] : '',
			),
			'cookies'  => array(),
			'filename' => null,
		);
	}

	/**
	 * 生成結果に対する包含・非包含のアサーションを実行する
	 *
	 * `correct`（`the_content` 経由の期待値）との比較だけでは、
	 * 期待値も実測値も同じ関数の同じ経路に行き着くため恒真になってしまう.
	 * そのため、どの経路を通って何が出力されたかをリテラルで固定して検証する.
	 *
	 * @param array  $test_case テストケース（`contains` / `not_contains` / `matches` / `test_condition_name`）.
	 * @param string $result 実際に生成された HTML.
	 * @return void
	 */
	private function assert_blog_card_contents( $test_case, $result ) {
		// 含まれているべき文字列.
		if ( ! empty( $test_case['contains'] ) ) {
			foreach ( $test_case['contains'] as $needle ) {
				$this->assertStringContainsString( $needle, $result, $test_case['test_condition_name'] );
			}
		}
		// 一致すべき正規表現（テンプレートのインデントを挟む箇所の検証に使う）.
		if ( ! empty( $test_case['matches'] ) ) {
			foreach ( $test_case['matches'] as $pattern ) {
				$this->assertMatchesRegularExpression( $pattern, $result, $test_case['test_condition_name'] );
			}
		}
		// 含まれていてはいけない文字列.
		if ( ! empty( $test_case['not_contains'] ) ) {
			foreach ( $test_case['not_contains'] as $needle ) {
				$this->assertStringNotContainsString( $needle, $result, $test_case['test_condition_name'] );
			}
		}
	}

	/**
	 * oembed_html 内部リンク、WordPressで作られたサイトのテスト
	 * cache は 管理画面URLを貼り付けた時に自動で変換される文字列
	 *
	 * 外部サイトへのリクエストはすべて `mock_http_request` で固定しているため、
	 * 実通信は発生せず、外部サイトの状態に結果が左右されない.
	 * vektor-inc.co.jp は取得失敗固定のため、URL のみのフォールバック表示となる.
	 */
	public function test_vk_get_post_data_blog_card() {
		// テスト用の投稿を作成.
		$post    = array(
			'post_title'   => 'test',
			'post_content' => 'content',
			'post_name'    => 'test',
			'post_status'  => 'publish',
		);
		$post_id = wp_insert_post( $post );

		// the_contentのフィルターフックで自動に入るpタグを削除.
		remove_filter( 'the_content', 'wpautop' );
		$test_array = array(
			// WordPressで作られたサイト サイト内記事.
			array(
				'test_condition_name' => 'サイト内記事の URL の場合 => 投稿情報から生成したブログカード',
				'url'                 => get_permalink( $post_id ),
				'cache'               => '[embed]' . get_permalink( $post_id ) . '[/embed]',
				'correct'             => apply_filters( 'the_content', '[embed]' . get_permalink( $post_id ) . '[/embed]' ),
				'contains'            => array(
					'class="blog-card"',
					'>test</a>',
					'content',
				),
				'not_contains'        => array( 'vk-wp-oembed-blog-card-url-template' ),
			),
			// WordPressで作られたサイト トップページ.
			array(
				'test_condition_name' => '外部サイト（取得失敗）のトップページの場合 => URL のみのフォールバック表示',
				'url'                 => 'https://www.vektor-inc.co.jp/',
				'cache'               => '[embed]https://www.vektor-inc.co.jp/[/embed]',
				'correct'             => apply_filters( 'the_content', '[embed]https://www.vektor-inc.co.jp/[/embed]' ),
				'contains'            => array(
					'vk-wp-oembed-blog-card-url-template',
					'https://www.vektor-inc.co.jp/',
				),
				'not_contains'        => array( 'class="blog-card"' ),
			),
			// WordPressで作られたサイト 下層ページ.
			array(
				'test_condition_name' => '外部サイト（取得失敗）の下層ページの場合 => URL のみのフォールバック表示',
				'url'                 => 'https://www.vektor-inc.co.jp/service/',
				'cache'               => '[embed]https://www.vektor-inc.co.jp/service/[/embed]',
				'correct'             => apply_filters( 'the_content', '[embed]https://www.vektor-inc.co.jp/service/[/embed]' ),
				'contains'            => array(
					'vk-wp-oembed-blog-card-url-template',
					'https://www.vektor-inc.co.jp/service/',
				),
				'not_contains'        => array( 'class="blog-card"' ),
			),
			// WordPressが許可しているプロバイダ−の場合.
			array(
				'test_condition_name' => 'WordPress が許可しているプロバイダー（YouTube）の場合 => ブログカードにせず埋め込み iframe',
				'url'                 => 'https://youtu.be/OCYupuj5HrQ',
				'cache'               => '<iframe loading="lazy" title="WordPressテーマ Lightning (G3) クイックスタート【公式】" width="1140" height="641" src="https://www.youtube.com/embed/OCYupuj5HrQ?feature=oembed" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>',
				'correct'             => apply_filters( 'the_content', '[embed]https://youtu.be/OCYupuj5HrQ[/embed]' ),
				'contains'            => array(
					'https://www.youtube.com/embed/OCYupuj5HrQ?feature=oembed',
					'title="WordPressテーマ Lightning (G3) クイックスタート【公式】"',
				),
				'not_contains'        => array( 'class="blog-card"' ),
			),
		);
		foreach ( $test_array as $key => $value ) {
			$result = VK_WP_Oembed_Blog_Card::oembed_html( $value['cache'], $value['url'] );
			if ( function_exists( 'wp_img_tag_add_loading_optimization_attrs' ) ) {
				$result = wp_img_tag_add_loading_optimization_attrs( $result, 'custom' );
			}
			$this->assertEquals( $value['correct'], $result, $value['test_condition_name'] );
			// `correct` との比較は同じ経路同士の比較になり得るため、経路をリテラルで固定して検証する.
			$this->assert_blog_card_contents( $value, $result );
		}
		// wpautopフィルターフックを戻す.
		add_filter( 'the_content', 'wpautop' );
		wp_delete_post( $post_id );
	}

	/**
	 * embed_maybe_make_link 外部リンクのテスト
	 *
	 * 各 URL のレスポンスは `mock_http_request` で固定しているため、
	 * 外部サイトの状態に依存せず結果が決まる.
	 */
	public function test_vk_get_blog_card() {
		// 文字コード変換のケースが形骸化しないよう、モックが本当に Shift_JIS
		// （UTF-8 としては不正なバイト列）を返していることを先に確認する.
		// UTF-8 のまま返すと `encode()` が実質的に何もしない状態になり、変換の検証にならない.
		$sjis_response = $this->mock_http_request( false, array(), 'http://abehiroshi.la.coocan.jp/' );
		$this->assertFalse(
			mb_check_encoding( $sjis_response['body'], 'UTF-8' ),
			'abehiroshi.la.coocan.jp のモックが UTF-8 を返しており、encode() の文字コード変換が検証されない'
		);

		// the_contentのフィルターフックで自動に入るpタグを削除.
		remove_filter( 'the_content', 'wpautop' );
		$test_array = array(
			// WordPressでは作られていないサイト.
			array(
				'test_condition_name' => 'WordPress で作られていないサイト（取得成功）の場合 => OGP からブログカードを生成',
				'url'                 => 'https://github.com/vektor-inc/lightning',
				'correct'             => apply_filters( 'the_content', '[embed]https://github.com/vektor-inc/lightning[/embed]' ),
				'contains'            => array(
					'class="blog-card"',
					'>GitHub - vektor-inc/lightning: Lightning is a WordPress theme.</a>',
					'Lightning is a WordPress theme.',
					'https://github.com/vektor-inc/lightning/og-image.png',
					'https://github.com/favicon.ico',
				),
				// og:site_name がサイト名として出力されていること（ドメイン名へのフォールバックではないこと）.
				'matches'             => array( '/blog-card-site-title.*?>\s*GitHub\s*<\/a>/s' ),
				'not_contains'        => array( 'vk-wp-oembed-blog-card-url-template' ),
			),
			// 外部からの接続を拒否しているサイト（403）.
			array(
				'test_condition_name' => '外部からの接続を拒否しているサイト（403）の場合 => URL のみのフォールバック表示',
				'url'                 => 'https://www.whitehouse.gov/',
				'correct'             => apply_filters( 'the_content', '[embed]https://www.whitehouse.gov/[/embed]' ),
				'contains'            => array(
					'vk-wp-oembed-blog-card-url-template',
					'https://www.whitehouse.gov/',
				),
				'not_contains'        => array( 'class="blog-card"' ),
			),
			// HTMLの文字コードが異なるサイト.
			array(
				'test_condition_name' => 'HTML の文字コードが Shift_JIS のサイトの場合 => UTF-8 に変換してブログカードを生成',
				'url'                 => 'http://abehiroshi.la.coocan.jp/',
				'correct'             => apply_filters( 'the_content', '[embed]http://abehiroshi.la.coocan.jp/[/embed]' ),
				'contains'            => array(
					'class="blog-card"',
					'>阿部寛のホームページ</a>',
					'阿部寛の公式ホームページです。',
				),
				'not_contains'        => array( 'vk-wp-oembed-blog-card-url-template' ),
			),
		);
		foreach ( $test_array as $key => $value ) {
			$output = '';
			$result = VK_WP_Oembed_Blog_Card::maybe_make_link( $output, $value['url'] );
			if ( function_exists( 'wp_img_tag_add_loading_optimization_attrs' ) ) {
				$result = wp_img_tag_add_loading_optimization_attrs( $result, 'custom' );
			}
			$this->assertEquals( $value['correct'], $result, $value['test_condition_name'] );
			// `correct` との比較は同じ経路同士の比較になり得るため、経路をリテラルで固定して検証する.
			$this->assert_blog_card_contents( $value, $result );
		}
		// wpautopフィルターフックを戻す.
		add_filter( 'the_content', 'wpautop' );
	}
}
