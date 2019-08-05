<?php

if ( ! class_exists( 'VK_Post_Components' ) ) {

	class VK_Post_Components {

		static public function get_loop_post_view_options( $options ) {
			$default = array(
				'layout'  => 'card',
				'display' => array(
					'image'       => true,
					'excerpt'     => false,
					'date'        => false,
					'link_button' => true,
					'link_text'   => __( 'Read more', 'lightning' ),
					'overlay'     => false,
				),
				'class'   => array(
					'outer' => '',
				),
			);
			$options = wp_parse_args( $options, $default );
			return $options;
		}

		static public function get_view( $post, $options ) {
			$options = self::get_loop_post_view_options( $options );

			if ( $options['layout'] == 'media' ) {
				$html = self::get_view_media( $post, $options );
			} else {
				$html = self::get_view_card( $post, $options );
			}
			return $html;
		}

		static public function get_view_card( $post, $options ) {
			$html = '';
			if ( ! empty( $options['class']['outer'] ) ) {
				$class_outer = ' ' . esc_attr( $options['class']['outer'] );
			}
			$html .= '<div class="card' . $class_outer . '">';
			$html .= '<a href="' . get_the_permalink() . '">';
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
			$html .= '<h5 class="card-title">' . get_the_title() . '</h5>';

			if ( $options['display']['date'] ) {
				$html .= '<p class="card-text">';
				$html .= '<span class="published entry-meta_items">' . esc_html( get_the_date() ) . '</span>';
				$html .= '</p>';
			}

			$html .= '</div><!-- [ /.card-body ] -->';
			$html .= '</a>';
			$html .= '</div><!-- [ /.card ] -->';
			return $html;
		}

		static public function get_view_media( $post, $options ) {
			$html = '';
			if ( ! empty( $options['class']['outer'] ) ) {
				$class_outer = ' ' . esc_attr( $options['class']['outer'] );
			}
			$html .= '<div class="media' . $class_outer . '">';

			if ( $options['display']['image'] ) {
				$html      .= '<a href="' . get_the_permalink() . '" class="media-img mr-3">';
				$image_attr = array( 'class' => '' );
				$html      .= get_the_post_thumbnail( $post->ID, 'medium', $image_attr );
				$html      .= '</a>';
			}
			$html .= '<div class="media-body">';
			$html .= '<h5 class="media-title">' . get_the_title() . '</h5>';

			if ( $options['display']['date'] ) {
				$html .= '<p class="media-text">';
				$html .= '<span class="published entry-meta_items">' . esc_html( get_the_date() ) . '</span>';
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
