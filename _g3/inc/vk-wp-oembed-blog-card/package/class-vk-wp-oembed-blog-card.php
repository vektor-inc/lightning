<?php
/**
 * ブログカード
 * blockquoteがついたembedのHTMLとオリジナルURLの時に実行
 * YouTubeのリンクなどには実行されない
 */

if ( ! class_exists( 'VK_WP_Oembed_Blog_Card' ) ) {

	class VK_WP_Oembed_Blog_Card {
		/**
		 * Constructor
		 */
		public function __construct() {
			// ハイフンなどの特殊文字を勝手に変換させない
			add_filter( 'run_wptexturize', '__return_false' );

			/**
			 * 内部リンク,WordPressで作られたサイトの場合
			 */
			add_filter( 'embed_oembed_html', array( __CLASS__, 'oembed_html' ), 10, 2 );

			/**
			 * 外部URLリンク の場合
			 */
			add_filter( 'embed_maybe_make_link', array( __CLASS__, 'maybe_make_link' ), 9, 2 );

			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_style' ) );
		}

		public static function add_style() {
			global $vk_embed_dir_uri;
			wp_enqueue_style( 'vk-blog-card', $vk_embed_dir_uri . 'css/blog-card.css' );
		}

		/**
		 * WordPress独自のブログカード生成時のフィルターフック
		 */
		public static function oembed_html( $cache, $url ) {

			/**
			 * YouTubeなどのWordPressが許可している埋め込み方法はそれに従う
			 * https://runebook.dev/ja/docs/wordpress/functions/wp_filter_oembed_result
			 */
			$wp_oembed = _wp_oembed_get_object();
			if ( false !== $wp_oembed->get_provider( $url, array( 'discover' => false ) ) ) {
				return $cache;
			}

			$post_id = url_to_postid( $url );
			if ( $post_id ) {
				/**
				 * $post_idが取得できる時
				 */
				$content = static::vk_get_post_data_blog_card( $post_id );
			} else {
				/**
				 * $post_idが取得できない場合
				 */
				$content = static::vk_get_blog_card( $url );
			}
			return $content;
		}

		/**
		 * WordPress独自のブログカードで生成出来ないもの
		 */
		public static function maybe_make_link( $output, $url ) {
			$content = static::vk_get_blog_card( $url );
			return $content;
		}

		/**
		 * 外部サイトの場合のブログカード生成
		 */
		public static function vk_get_blog_card( $url ) {
			/**
			 * リンク先のHTMLを取得
			 */
			$response = wp_remote_get( $url );

			/**
			 * HTTP レスポンスステータスコードで条件分岐
			 */
			$status_code = wp_remote_retrieve_response_code( $response );
			if ( 200 !== $status_code && 304 !== $status_code ) {
				$content = static::get_url_template( $url );
				return $content;
			}

			// URLのHTMLを$bodyに入れる
			$body = $response['body'];
			// 取得したHTMLを今のサイトの文字コードにencode
			$body = static::encode( $body );

			/**
			 * ブログカードに必要な情報を取得
			 */
			$blog_card_data['url']         = $url;
			$blog_card_data['title']       = static::get_title( $body );
			$blog_card_data['thumbnail']   = static::get_thumbnail( $body );
			$blog_card_data['description'] = static::get_description( $body );
			$blog_card_data['favicon']     = static::get_favicon( $body );
			$blog_card_data['site_name']   = static::get_site_name( $body );
			$blog_card_data['domain']      = static::get_domain( $url );

			/**
			 * ブログカードHTMLを生成
			 */
			$content = static::vk_blog_card_html( $blog_card_data );

			return $content;
		}

		/**
		 * 内部リンク ブログカード生成
		 */
		public static function vk_get_post_data_blog_card( $post_id ) {

			// ブログカードに必要な情報を取得
			$blog_card_data['url']         = get_permalink( $post_id );
			$blog_card_data['title']       = get_the_title( $post_id );
			$blog_card_data['thumbnail']   = get_the_post_thumbnail_url( $post_id, 'large' );
			$blog_card_data['description'] = get_the_excerpt( $post_id );
			$blog_card_data['favicon']     = get_site_icon_url( 32 );
			$blog_card_data['site_name']   = get_bloginfo( 'name' );
			$blog_card_data['domain']      = home_url();

			/**
			 * ブログカードHTMLを生成
			 */
			$content = static::vk_blog_card_html( $blog_card_data );

			return $content;
		}

		/**
		 * HTTP レスポンスステータスコードで200以外のHTML
		 */
		public static function get_url_template( $url ) {
			ob_start();
			?>
			<p class="vk-wp-oembed-blog-card-url-template">
				<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_url( $url ); ?></a>
			</p>
			<?php
			$content = ob_get_clean();
			$content = apply_filters( 'vk_wp_oembed_url_card_template', $content, $url );
			return $content;
		}

		/**
		 * タイトルを取得
		 *
		 * @return string
		 */
		public static function get_title( $body ) {
			if ( preg_match( '/title>(.+?)<\/title/is', $body, $matches ) ) {
				return $matches[1];
			}
			return '';
		}

		/**
		 * サムネイルを取得
		 *
		 * @return string
		 */
		public static function get_thumbnail( $body ) {
			if ( preg_match( '/<meta.+?property=["\']og:image["\'][^\/>]*?content=["\']([^"\']+?)["\'].*?\/?>/is', $body, $matches ) ) {
				return $matches[1];
			}
			return '';
		}

		/**
		 * 説明文を取得
		 *
		 * @return string
		 */
		public static function get_description( $body ) {
			if ( preg_match( '/<meta.+?property=["\']og:description["\'][^\/>]*?content=["\']([^"\']+?)["\'].*?\/?>/is', $body, $matches ) ) {
				return $matches[1];
			}
			return '';
		}

		/**
		 * ファビコンを取得
		 *
		 * @return string
		 */
		public static function get_favicon( $body ) {
			if ( preg_match( '/<link [^>]*?rel=["\']icon["\'][^\/>]*? href=["\']([^"\']+?)["\'][^\/>]*?\/?>/si', $body, $matches ) ) {
				return $matches[1];
			}
			if ( preg_match( '/<link [^>]*?rel=["\']shortcut icon["\'][^\/>]*? href=["\']([^"\']+?)["\'][^\/>]*?\/?>/si', $body, $matches ) ) {
				return $matches[1];
			}
			return '';
		}

		/**
		 * サイト名
		 *
		 * @return string
		 */
		public static function get_site_name( $body ) {
			if ( preg_match( '/<meta.+?property=["\']og:site_name["\'][^\/>]*?content=["\']([^"\']+?)["\'].*?\/?>/is', $body, $matches ) ) {
				return $matches[1];
			}
			return '';
		}

		/**
		 * ドメイン
		 *
		 * @return string
		 */
		public static function get_domain( $url ) {
			$domain = wp_parse_url( $url, PHP_URL_HOST );
			return $domain;
		}

		/**
		 * Encode.
		 * body部分をエンコードする
		 * 参考：https://github.com/inc2734/wp-oembed-blog-card/blob/HEAD/src/App/Model/Requester.php#L146
		 */
		public static function encode( $body ) {
			if ( ! function_exists( 'mb_convert_encoding' ) || ! $body ) {
				return $body;
			}

			foreach ( array( 'UTF-8', 'SJIS', 'EUC-JP', 'ASCII', 'JIS' ) as $encode ) {
				$encoded_content = mb_convert_encoding( $body, $encode, $encode );
				if ( strcmp( $body, $encoded_content ) === 0 ) {
					$from_encode = $encode;
					break;
				}
			}

			if ( empty( $from_encode ) ) {
				return $body;
			}

			return mb_convert_encoding( $body, get_bloginfo( 'charset' ), $from_encode );
		}

		/**
		 * ブログカードHTML生成
		 */
		public static function vk_blog_card_html( $blog_card_data ) {
			/**
			 * 必要な値を用意する
			 */
			$url         = $blog_card_data['url'];
			$title       = $blog_card_data['title'];
			$description = $blog_card_data['description'];
			$thumbnail   = $blog_card_data['thumbnail'];
			$favicon     = $blog_card_data['favicon'];
			$site_name   = $blog_card_data['site_name'];
			$domain      = $blog_card_data['domain'];
			ob_start();
			?>
			<div class="blog-card">
				<div class="blog-card-body-outer">
					<div class="blog-card-body">
						<?php if ( $title ) : ?>
							<h5 class="blog-card-title">
								<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
							</h5>
						<?php endif; ?>
						<?php if ( $description ) : ?>
							<p class="blog-card-text">
								<?php
								if ( function_exists( 'mb_strimwidth' ) ) {
									echo esc_html( mb_strimwidth( $description, 0, 160, '…', 'utf-8' ) );
								} else {
									echo esc_html( $description );
								}
								?>
							</p>
						<?php endif; ?>
						<div class="blog-card-site-title">
							<a href="<?php echo esc_url( $domain ); ?>">
								<?php if ( $favicon ) : ?>
									<img loading="lazy" src="<?php echo esc_url( $favicon ); ?>" width="16" height="16" alt="" >
								<?php endif; ?>
								<?php
								if ( $site_name ) {
									echo esc_html( $site_name );
								} else {
									echo esc_url( $domain );
								}
								?>
							</a>
						</div>
					</div>
				</div>
				<?php if ( $thumbnail ) : ?>
					<div class="blog-card-image-outer">
						<a href="<?php echo esc_url( $url ); ?>" class="blog-card-image-frame">
							<img class="blog-card-image-src" src="<?php echo esc_url( $thumbnail ); ?>" alt="">
						</a>
					</div>
				<?php endif; ?>
			</div>

			<?php
			$content = ob_get_clean();
			$content = apply_filters( 'vk_wp_oembed_blog_card_template', $content );
			return $content;
		}
	}
	new VK_WP_Oembed_Blog_Card();
}
