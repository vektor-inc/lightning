<?php
/**
 * VK Helpers
 *
 * @package VK Helpers
 */

/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。
修正の際は上記リポジトリのデータを修正してください。
編集権限を持っていない方で何か修正要望などありましたら
各プラグインのリポジトリにプルリクエストで結構です。
*/

if ( ! class_exists( 'VK_Helpers' ) ) {
	/**
	 * VK Helpers
	 */
	class VK_Helpers {

		/*
		get_post_top_info
		get_post_type_info
		sanitize_checkbox
		sanitize_number_percentage
		sanitize_choice
		sanitize_textarea
		sanitize_boolean
		color_auto_modifi
		color_adjust_under_ff
		color_mode_check
		color_convert_rgba
		deactivate_plugin
		*/

		public function __construct() {
			add_action( 'customize_register', array( __CLASS__, 'add_customize_class' ), 0 );
		}

		public static function add_customize_class( $wp_customize ) {
			if ( ! class_exists( 'VK_Custom_Html_Control' ) ) {
				require_once dirname( __FILE__ ) . '/class-vk-custom-html-control.php';
			}
			if ( ! class_exists( 'VK_Custom_Text_Control' ) ) {
				require_once dirname( __FILE__ ) . '/class-vk-custom-text-control.php';
			}
		}

		public static function get_post_top_info() {

			$post_top_info = array();

			// Get post top page by setting display page.
			$post_top_info['id'] = get_option( 'page_for_posts' );

			// Set use post top page flag.
			$post_top_info['use'] = ( $post_top_info['id'] ) ? true : false;

			// When use post top page that get post top page name.
			$post_top_info['name'] = ( $post_top_info['use'] ) ? get_the_title( $post_top_info['id'] ) : '';

			$post_top_info['url'] = ( $post_top_info['use'] ) ? get_permalink( $post_top_info['id'] ) : '';

			return $post_top_info;
		}


		public static function get_post_type_info() {
			// Check use post top page
			$post_top_info = self::get_post_top_info();

			$woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id' );

			// Get post type slug
			/*
			-------------------------------------------*/
			// When WooCommerce taxonomy archive page , get_post_type() is does not work properly
			// $post_type_info['slug'] = get_post_type();

			global $wp_query;
			if ( is_page() ) {
				$post_type_info['slug'] = 'page';
			} elseif ( ! empty( $wp_query->query_vars['post_type'] ) ) {

				$post_type_info['slug'] = $wp_query->query_vars['post_type'];
				// Maybe $wp_query->query_vars['post_type'] is usually an array...
				if ( is_array( $post_type_info['slug'] ) ) {
					$post_type_info['slug'] = current( $post_type_info['slug'] );
				}
			} elseif ( is_tax() ) {
				// Case of tax archive and no posts
				$taxonomy               = get_queried_object()->taxonomy;
				$post_type_info['slug'] = get_taxonomy( $taxonomy )->object_type[0];
			} else {
				// This is necessary that when no posts.
				$post_type_info['slug'] = 'post';
			}

			// Get custom post type name
			/*-------------------------------------------*/
			$post_type_object = get_post_type_object( $post_type_info['slug'] );
			if ( $post_type_object ) {
				$allowed_html = array(
					'span' => array( 'class' => array() ),
					'b'    => array(),
				);
				if ( $post_top_info['use'] && $post_type_info['slug'] == 'post' ) {
					$post_type_info['name'] = wp_kses( get_the_title( $post_top_info['id'] ), $allowed_html );
				} elseif ( $woocommerce_shop_page_id && $post_type_info['slug'] == 'product' ) {
					$post_type_info['name'] = wp_kses( get_the_title( $woocommerce_shop_page_id ), $allowed_html );
				} else {
					$post_type_info['name'] = esc_html( $post_type_object->labels->name );
				}
			}

			// Get custom post type archive url
			/*-------------------------------------------*/
			if ( $post_top_info['use'] && $post_type_info['slug'] == 'post' ) {
				$post_type_info['url'] = esc_url( get_the_permalink( $post_top_info['id'] ) );
			} elseif ( $woocommerce_shop_page_id && $post_type_info['slug'] == 'product' ) {
				$post_type_info['url'] = esc_url( get_the_permalink( $woocommerce_shop_page_id ) );
			} else {
				$post_type_info['url'] = esc_url( get_post_type_archive_link( $post_type_info['slug'] ) );
			}

			$post_type_info = apply_filters( 'vk_get_post_type_info', $post_type_info );
			return $post_type_info;
		}

		public static function get_display_taxonomies( $post_id = null, $args = null ) {
			if ( ! $post_id ) {
				global $post;
				$post_id = $post->ID;
			}
			$taxonomies = get_the_taxonomies( $post_id, $args );

			// 非公開のタクソノミーを自動的に除外
			foreach ( $taxonomies as $taxonomy => $value ) {
				$taxonomy_info = get_taxonomy( $taxonomy );
				if ( empty( $taxonomy_info->public ) ) {
					unset( $taxonomies[ $taxonomy ] );
				}
			}

			// 上記を後で実装したので以下の処理は事実上不要と思われるが、
			// 公開タクソノミーで意図的に表示したくないものもあるかもしれないのでフィルターは消さない
			$exclusion = array( 'post_tag', 'product_type' );
			$exclusion = apply_filters( 'vk_get_display_taxonomies_exclusion', $exclusion );
			if ( is_array( $exclusion ) ) {
				foreach ( $exclusion as $key => $value ) {
					unset( $taxonomies[ $value ] );
				}
			}
			return $taxonomies;
		}

		/**
		 * Sanitize Check Box
		 *
		 * @param string $input $input.
		 */
		public static function sanitize_checkbox( $input ) {
			if ( 'true' === $input || true === $input ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Sanitize Number
		 *
		 * @param string $input $input.
		 */
		public static function sanitize_number( $input ) {
			$input = mb_convert_kana( $input, 'a' );
			if ( is_numeric( $input ) ) {
				return $input;
			} else {
				return 0;
			}
		}

		/**
		 * Sanitize Number Percentage
		 *
		 * @param string $input $input.
		 */
		public static function sanitize_number_percentage( $input ) {
			$input = self::sanitize_number( $input );
			if ( 0 <= $input && $input <= 100 ) {
				return $input;
			} else {
				return 0;
			}
		}

		/**
		 * Sanitize Choice
		 *
		 * @param string $input $input.
		 */
		public static function sanitize_choice( $input ) {
			return esc_attr( $input );
		}


		/**
		 * Sanitize Text Area
		 *
		 * @param string $input $input.
		 */
		public static function sanitize_textarea( $input ) {
			$allowed_html = array(
				'a'      => array(
					'id'    => array(),
					'href'  => array(),
					'title' => array(),
					'class' => array(),
					'role'  => array(),
				),
				'br'     => array(),
				'em'     => array(),
				'strong' => array(),
				'i'      => array(
					'class' => array(),
				),
			);
			return wp_kses( $input, $allowed_html );
		}

		/**
		 * Sanitize Boolean
		 *
		 * @param string $input $input.
		 */
		public static function sanitize_boolean( $input ) {
			if ( $input == true ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * 色を比率で明るくしたり暗くする
		 *
		 * @param  string  $color       #あり16進数.
		 * @param  integer $change_rate 1 が 100%.
		 */
		public static function color_auto_modifi( $color, $change_rate = 1 ) {

			if ( ! $color ) {
				return;
			}

			$color = preg_replace( '/#/', '', $color );
			// 16進数を10進数に変換
			$r = hexdec( substr( $color, 0, 2 ) );
			$g = hexdec( substr( $color, 2, 2 ) );
			$b = hexdec( substr( $color, 4, 2 ) );

			// 10進数の状態で変更レートを掛けて dechex で 16進数に戻す.
			$color_array      = array();
			$color_array['r'] = dechex( self::color_adjust_under_ff( $r * $change_rate ) );
			$color_array['g'] = dechex( self::color_adjust_under_ff( $g * $change_rate ) );
			$color_array['b'] = dechex( self::color_adjust_under_ff( $b * $change_rate ) );

			$new_color = '#';

			foreach ( $color_array as $key => $value ) {
				/*
				桁数が１桁の場合2桁にする（ 16進数を sprintf( "%02x",$value ） しても 00 にされるため文字数が1文字のものに対して0を追加している
				 */
				if ( mb_strlen( $value ) < 2 ) {
					$color_array[ $key ] = '0' . $value;
				}
				$new_color .= $color_array[ $key ];
			}
			return $new_color;
		}

		/**
		 * 色の自動変更で255を越えてしまった時に255に強制的に抑える
		 *
		 * @param  [type] $num RGBの10進数の数値.
		 */
		public static function color_adjust_under_ff( $num ) {
			if ( $num > 256 ) {
				$num = 255;
			}
			return $num;
		}

		/**
		 * [color_mode_check description]
		 *
		 * @param string $input input color code.
		 */
		public static function color_mode_check( $input = '#ffffff' ) {
			$color['input'] = $input;
			// delete #.
			$color['input'] = preg_replace( '/#/', '', $color['input'] );

			$color_len = strlen( $color['input'] );

			// Only 3 character.
			if ( 3 === $color_len ) {
				$color_red   = substr( $color['input'], 0, 1 ) . substr( $color['input'], 0, 1 );
				$color_green = substr( $color['input'], 1, 1 ) . substr( $color['input'], 1, 1 );
				$color_blue  = substr( $color['input'], 2, 1 ) . substr( $color['input'], 2, 1 );
			} elseif ( 6 === $color_len ) {
				$color_red   = substr( $color['input'], 0, 2 );
				$color_green = substr( $color['input'], 2, 2 );
				$color_blue  = substr( $color['input'], 4, 2 );
			} else {
				$color_red   = 'ff';
				$color_green = 'ff';
				$color_blue  = 'ff';
			}

			// change 16 to 10 number.
			$color['color_red']   = hexdec( $color_red );
			$color['color_green'] = hexdec( $color_green );
			$color['color_blue']  = hexdec( $color_blue );

			$color['number_sum'] = $color['color_red'] + $color['color_green'] + $color['color_blue'];

			$color['brightness'] = 0.00130718954 * $color['number_sum'];

			if ( $color['brightness'] < 0.5 ) {
				$color['mode'] = 'dark';
			} else {
				$color['mode'] = 'bright';
			}

			return $color;

		}

		/**
		 * 16進数をRGBAに変換する
		 *
		 * @param  string $input hex color code.
		 * @param  num    $alpha transparnt value.
		 */
		public static function color_convert_rgba( $input = '#FFFFFF', $alpha = 1 ) {
			$color = self::color_mode_check( $input );
			$rgba .= 'rgba(' . $color['color_red'] . ', ' . $color['color_green'] . ', ' . $color['color_blue'] . ', ' . $alpha . ')';
			return esc_html( $rgba );
		}

		/**
		 * 有効化されているプラグインを無効化する
		 *
		 * @param string $plugin_path path of plugin.
		 */
		public static function deactivate_plugin( $plugin_path ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			if ( is_plugin_active( $plugin_path ) ) {
				$active_plugins = get_option( 'active_plugins' );
				// delete item.
				$active_plugins = array_diff( $active_plugins, array( $plugin_path ) );
				// re index.
				$active_plugins = array_values( $active_plugins );
				update_option( 'active_plugins', $active_plugins );
			}
		}

	}
	new VK_Helpers();
}
