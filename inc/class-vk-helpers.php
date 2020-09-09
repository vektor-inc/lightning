<?php

if ( ! class_exists( 'VK_Helpers' ) ) {

	class VK_Helpers {

		/**
		 * 色を比率で明るくしたり暗くする
		 * @param  [type]  $color       #あり16進数
		 * @param  integer $change_rate 1 が 100%
		 * @return [type]               string （#あり16進数）
		 */
		public static function color_auto_modifi( $color, $change_rate = 1 ) {

			$color = preg_replace( '/#/', '', $color );
			// 16進数を10進数に変換
			$r = hexdec( substr( $color, 0, 2 ) );
			$g = hexdec( substr( $color, 2, 2 ) );
			$b = hexdec( substr( $color, 4, 2 ) );

			// 10進数の状態で変更レートを掛けて dechex で 16進数に戻す
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
		 * @param  [type] $num RGBの10進数の数値
		 * @return [type]      [description]
		 */
		public static function color_adjust_under_ff( $num ) {
			if ( $num > 256 ) {
				$num = 255;
			}
			return $num;
		}

		/**
		 * [color_mode_check description]
		 * @param  string  $input         input color code
		 * @param  boolean $return_detail If false that return 'mode' only
		 * @return string                 If $return_detail == false that return light ot dark
		 */
		public static function color_mode_check( $input = '#ffffff' ) {
			$color['input'] = $input;
			// delete #
			$color['input'] = preg_replace( '/#/', '', $color['input'] );

			$color_len = strlen( $color['input'] );

			// Only 3 character
			if ( $color_len === 3 ) {
				$color_red   = substr( $color['input'], 0, 1 ) . substr( $color['input'], 0, 1 );
				$color_green = substr( $color['input'], 1, 1 ) . substr( $color['input'], 1, 1 );
				$color_blue  = substr( $color['input'], 2, 1 ) . substr( $color['input'], 2, 1 );
			} elseif ( $color_len === 6 ) {
				$color_red   = substr( $color['input'], 0, 2 );
				$color_green = substr( $color['input'], 2, 2 );
				$color_blue  = substr( $color['input'], 4, 2 );
			} else {
				$color_red   = 'ff';
				$color_green = 'ff';
				$color_blue  = 'ff';
			}

			// change 16 to 10 number
			$color['color_red']           = hexdec( $color_red );
			$color['color_green']         = hexdec( $color_green );
			$color['color_blue']          = hexdec( $color_blue );

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
		 * @param  string $input 
		 * @param  num $alpha 
		 */
		public static function color_convert_rgba( $input = '#FFFFFF', $alpha = 1  ) {
			$color = VK_Helpers::color_mode_check($input);
			$rgba .= 'rgba(' . $color['color_red'] . ', ' . $color['color_green'] . ', ' .$color['color_blue'] . ', ' . $alpha . ')';
			return esc_html( $rgba );
		}

		/**
		 * 有効化されているプラグインを無効化する
		 */
		public static function deactivate_plugin( $plugin_path ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
			if ( is_plugin_active( $plugin_path ) ) {
				$active_plugins = get_option( 'active_plugins' );
				// delete item
				$active_plugins = array_diff( $active_plugins, array( $plugin_path ) );
				// re index
				$active_plugins = array_values( $active_plugins );
				update_option( 'active_plugins', $active_plugins );
			}
		}

	}
}
