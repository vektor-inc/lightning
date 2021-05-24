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
			//ハイフンなどの特殊文字を勝手に変換させない
			add_filter( 'run_wptexturize', '__return_false');
			add_filter( 'embed_oembed_html', array( __CLASS__, 'vk_embed_oembed_html' ) );
			add_filter( 'embed_maybe_make_link', array( __CLASS__, 'vk_embed_maybe_make_link' ) , 9, 2 );
			add_action( 'after_setup_theme', array( __CLASS__, 'add_style' ) );
		}

		public static function add_style(){
			global $vk_embed_dir_uri;
			wp_enqueue_style( 'vk-blog-card', $vk_embed_dir_uri . 'css/blog-card.css' );
		}
		
		/**
		 * WordPress独自のブログカード生成時のフィルターフックembed_oembed_html
		 * File: wp-includes/class-wp-oembed.php
		 * esc_url_raw は & などがはいってもエスケープさせないため
		 */
		public static function vk_embed_oembed_html( $output ) {
			$pattern = '/<blockquote class="wp-embedded-content".*?><a href="(.+?)"/i';
			if ( ! preg_match( $pattern, $output, $match ) )  {
				return $output;
			}
			$url = esc_url_raw( $match[1] );
			$content = static::vk_get_blog_card( $url );
			return $content;
		}

		/**
		 * WordPress独自のブログカードで生成出来ないもの
		 * 「埋め込み URL このコンテンツを埋め込めませんでした。」 
		 * と表示されるものに実行
		 */
		public static function vk_embed_maybe_make_link( $output, $url ) {
			$content = static::vk_get_blog_card( $url );
			return $content;
		}

		/**
		 * ブログカードのHTML生成
		 * urlを渡すとHTMLが返ってくる
		 * wp_remote_getでリンク先のHTMLを取得
		 */
		public static function vk_get_blog_card( $url ) {
			$response = wp_remote_get( $url );
			//HTTP レスポンスステータスコードで条件分岐
			$status_code = wp_remote_retrieve_response_code( $response );
			if ( 200 !== $status_code  && 304 !== $status_code ) {
				$content = static::get_url_template( $url );
				return $content;
			}

			// URLのHTMLを$bodyに入れる
			$body = $response['body'];
			// 取得したHTMLを今のサイトの文字コードにencode
			$body = static::encode( $body );

			//ブログカードに必要な情報を取得
			$title = static::get_title( $body );
			$og_image = static::get_thumbnail( $body );
			$og_description = static::get_description( $body );

			/*
			スタイルは一旦Horizontalカードを使用
			https://getbootstrap.jp/docs/4.2/components/card/#horizontal
			HTMLがカスタマイズ出来る形にする
			*/
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
						<?php if ( $og_description ) : ?>
							<p class="blog-card-text">
								<?php
								if ( function_exists( 'mb_strimwidth' ) ) {
									echo esc_html( mb_strimwidth( $og_description, 0, 160, '…', 'utf-8' ) );
								} else {
									echo esc_html( $og_description ); 
								}
								?>
							</p>
						<?php endif; ?>
					</div>
				</div>
				<?php if ( $og_image ) : ?>
					<div class="blog-card-image-outer">
						<a href="<?php echo esc_url( $url ); ?>" class="blog-card-image-frame">
						<img class="blog-card-image-src" src="<?php echo esc_url( $og_image ); ?>" alt="">
						</a>
					</div>
				<?php endif; ?>
			</div>
			<?
			$content = ob_get_clean();
			$content = apply_filters( 'vk_wp_oembed_blog_card_template', $content );
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
			if ( preg_match( '/<title>(.+?)<\/title>/is', $body, $matches ) ) {
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
	}
	new VK_WP_Oembed_Blog_Card();
}
