<?php

if ( ! class_exists( 'VK_Component_Posts' ) ) {

	class VK_Component_Posts {

		/*-------------------------------------------*/
		/* Basic method
		/*-------------------------------------------*/
		/* Common Parts
		/*-------------------------------------------*/
		/* Layout patterns
		/*-------------------------------------------*/
		/* UI Helper method
		/*-------------------------------------------*/

		/*-------------------------------------------*/
		/* Basic method
		/*-------------------------------------------*/
		static public function get_loop_post_view_options( $options ) {
			$default = array(
				'layout'            => 'card',
				// 'slug'         => '',
				'image'             => true,
				'image_default_url' => false,
				'excerpt'           => false,
				'date'              => false,
				'btn'               => false,
				'btn_text'          => __( 'Read more', 'lightning' ),
				'overlay'           => false,
				'new'               => false,
				'new_text'          => __( 'New!!', 'lightning' ),
				'new_date'          => 7,
				'class_outer'       => '',
				'class_title'       => '',
				'body_prepend'      => '',
				'body_append'       => '',
			);
			$return = wp_parse_args( $options, $default );
			return $return;
		}

		static public function get_view( $post, $options ) {

			$options = self::get_loop_post_view_options( $options );

			if ( $options['layout'] == 'card-horizontal' ) {
				$html = self::get_view_type_card_horizontal( $post, $options );
			} elseif ( $options['layout'] == 'media' ) {
				$html = self::get_view_type_media( $post, $options );
			} else {
				$html = self::get_view_type_card( $post, $options );
			}
			return $html;
		}

		static public function the_view( $post, $options ) {
			 echo wp_kses_post( self::get_view( $post, $options ) );
		}

		/*-------------------------------------------*/
		/* Common Parts
		/*-------------------------------------------*/
		/**
		 * Common Part _ first DIV
		 * @var [type]
		 */
		static public function get_view_first_div( $post, $options ) {
			if ( $options['layout'] == 'card-horizontal' ) {
				$class_outer = 'card card-post card-horizontal';
			} elseif ( $options['layout'] == 'media' ) {
				$class_outer = 'media';
			} else {
				$class_outer = 'card card-post';
			}
			if ( ! empty( $options['class_outer'] ) ) {
				$class_outer .= ' ' . esc_attr( $options['class_outer'] );
			}
			return '<div id="post-' . esc_attr( $post->ID ) . '" class="vk_post ' . join( ' ', get_post_class( $class_outer ) ) . '">';
		}

		/**
		 * Common Part _ post thumbnail
		 * @param  [type] $post    [description]
		 * @param  [type] $options [description]
		 * @param  string $class   [description]
		 * @return [type]          [description]
		 */
		static public function get_thumbnail_image( $post, $options, $attr = array() ) {

			$default = array(
				'class_outer' => '',
				'class_image' => '',
			);
			$classes = wp_parse_args( $attr, $default );

			$html = '';
			if ( $options['image'] ) {
				if ( $classes['class_outer'] ) {
					$classes['class_outer'] = ' ' . $classes['class_outer'];
				}
				$html .= '<div class="vk_post_imgOuter' . $classes['class_outer'] . '">';

				if ( $options['overlay'] ) {

					$html .= '<div class="card-img-overlay">';
					$html .= $options['overlay'];
					$html .= '</div>';

				}

				if ( $classes['class_image'] ) {
					$image_class = 'vk_post_imgOuter_img ' . $classes['class_image'];
				} else {
					$image_class = 'vk_post_imgOuter_img';
				}

				$image_attr = array( 'class' => $image_class );
				$img        = get_the_post_thumbnail( $post->ID, 'medium', $image_attr );
				if ( $img ) {
					$html .= $img;
				} elseif ( $options['image_default_url'] ) {
					$html .= '<img src="' . esc_url( $options['image_default_url'] ) . '" alt="" class="' . $image_class . '" />';
				}

				$html .= '</div><!-- [ /.vk_post_imgOuter ] -->';
			} // if ( $options['image'] ) {
			return $html;
		}

		/**
		 * Common Part _ post body
		 * @var [type]
		 */
		static public function get_view_body( $post, $options, $attr = array() ) {
			$default = array(
				'textlink' => false,
			);
			$attr    = wp_parse_args( $attr, $default );

			$layout_type = $options['layout'];
			if ( $layout_type == 'card-horizontal' ) {
				$layout_type = 'card';
			}

			$html = '';

			$html .= '<div class="vk_post_body ' . $layout_type . '-body">';

			if ( ! empty( $options['body_prepend'] ) ) {
				$html .= $options['body_prepend'];
			}

			$html .= '<h5 class="vk_post_title ' . $layout_type . '-title">';

			if ( $attr['textlink'] ) {
				$html .= '<a href="' . get_the_permalink( $post->ID ) . '">';
			}

			$html .= get_the_title( $post->ID );

			if ( $options['new'] ) {
				$today = date_i18n( 'U' );
				$entry = get_the_time( 'U' );
				$kiji  = date( 'U', ( $today - $entry ) ) / 86400;
				if ( $options['new_date'] > $kiji ) {
					$html .= '<span class="vk_post_title_new">' . $options['new_text'] . '</span>';
				}
			}

			if ( $attr['textlink'] ) {
				$html .= '</a>';
			}

			$html .= '</h5>';

			if ( $options['date'] ) {
				$html .= '<div class="vk_post_date ' . $layout_type . '-date published">';
				$html .= esc_html( get_the_date( null, $post->ID ) );
				$html .= '</div>';
			}

			if ( $options['excerpt'] ) {
				$html .= '<p class="vk_post_excerpt ' . $layout_type . '-text">';
				$html .= wp_kses_post( get_the_excerpt( $post->ID ) );
				$html .= '</p>';
			}

			if ( $options['btn'] ) {
				$button_options = array(
					'outer_id'       => '',
					'outer_class'    => '',
					'btn_text'       => $options['btn_text'],
					'btn_url'        => get_the_permalink( $post->ID ),
					'btn_class'      => 'btn btn-primary',
					'btn_target'     => '',
					'btn_ghost'      => false,
					'btn_color_text' => '',
					'btn_color_bg'   => '',
					'shadow_use'     => false,
					'shadow_color'   => '',
				);
				$html          .= VK_Component_Button::get_view( $button_options );
			}

			if ( ! empty( $options['body_append'] ) ) {
				$html .= $options['body_append'];
			}

			$html .= '</div><!-- [ /.' . $layout_type . '-body ] -->';

			return $html;
		}

		/*-------------------------------------------*/
		/* Layout patterns
		/*-------------------------------------------*/
		/**
		 * Card
		 * @var [type]
		 */
		static public function get_view_type_card( $post, $options ) {
			$html  = '';
			$html .= self::get_view_first_div( $post, $options );
			$html .= '<a href="' . get_the_permalink( $post->ID ) . '">';

			$attr  = array(
				'class_outer' => '',
				'class_image' => 'card-img-top',
			);
			$html .= self::get_thumbnail_image( $post, $options, $attr );

			$html .= self::get_view_body( $post, $options );

			$html .= '</a>';
			$html .= '</div><!-- [ /.card ] -->';
			return $html;
		}

		/**
		 * Card horizontal
		 * @var [type]
		 */
		static public function get_view_type_card_horizontal( $post, $options ) {
			$html  = '';
			$html .= self::get_view_first_div( $post, $options );
			$html .= '<a href="' . get_the_permalink( $post->ID ) . '" class="card-horizontal-inner">';
			$html .= '<div class="row no-gutters card-horizontal-inner-row">';

			$image_src = '';
			if ( $options['image'] ) {

				$image_src = get_the_post_thumbnail_url( $post->ID, 'medium' );
				if ( ! $image_src && $options['image_default_url'] ) {
					$image_src = esc_url( $options['image_default_url'] );
				}

				$html .= '<div class="col-5 card-img-outer" style="background-image:url(' . $image_src . ')">';
				$html .= self::get_thumbnail_image( $post, $options, 'card-img card-img-use-bg' );
				$html .= '</div><!-- /.col -->';
				$html .= '<div class="col-7">';
			}

			$html .= self::get_view_body( $post, $options );

			if ( $options['image'] ) {
				$html .= '</div><!-- /.col -->';
			}

			$html .= '</div>';
			$html .= '</a>';
			$html .= '</div><!-- [ /.card ] -->';
			return $html;
		}

		/**
		 * Media
		 * @var [type]
		 */
		static public function get_view_type_media( $post, $options ) {
			$html  = '';
			$html .= self::get_view_first_div( $post, $options );
			if ( $options['image'] ) {
				$html .= '<a href="' . get_the_permalink() . '" class="media-img">';
				$html .= self::get_thumbnail_image( $post, $options, '' );
				$html .= '</a>';
			}

			$attr  = array(
				'textlink' => true,
			);
			$html .= self::get_view_body( $post, $options, $attr );

			$html .= '</div><!-- [ /.media ] -->';
			return $html;
		}

		/*-------------------------------------------*/
		/* UI Helper method
		/*-------------------------------------------*/
		/**
		 * Convert col-count from inputed column count.
		 * @param  integer $input_col [description]
		 * @return [type]             [description]
		 */
		public static function get_col_converted_size( $input_col = 4 ) {
			if ( $input_col == 1 ) {
				$col = 12;
			} elseif ( $input_col == 2 ) {
				$col = 6;
			} elseif ( $input_col == 3 ) {
				$col = 4;
			} elseif ( $input_col == 4 ) {
				$col = 3;
			}
			return strval( $col );
		}

		/**
		 * Get all size col classes
		 * @param  [type] $attributes inputed col numbers array
		 * @return [type]             [description]
		 */
		public static function get_col_size_classes( $attributes ) {
			$col_class_array = array();
			$sizes           = array( 'xs', 'sm', 'md', 'lg', 'xl' );
			foreach ( $sizes as $key => $size ) {
				if ( ! empty( $attributes[ 'col_' . $size ] ) ) {
					$col_class_array[] = 'vk_post-col-' . $size . '-' . self::get_col_converted_size( $attributes[ 'col_' . $size ] );
				}
			}
			$col_class = implode( ' ', $col_class_array );
			return $col_class;
		}

	}
}
