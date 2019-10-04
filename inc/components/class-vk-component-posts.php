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

			if ( $options['layout'] == 'card-holizontal' ) {
				$html = self::get_view_type_card_holizontal( $post, $options );
			} elseif ( $options['layout'] == 'media' ) {
				$html = self::get_view_type_media( $post, $options );
			} else {
				$html = self::get_view_type_card( $post, $options );
			}
			return $html;
		}

		static public function get_view_first_div( $post, $options ) {
			if ( $options['layout'] == 'card-holizontal' ) {
				$class_outer = 'card card-post card-holizontal';
			} elseif ( $options['layout'] == 'media' ) {
				$class_outer = 'media';
			} else {
				$class_outer = 'card card-post';
			}
			if ( ! empty( $options['class']['outer'] ) ) {
				$class_outer .= ' ' . esc_attr( $options['class']['outer'] );
			}
			return '<div id="post-' . esc_attr( $post->ID ) . '" ' . lightning_get_post_class( $class_outer ) . '>';
		}


		static public function get_view_body( $post, $options ) {
			$body_html = '';
			if ( ! empty( $options['body_prepend'] ) ) {
				$body_html .= $options['body_prepend'];
			}

			$title_class = $options['layout'];

			$body_html .= '<h5 class="card-title">' . get_the_title( $post->ID ) . '</h5>';

			if ( $options['display']['excerpt'] ) {
				$body_html .= '<p class="card-text">';
				$body_html .= wp_kses_post( get_the_excerpt( $post->ID ) );
				$body_html .= '</p>';
			}

			if ( $options['display']['date'] ) {
				$body_html .= '<p class="card-date">';
				$body_html .= esc_html( get_the_date( null, $post->ID ) );
				$body_html .= '</p>';
			}

			if ( ! empty( $options['body_append'] ) ) {
				$body_html .= $options['body_append'];
			}
			return $body_html;
		}

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
			$html .= '<div class="card-body">';

			$html .= self::get_view_body( $post, $options );

			$html .= '</div><!-- [ /.card-body ] -->';
			$html .= '</a>';
			$html .= '</div><!-- [ /.card ] -->';
			return $html;
		}

		static public function get_view_type_card_holizontal( $post, $options ) {
			$html  = '';
			$html .= self::get_view_first_div( $post, $options );
			$html .= '<a href="' . get_the_permalink( $post->ID ) . '" class="card-holizontal-inner">';
			$html .= '<div class="row no-gutters card-holizontal-inner-row">';

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
			$html .= '<div class="card-body">';

			$html .= self::get_view_body( $post, $options );

			$html .= '</div><!-- [ /.card-body ] -->';
			$html .= '</div><!-- /.col -->';

			$html .= '</div>';
			$html .= '</a>';
			$html .= '</div><!-- [ /.card ] -->';
			return $html;
		}

		static public function get_view_type_media( $post, $options ) {
			$html  = '';
			$html .= self::get_view_first_div( $post, $options );
			if ( $options['display']['image'] ) {
				$html      .= '<a href="' . get_the_permalink() . '" class="media-img mr-3">';
				$image_attr = array( 'class' => '' );
				$html      .= get_the_post_thumbnail( $post->ID, 'medium', $image_attr );
				$html      .= '</a>';
			}
			$html .= '<div class="media-body">';
			$html .= '<h5 class="media-title">' . get_the_title( $post->ID ) . '</h5>';

			if ( $options['display']['date'] ) {
				$html .= '<p class="media-text">';
				$html .= '<span class="published entry-meta_items">' . esc_html( get_the_date( null, $post->ID ) ) . '</span>';
				$html .= '</p>';
			}

			$html .= '</div><!-- [ /.media-body ] -->';
			$html .= '</div><!-- [ /.media ] -->';
			return $html;
		}


		static public function the_view( $post, $options ) {
			 echo wp_kses_post( self::get_view( $post, $options ) );
		}
	}
}
