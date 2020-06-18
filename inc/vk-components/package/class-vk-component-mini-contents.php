<?php
/*
The original of this file is located at:
https://github.com/vektor-inc/vektor-wp-libraries
If you want to change this file, please change the original file.
*/

if ( ! class_exists( 'VK_Component_Mini_Contents' ) ) {

	class VK_Component_Mini_Contents {

		public static function get_options( $options ) {
			$default = array(
				'outer_id'       => '',
				'outer_class'    => '',
				'text_color'     => '#333',
				'text_align'     => false,
				'shadow_use'     => false,
				'shadow_color'   => '',
				'title_text'     => '',
				'title_tag'      => 'h3',
				'title_class'    => '',
				'caption_text'   => '',
				'caption_tag'    => 'div',
				'caption_class'  => '',
				'btn_text'       => '',
				'btn_url'        => '',
				'btn_class'      => 'btn btn-primary',
				'btn_target'     => '',
				'btn_ghost'      => true,
				'btn_color_text' => '#000',
				'btn_color_bg'   => '#c00',
			);
			$options = wp_parse_args( $options, $default );
			return $options;
		}

		public static function get_view( $options ) {
			$options = self::get_options( $options );

			$html  = '';
			$style = '';

			if ( $options['text_align'] ) {
				$style = ' style="text-align:' . esc_attr( $options['text_align'] ) . '"';
			}

			$html .= '<div class="' . esc_attr( $options['outer_class'] ) . '"' . $style . '>';

			$font_style = '';
			if ( $options['text_color'] ) {
				$font_style .= 'color:' . $options['text_color'] . ';';
			}
			if ( $options['shadow_use'] ) {

				if ( $options['shadow_color'] ) {
					$font_style .= 'text-shadow:0 0 2px ' . $options['shadow_color'];
				} else {
					$font_style .= 'text-shadow:0 0 2px #000';
				}
			}
			if ( $font_style ) {
				$font_style = ' style="' . esc_attr( $font_style ) . '"';
			}

			// If Text Title exist
			if ( $options['title_text'] ) {
				if ( $options['title_class'] ) {
					$title_class = ' class="' . esc_attr( $options['title_class'] ) . '"';
				}
				$html .= '<' . esc_html( $options['title_tag'] ) . $title_class . $font_style . '>';
				$html .= nl2br( wp_kses_post( $options['title_text'] ) );
				$html .= '</' . esc_html( $options['title_tag'] ) . '>';
			}

			// If Text caption exist
			if ( $options['caption_text'] ) {
				if ( $options['caption_class'] ) {
					$caption_class = ' class="' . esc_attr( $options['caption_class'] ) . '"';
				}
				$html .= '<' . esc_html( $options['caption_tag'] ) . $caption_class . $font_style . '>';
				$html .= nl2br( wp_kses_post( $options['caption_text'] ) );
				$html .= '</' . esc_html( $options['caption_tag'] ) . '>';

			}

			// If Button exist
			if ( $options['btn_url'] && $options['btn_text'] ) {

				$html .= VK_Component_Button::get_view( $options );

			} // If Button exist

			$html .= '</div>';

			return $html;
		}

		public static function the_view( $options ) {
			 echo self::get_view( $options );
		}
	}
}
