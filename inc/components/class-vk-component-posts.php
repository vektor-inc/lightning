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
				'layout'                     => 'card',
				'display_image'              => true,
				'display_image_overlay_term' => true,
				'display_excerpt'            => false,
				'display_date'               => true,
				'display_new'                => true,
				'display_btn'                => false,
				'image_default_url'          => false,
				'overlay'                    => false,
				'btn_text'                   => __( 'Read more', 'lightning' ),
				'btn_align'                  => 'right',
				'new_text'                   => __( 'New!!', 'lightning' ),
				'new_date'                   => 7,
				'textlink'                   => true,
				'class_outer'                => '',
				'class_title'                => '',
				'body_prepend'               => '',
				'body_append'                => '',
			);
			$return  = wp_parse_args( $options, $default );
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

		static public function get_loop( $wp_query, $options, $options_loop = array() ) {

			$options_loop_dafault = array(
				'class_loop_outer' => '',
			);
			$options_loop         = wp_parse_args( $options_loop, $options_loop_dafault );

			$loop = '';
			if ( $wp_query->have_posts() ) :

				$outer_class = '';
				if ( $options_loop['class_loop_outer'] ) {
					$outer_class = ' ' . $options_loop['class_loop_outer'];
				}

				$loop                  .= '<div class="vk_posts' . $outer_class . '">';
				$options['display_btn'] = true;

				while ( $wp_query->have_posts() ) {
					$wp_query->the_post();
					global $post;
					$loop .= VK_Component_Posts::get_view( $post, $options );
				} // while ( have_posts() ) {
				endif;

				$loop .= '</div>';

			wp_reset_query();
			wp_reset_postdata();
			return $loop;
		}
		static public function the_loop( $wp_query, $options, $options_loop = array() ) {
			echo self::get_loop( $wp_query, $options, $options_loop );
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
			if ( $options['display_btn'] ) {
				$class_outer .= ' vk_post-btn-display';
			}
			$html = '<div id="post-' . esc_attr( $post->ID ) . '" class="vk_post ' . join( ' ', get_post_class( $class_outer ) ) . '">' . "\n";
			return $html;
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
			if ( $options['display_image'] ) {
				if ( $classes['class_outer'] ) {
					$classes['class_outer'] = ' ' . $classes['class_outer'];
				}
				$html .= '<div class="vk_post_imgOuter' . $classes['class_outer'] . '">' . "\n";
				$html .= '<a href="' . get_the_permalink( $post->ID ) . '">' . "\n";

				if ( $options['overlay'] ) {
					$html .= '<div class="card-img-overlay">' . "\n";
					$html .= $options['overlay'];
					$html .= '</div>' . "\n";
				}

				if ( $options['display_image_overlay_term'] ) {

					$html     .= '<div class="card-img-overlay">' . "\n";
					$term_args = array(
						'class' => 'card_singleTermLabel',
					);
					$html     .= Vk_term_color::get_single_term_with_color( false, $term_args ) . "\n";
					$html     .= '</div>' . "\n";

				}

				if ( $classes['class_image'] ) {
					$image_class = 'vk_post_imgOuter_img ' . $classes['class_image'];
				} else {
					$image_class = 'vk_post_imgOuter_img';
				}

				$image_attr = array( 'class' => $image_class );
				$img        = get_the_post_thumbnail( $post->ID, 'medium', $image_attr );
				if ( $img ) {
					$html .= $img . "\n";
				} elseif ( $options['image_default_url'] ) {
					$html .= '<img src="' . esc_url( $options['image_default_url'] ) . '" alt="" class="' . $image_class . '" />' . "\n";
				}
				$html .= '</a>' . "\n";
				$html .= '</div><!-- [ /.vk_post_imgOuter ] -->' . "\n";
			} // if ( $options['display_image'] ) {
			return $html;
		}

		/**
		 * Common Part _ post body
		 * @var [type]
		 */
		static public function get_view_body( $post, $options ) {
			// $default = array(
			// 	'textlink' => false,
			// );
			// $attr = wp_parse_args( $attr, $default );

			$layout_type = $options['layout'];
			if ( $layout_type == 'card-horizontal' ) {
				$layout_type = 'card';
			}

			$html = '';

			$html .= '<div class="vk_post_body ' . $layout_type . '-body">' . "\n";

			if ( ! empty( $options['body_prepend'] ) ) {
				$html .= $options['body_prepend'];
			}

			$html .= '<h5 class="vk_post_title ' . $layout_type . '-title">';

			if ( $options['textlink'] ) {
				$html .= '<a href="' . get_the_permalink( $post->ID ) . '">' . "\n";
			}

			$html .= get_the_title( $post->ID );

			if ( $options['display_new'] ) {
				$today = date_i18n( 'U' );
				$entry = get_the_time( 'U' );
				$kiji  = date( 'U', ( $today - $entry ) ) / 86400;
				if ( $options['new_date'] > $kiji ) {
					$html .= '<span class="vk_post_title_new">' . $options['new_text'] . '</span>';
				}
			}

			if ( $options['textlink'] ) {
				$html .= '</a>' . "\n";
			}

			$html .= '</h5>' . "\n";

			if ( $options['display_date'] ) {
				$html .= '<div class="vk_post_date ' . $layout_type . '-date published">';
				$html .= esc_html( get_the_date( null, $post->ID ) );
				$html .= '</div>' . "\n";
			}

			if ( $options['display_excerpt'] ) {
				$html .= '<p class="vk_post_excerpt ' . $layout_type . '-text">';
				$html .= wp_kses_post( get_the_excerpt( $post->ID ) );
				$html .= '</p>' . "\n";
			}

			if ( $options['display_btn'] ) {
				$button_options = array(
					'outer_id'       => '',
					'outer_class'    => '',
					'btn_text'       => $options['btn_text'],
					'btn_url'        => get_the_permalink( $post->ID ),
					'btn_class'      => 'btn btn-primary btn-sm',
					'btn_target'     => '',
					'btn_ghost'      => false,
					'btn_color_text' => '',
					'btn_color_bg'   => '',
					'shadow_use'     => false,
					'shadow_color'   => '',
				);

				$text_align = '';
				if ( $options['btn_align'] == 'right' ) {
					$text_align = ' text-right';
				}
				$html .= '<div class="vk_post_btnOuter' . $text_align . '">';
				$html .= VK_Component_Button::get_view( $button_options );
				$html .= '</div>' . "\n";
			}

			if ( ! empty( $options['body_append'] ) ) {
				$html .= $options['body_append'];
			}

			$html .= '</div><!-- [ /.' . $layout_type . '-body ] -->' . "\n";

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
			// $html .= '<a href="' . get_the_permalink( $post->ID ) . '">';

			$attr  = array(
				'class_outer' => '',
				'class_image' => 'card-img-top',
			);
			$html .= self::get_thumbnail_image( $post, $options, $attr );

			$html .= self::get_view_body( $post, $options );

			// $html .= '</a>';
			$html .= '</div><!-- [ /.card ] -->' . "\n\n";
			return $html;
		}

		/**
		 * Card horizontal
		 * @var [type]
		 */
		static public function get_view_type_card_horizontal( $post, $options ) {
			$html  = '';
			$html .= self::get_view_first_div( $post, $options );
			// $html .= '<a href="' . get_the_permalink( $post->ID ) . '" class="card-horizontal-inner">' . "\n";
			$html .= '<div class="row no-gutters card-horizontal-inner-row">' . "\n";

			$image_src = '';
			if ( $options['display_image'] ) {

				$image_src = get_the_post_thumbnail_url( $post->ID, 'medium' );
				if ( ! $image_src && $options['image_default_url'] ) {
					$image_src = esc_url( $options['image_default_url'] );
				}

				$html .= '<div class="col-5 card-img-outer" style="background-image:url(' . $image_src . ')">' . "\n";
				$attr  = array(
					'class_outer' => '',
					'class_image' => 'card-img card-img-use-bg',
				);
				$html .= self::get_thumbnail_image( $post, $options, $attr );
				$html .= '</div><!-- /.col -->' . "\n";
				$html .= '<div class="col-7">' . "\n";
			}

			$html .= self::get_view_body( $post, $options );

			if ( $options['display_image'] ) {
				$html .= '</div><!-- /.col -->' . "\n";
			}

			$html .= '</div><!-- [ /.row ] -->' . "\n";
			// $html .= '</a>' . "\n";
			$html .= '</div><!-- [ /.card ] -->' . "\n\n";
			return $html;
		}

		/**
		 * Media
		 * @var [type]
		 */
		static public function get_view_type_media( $post, $options ) {
			$html  = '';
			$html .= self::get_view_first_div( $post, $options );
			if ( $options['display_image'] ) {
				// $html .= '<a href="' . get_the_permalink() . '" class="media-img">';
				$attr  = array(
					'class_outer' => 'media-img',
					'class_image' => '',
				);
				$html .= self::get_thumbnail_image( $post, $options, $attr );
				// $html .= '</a>' . "\n";
			}

			// $attr  = array(
			// 	'textlink' => true,
			// );
			$html .= self::get_view_body( $post, $options );

			$html .= '</div><!-- [ /.media ] -->' . "\n\n";
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
