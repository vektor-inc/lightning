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
			$content = static::get_blog_card( $url );
			return $content;
		}

		/**
		 * WordPress独自のブログカードで生成出来ないもの
		 * 「埋め込み URL このコンテンツを埋め込めませんでした。」 
		 * と表示されるものに実行
		 */
		public static function vk_embed_maybe_make_link( $output, $url ) {
			$content = static::get_blog_card( $url );
			return $content;
		}

		/**
		 * ブログカードのHTML生成
		 * urlを渡すとHTMLが返ってくる
		 * wp_remote_getでリンク先のHTMLを取得
		 */
		public static function get_blog_card( $url ) {
			$response = wp_remote_get( $url );
			// URLのHTMLを$bodyに入れる
			$body = $response['body'];

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
			<div class="card mb-3">
				<a href="<?php echo esc_url( $url ); ?>">
					<div class="row no-gutters">
						<div class="col-md-4">
							<?php if ( $og_image ) : ?>
								<img class="bd-placeholder-img" src="<?php echo esc_url( $og_image ); ?>" alt="">
							<?php endif; ?>
						</div>
						<div class="col-md-8">
							<div class="card-body">
								<?php if ( $title ) : ?>
									<h5 class="card-title"><?php echo esc_html( $title ); ?></h5>
								<?php endif; ?>
								<?php if ( $og_description ) : ?>
									<p class="card-text">
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
					</div>
				</a>
			</div>
			<?
			$content = ob_get_clean();
			$content = apply_filters( 'lightning_wp_oembed_blog_card_template', $content );
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
	}
	new VK_WP_Oembed_Blog_Card();
}
