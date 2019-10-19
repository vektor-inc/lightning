<?php

if ( ! class_exists( 'VK_Component_Posts' ) ) {

	class VK_Component_Posts {

		static public function get_loop_post_view_options( $options ) {
			$default = array(
				'layout'       => 'card',
				'slug'         => '',
				'display'      => array(
					'image'       => true,
					'excerpt'     => false,
					'date'        => false,
					'link_button' => true,
					'link_text'   => __( 'Read more', 'lightning' ),
					'overlay'     => false,
				),
				'class'        => array(
					'outer' => '',
					'title' => '',
				),
				'body_prepend' => '',
				'body_append'  => '',
			);
			$options = wp_parse_args( $options, $default );
			return $options;
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
			if ( ! empty( $options['class']['outer'] ) ) {
				$class_outer .= ' ' . esc_attr( $options['class']['outer'] );
			}
			return '<div id="post-' . esc_attr( $post->ID ) . '" class="vk_posts ' . join( ' ', get_post_class( $class_outer ) ) . '">';
		}

		/**
		 * Common Part _ post body
		 * @var [type]
		 */
		static public function get_view_body( $post, $options ) {
			$layout_type = $options['layout'];
			if ( $layout_type == 'card-horizontal' ) {
				$layout_type = 'card';
			}

			$html = '';

			$html .= '<div class="vk_posts_body ' . $layout_type . '-body">';

			if ( ! empty( $options['body_prepend'] ) ) {
				$html .= $options['body_prepend'];
			}

			$html .= '<h5 class="vk_posts_title ' . $layout_type . '-title">' . get_the_title( $post->ID ) . '</h5>';

			if ( $options['display']['excerpt'] ) {
				$html .= '<p class="vk_posts_excerpt ' . $layout_type . '-text">';
				$html .= wp_kses_post( get_the_excerpt( $post->ID ) );
				$html .= '</p>';
			}

			if ( $options['display']['date'] ) {
				$html .= '<div class="vk_posts_date ' . $layout_type . '-date published">';
				$html .= esc_html( get_the_date( null, $post->ID ) );
				$html .= '</div>';
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
			if ( $options['display']['overlay'] ) {
				$html .= '<div class="card-img-overlay">';
				$html .= $options['display']['overlay'];
				$html .= '</div>';
			}
			if ( $options['display']['image'] ) {
				$image_attr = array( 'class' => 'card-img-top' );
				$html      .= get_the_post_thumbnail( $post->ID, 'medium', $image_attr );
			}

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

			if ( $options['display']['image'] ) {
				$image_src = get_the_post_thumbnail_url( $post->ID, 'medium' );
			}

			$html .= '<div class="col-5 card-img-outer" style="background-image:url(' . $image_src . ')">';
			if ( $options['display']['overlay'] ) {
				$html .= '<div class="card-img-overlay">';
				$html .= $options['display']['overlay'];
				$html .= '</div>';
			}
			if ( $options['display']['image'] ) {
				$image_attr = array( 'class' => 'card-img card-img-use-bg' );
				$html      .= get_the_post_thumbnail( $post->ID, 'medium', $image_attr );
			}
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
			if ( $options['display']['image'] ) {
				$html      .= '<a href="' . get_the_permalink() . '" class="media-img mr-3">';
				$image_attr = array( 'class' => '' );
				$html      .= get_the_post_thumbnail( $post->ID, 'medium', $image_attr );
				$html      .= '</a>';
			}

			$html .= self::get_view_body( $post, $options );

			$html .= '</div><!-- [ /.media ] -->';
			return $html;
		}


		static public function the_view( $post, $options ) {
			 echo wp_kses_post( self::get_view( $post, $options ) );
		}
	}
}
