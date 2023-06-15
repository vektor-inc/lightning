<?php
/*
The original of this file is located at:
https://github.com/vektor-inc/vektor-wp-libraries
If you want to change this file, please change the original file.
*/

if ( ! class_exists( 'VK_Components_Button' ) ) {

	class VK_Component_Button {

		public static function get_options( $options ) {
			$default = array(
				'outer_id'       => '',
				'outer_class'    => '',
				'btn_text'       => '',
				'btn_url'        => '',
				'btn_class'      => 'btn btn-primary',
				'btn_target'     => '',
				'btn_ghost'      => false,
				'btn_color_text' => '',
				'btn_color_bg'   => '',
				'shadow_use'     => false,
				'shadow_color'   => '',
			);
			$options = wp_parse_args( $options, $default );
			return $options;
		}

		public static function get_view( $options ) {

			$html = '';

			$options   = self::get_options( $options );
			$btn_class = '';
			if ( $options['btn_class'] ) {
				$btn_class = ' class="' . esc_attr( $options['btn_class'] ) . '"';
			}

			$btn_target = '';
			if ( $options['btn_target'] ) {
				$btn_target = ' target="' . esc_attr( $options['btn_target'] ) . '"';
			}

			if ( $options['btn_ghost'] || $options['btn_color_text'] || $options['btn_color_bg'] || $options['shadow_use'] || $options['shadow_color'] ) {
				$html .= self::get_style_all( $options );
			}

			$html .= '<a' . $btn_class . ' href="' . esc_url( $options['btn_url'] ) . '"' . $btn_target . '>';
			$html .= wp_kses_post( $options['btn_text'] );
			$html .= '</a>';

			return $html;

		}

		public static function get_style_all( $options ) {
			$options = self::get_options( $options );

			if ( ! $options['btn_class'] ) {
				return;
			}

			$dynamic_css = '';

			// Creat btn styles
			if ( $options['outer_id'] ) {
				$outer_single_selector .= '#' . $options['outer_id'] . ' ';
			} elseif ( $options['outer_class'] ) {
				$outer_class_args      = explode( ' ', $options['outer_class'] );
				$outer_single_selector = '.' . $outer_class_args[0] . ' ';
			}

			$link_class_args      = explode( ' ', $options['btn_class'] );
			$link_single_selector = '.' . $link_class_args[0];

			$dynamic_css .= '<style type="text/css">';
			$dynamic_css .= $outer_single_selector . $link_single_selector . '{';
			$dynamic_css .= self::get_style_text( $options );
			$dynamic_css .= self::get_style_bg( $options );
			$dynamic_css .= self::get_style_border( $options );
			$dynamic_css .= self::get_style_box_shadow( $options );
			$dynamic_css .= '}';
			$dynamic_css .= $outer_single_selector . ' ' . $link_single_selector . ':hover{';
			$dynamic_css .= self::get_style_text_hover( $options );
			$dynamic_css .= self::get_style_bg_hover( $options );
			$dynamic_css .= self::get_style_border_hover( $options );
			$dynamic_css .= '}';
			$dynamic_css .= '</style>';

			// delete before after space
			$dynamic_css = trim( $dynamic_css );
			// convert tab and br to space
			$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
			// Change multiple spaces to single space
			$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );

			return $dynamic_css;

		}

		/**
		 * Color_mode_check
		 * VK_Helpers::color_mode_check( $input ); と同じだが、
		 * Composer版以外の VK_Helpers を削除していきたいので内包
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
		 * ボタンの文字色のスタイルを出力する
		 *
		 * @param  [type] $options [description]
		 * @return [type]          [description]
		 */
		public static function get_style_text( $options ) {
			$options    = self::get_options( $options );
			$style_text = '';

			if ( ! $options['btn_ghost'] ) {
				/*
				 塗りボタンの時 -------------------------------*/
				// ボタンの初期状態の文字色が白なので指定する必要がない
				$style_text = 'color:#fff;';

				$color = self::color_mode_check( $options['btn_color_bg'] );
				if ( $color['brightness'] > 0.8 ) {
					$style_text = 'color:#000;';
				}
			} elseif ( $options['btn_ghost'] ) {
				// ゴーストボタンの時 -------------------------------*/
				// 文字色指定があればボタンカラーを適用
				if ( $options['btn_color_text'] ) {
					$style_text = 'color:' . $options['btn_color_text'] . ';';
				}
				if ( $options['shadow_use'] && $options['shadow_color'] ) {
					$style_text .= 'text-shadow:0 0 2px ' . $options['shadow_color'] . ';';
				}
			}

			return $style_text;
		}

		/**
		 * ボタンのホバー時の文字色を出力する
		 *
		 * @param  [type] $options [description]
		 * @return [type]          [description]
		 */
		public static function get_style_text_hover( $options ) {
			$options          = self::get_options( $options );
			$style_text_hover = '';

			// ゴーストだろうが塗りだろうが、ホバー時は背景塗りにするのでゴーストかどうかの条件分岐は関係ない

			$color = self::color_mode_check( $options['btn_color_bg'] );
			if ( $color['brightness'] > 0.8 ) {
				$style_text_hover = 'color:#000;';
			} else {
				$style_text_hover = 'color:#fff;';
			}

			return $style_text_hover;
		}

		/**
		 * ボタンの背景のスタイルを出力する
		 *
		 * @param  [type] $options [description]
		 * @return [type]          [description]
		 */
		public static function get_style_bg( $options ) {
			$style_bg = '';

			if ( $options['btn_ghost'] ) {
				// 初期状態だと背景色が指定されているので透過にする
				$style_bg = 'background:transparent;transition: .3s;';

			} elseif ( ! $options['btn_ghost'] ) {
				if ( ! empty( $options['btn_color_bg'] ) ) {
					// ボタンカラーが設定されている時
					$style_bg = 'background-color:' . esc_attr( $options['btn_color_bg'] ) . ';';
				}
			}
			return $style_bg;
		}

		/**
		 * ボタンのホバー時の背景のスタイルを出力する
		 *
		 * @param  [type] $options [description]
		 * @return [type]          [description]
		 */
		public static function get_style_bg_hover( $options ) {
			$options        = self::get_options( $options );
			$style_bg_hover = '';

			if ( $options['btn_ghost'] ) {

				$style_bg_hover = 'background-color:' . $options['btn_color_bg'] . ';';

			} elseif ( ! $options['btn_ghost'] ) {

				$style_bg_hover = 'filter: brightness(1.2) saturate(2);';
			}
			return $style_bg_hover;
		}

		/**
		 * ボタンの枠線のスタイルを出力する
		 *
		 * @param  [type] $options [description].
		 * @return [type]          [description]
		 */
		public static function get_style_border( $options ) {
			$options      = self::get_options( $options );
			$style_border = '';
			if ( $options['btn_ghost'] ) {
				$style_border = 'border-color:' . $options['btn_color_text'] . ';';
			} else {
				$style_border = 'border-color:' . $options['btn_color_bg'] . ';';
			}
			return $style_border;
		}

		/**
		 * ボタンのホバー時の枠線のスタイルを出力する
		 *
		 * @param  [type] $options [description]
		 * @return [type]          [description]
		 */
		public static function get_style_border_hover( $options ) {
			$options = self::get_options( $options );
			// 通常の塗りボタンもゴーストボタンも共通
			// （ hover 時は css filter で色を明るくするようになったので、ホバー時の枠線の色も通常のボタン背景色と同じ指定でよい ）
			$style_border_hover = 'border-color:' . $options['btn_color_bg'] . ';';
			return $style_border_hover;
		}

		/**
		 * ボタンのシャドウスタイルを出力する
		 *
		 * @param  [type] $options [description]
		 * @return [type]          [description]
		 */
		public static function get_style_box_shadow( $options ) {
			$options          = self::get_options( $options );
			$style_box_shadow = '';

			if ( $options['btn_ghost'] && ( $options['shadow_color'] ) && ( $options['shadow_use'] ) ) {
				$style_box_shadow = 'box-shadow:0 0 2px ' . $options['shadow_color'] . ';';
			}
			return $style_box_shadow;
		}

	}
}
