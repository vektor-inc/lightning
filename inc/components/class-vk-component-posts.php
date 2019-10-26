<?php

if ( ! class_exists( 'VK_Component_Posts' ) ) {

	class VK_Component_Posts {

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
				'new_text'          => __( 'New', 'lightning' ),
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
			return '<div id="post-' . esc_attr( $post->ID ) . '" class="vk_posts ' . join( ' ', get_post_class( $class_outer ) ) . '">';
		}

		/**
		 * Common Part _ post thumbnail
		 * @param  [type] $post    [description]
		 * @param  [type] $options [description]
		 * @param  string $class   [description]
		 * @return [type]          [description]
		 */
		static public function get_thumbnail_image( $post, $options, $class = '' ) {
			$html = '';
			if ( $options['image'] ) {
				$image_attr = array( 'class' => $class );
				$img        = get_the_post_thumbnail( $post->ID, 'medium', $image_attr );
				if ( $img ) {
					$html .= $img;
				} elseif ( $options['image_default_url'] ) {
					$html .= '<img src="' . esc_url( $options['image_default_url'] ) . '" alt="" class="' . $class . '" />';
				}
			}
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

			$html .= '<div class="vk_posts_body ' . $layout_type . '-body">';

			if ( ! empty( $options['body_prepend'] ) ) {
				$html .= $options['body_prepend'];
			}

			$html .= '<h5 class="vk_posts_title ' . $layout_type . '-title">';

			if ( $attr['textlink'] ) {
				$html .= '<a href="' . get_the_permalink( $post->ID ) . '">';
			}

			$html .= get_the_title( $post->ID );

			if ( $options['new'] ) {
				$today = date_i18n( 'U' );
				$entry = get_the_time( 'U' );
				$kiji  = date( 'U', ( $today - $entry ) ) / 86400;
				if ( $options['new'] > $kiji ) {
					$html .= '<span class="vk_posts_title_new">' . $options['new_text'] . '</span>';
				}
			}

			if ( $attr['textlink'] ) {
				$html .= '</a>';
			}

			$html .= '</h5>';

			if ( $options['date'] ) {
				$html .= '<div class="vk_posts_date ' . $layout_type . '-date published">';
				$html .= esc_html( get_the_date( null, $post->ID ) );
				$html .= '</div>';
			}

			if ( $options['excerpt'] ) {
				$html .= '<p class="vk_posts_excerpt ' . $layout_type . '-text">';
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

		/**
		 * Card
		 * @var [type]
		 */
		static public function get_view_type_card( $post, $options ) {
			$html  = '';
			$html .= self::get_view_first_div( $post, $options );
			$html .= '<a href="' . get_the_permalink( $post->ID ) . '">';
			if ( $options['overlay'] ) {
				$html .= '<div class="card-img-overlay">';
				$html .= $options['overlay'];
				$html .= '</div>';
			}

			$html .= self::get_thumbnail_image( $post, $options, 'card-img-top' );
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
			}

			$html .= '<div class="col-5 card-img-outer" style="background-image:url(' . $image_src . ')">';
			if ( $options['overlay'] ) {
				$html .= '<div class="card-img-overlay">';
				$html .= $options['overlay'];
				$html .= '</div>';
			}

			$html .= self::get_thumbnail_image( $post, $options, 'card-img card-img-use-bg' );

			$html .= '</div><!-- /.col -->';

			$html .= '<div class="col-7">';

			$html .= self::get_view_body( $post, $options );

			$html .= '</div><!-- /.col -->';

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
				$html .= '<a href="' . get_the_permalink() . '" class="media-img mr-3">';
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


		static public function the_view( $post, $options ) {
			 echo wp_kses_post( self::get_view( $post, $options ) );
		}
	}
}
