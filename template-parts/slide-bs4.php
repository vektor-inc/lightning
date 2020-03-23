<?php
if ( apply_filters( 'lightning_default_slide_display', true ) ) {

	$lightning_theme_options = lightning_get_theme_options();

	// count top slide
	$top_slide_count_max = lightning_top_slide_count_max();
	$top_slide_count     = lightning_top_slide_count( $lightning_theme_options );
	$top_slide_count     = apply_filters( 'lightning_top_slide_count', $top_slide_count );

	if ( $top_slide_count ) : ?>
		<?php
		if ( empty( $lightning_theme_options['top_slide_time'] ) ) {
			$interval = 4000;
		} else {
			$interval = esc_attr( $lightning_theme_options['top_slide_time'] );
		}
		$effect = '';
		if ( isset( $lightning_theme_options['top_slide_effect'] ) && $lightning_theme_options['top_slide_effect'] == 'fade' ) {
			$effect = ' carousel-fade';
		}
		?>
	<div id="top__fullcarousel" data-interval="<?php echo $interval; ?>" class="carousel slide slide-main<?php echo $effect; ?>" data-ride="carousel">


		<?php if ( $top_slide_count >= 2 ) : ?>
		<!-- Indicators -->
		<ol class="carousel-indicators">
			<?php for ( $i = 0; $i <= $top_slide_count - 1; ) { ?>
			<li data-target="#top__fullcarousel" data-slide-to="<?php echo $i; ?>"></li>
				<?php
				$i++;
			}
			?>
		</ol>
	<?php endif; ?>
	<div class="carousel-inner">
		<?php
		// Why end point is $top_slide_count_max that not $top_slide_count, image exist 1,2,5
		for ( $i = 1; $i <= $top_slide_count_max; ) {

			$top_slide_url = '';

			// If Alt exist
			$top_slide_alt = '';
			if ( ! empty( $lightning_theme_options[ 'top_slide_alt_' . $i ] ) ) {
				$top_slide_alt = $lightning_theme_options[ 'top_slide_alt_' . $i ];
			} elseif ( ! empty( $lightning_theme_options[ 'top_slide_title_' . $i ] ) ) {
				$top_slide_alt = $lightning_theme_options[ 'top_slide_title_' . $i ];
			} else {
				$top_slide_alt = '';
			}

			// Slide Display
			if ( ! empty( $lightning_theme_options[ 'top_slide_image_' . $i ] ) ) {
				$link_target = ( isset( $lightning_theme_options[ 'top_slide_link_blank_' . $i ] ) && $lightning_theme_options[ 'top_slide_link_blank_' . $i ] ) ? ' target="_blank"' : '';
				?>
				<div class="carousel-item item item-<?php echo $i; ?>
												<?php
												if ( $i == 1 ) {
													echo ' active';}
												?>
	">

				<?php if ( lightning_is_slide_outer_link( $lightning_theme_options, $i ) ) : ?>
						<a href="<?php echo esc_url( $lightning_theme_options[ 'top_slide_url_' . $i ] ); ?>"<?php echo $link_target; ?>>
				<?php endif; ?>

				<picture>
					<?php
					// If Mobile Image exist
					if ( ! empty( $lightning_theme_options[ 'top_slide_image_mobile_' . $i ] ) ) :
						?>
						  <source media="(max-width: 767px)" srcset="<?php echo esc_attr( $lightning_theme_options[ 'top_slide_image_mobile_' . $i ] ); ?>">
					<?php endif; ?>
					  <img src="<?php echo esc_attr( $lightning_theme_options[ 'top_slide_image_' . $i ] ); ?>" alt="<?php echo esc_attr( $top_slide_alt ); ?>" class="slide-item-img d-block w-100">
					</picture>

					<?php
					/*
					  slide-cover
					/*-------------------------------------------*/
					$cover_style = lightning_slide_cover_style( $lightning_theme_options, $i );
					if ( $cover_style ) {
						$cover_style = ( $cover_style ) ? ' style="' . esc_attr( $cover_style ) . '"' : '';
						echo '<div class="slide-cover"' . $cover_style . '></div>';
					}
					?>

					<?php if ( lightning_is_slide_outer_link( $lightning_theme_options, $i ) ) : ?>
						</a>
					<?php endif; ?>

					<div class="slide-text-set mini-content">

					<?php
					/*
					  mini_content
					/*-------------------------------------------*/
					$mini_content_args = array(
						'outer_class'    => 'mini-content-container-' . $i . ' container',
						'title_tag'      => 'h3',
						'title_class'    => 'slide-text-title',
						'caption_tag'    => 'div',
						'caption_class'  => 'slide-text-caption',
						'btn_class'      => 'btn btn-ghost',
						'btn_ghost'      => true,
						'btn_color_text' => '#333',
						'btn_color_bg'   => '#337ab7',
					);

					if ( ! empty( $lightning_theme_options[ 'top_slide_text_color_' . $i ] ) ) {
						$mini_content_args['text_color'] = $lightning_theme_options[ 'top_slide_text_color_' . $i ];
					}
					if ( ! empty( $lightning_theme_options[ 'top_slide_text_align_' . $i ] ) ) {
						$mini_content_args['text_align'] = $lightning_theme_options[ 'top_slide_text_align_' . $i ];
					}
					if ( ! empty( $lightning_theme_options[ 'top_slide_text_shadow_use_' . $i ] ) ) {
						$mini_content_args['shadow_use'] = $lightning_theme_options[ 'top_slide_text_shadow_use_' . $i ];
					}

					if ( ! empty( $lightning_theme_options[ 'top_slide_text_shadow_color_' . $i ] ) ) {
						$mini_content_args['shadow_color'] = $lightning_theme_options[ 'top_slide_text_shadow_color_' . $i ];
					}

					if ( ! empty( $lightning_theme_options[ 'top_slide_text_title_' . $i ] ) ) {
						$mini_content_args['title_text'] = $lightning_theme_options[ 'top_slide_text_title_' . $i ];
					}
					if ( ! empty( $lightning_theme_options[ 'top_slide_text_caption_' . $i ] ) ) {
						$mini_content_args['caption_text'] = $lightning_theme_options[ 'top_slide_text_caption_' . $i ];
					}
					if ( ! empty( $lightning_theme_options[ 'top_slide_text_btn_' . $i ] ) ) {
						$mini_content_args['btn_text'] = $lightning_theme_options[ 'top_slide_text_btn_' . $i ];
					}
					if ( ! empty( $lightning_theme_options[ 'top_slide_url_' . $i ] ) ) {
						$mini_content_args['btn_url'] = $lightning_theme_options[ 'top_slide_url_' . $i ];
					}
					if ( ! empty( $lightning_theme_options[ 'top_slide_link_blank_' . $i ] ) ) {
						$mini_content_args['btn_target'] = '_blank';
					}
					if ( ! empty( $lightning_theme_options[ 'top_slide_text_color_' . $i ] ) ) {
						$mini_content_args['btn_color_text'] = $lightning_theme_options[ 'top_slide_text_color_' . $i ];
					}
					if ( ! empty( $lightning_theme_options['color_key'] ) ) {
						$mini_content_args['btn_color_bg'] = $lightning_theme_options['color_key'];
					}

					?>

					<?php VK_Component_Mini_Contents::the_view( $mini_content_args ); ?>

					</div><!-- .mini-content -->
			  </div><!-- [ /.item ] -->

				<?php } // if ( $top_slide_image_src ) { ?>
			<?php
			$i++;
		}
		?>
	</div><!-- [ /.carousel-inner ] -->

		<?php if ( $top_slide_count >= 2 ) : ?>
		<a class="carousel-control-prev" href="#top__fullcarousel" role="button" data-slide="prev">
		<span class="carousel-control-prev-icon" aria-hidden="true"></span>
		<span class="sr-only">Previous</span>
	  </a>
	  <a class="carousel-control-next" href="#top__fullcarousel" role="button" data-slide="next">
		<span class="carousel-control-next-icon" aria-hidden="true"></span>
		<span class="sr-only">Next</span>
	  </a>
	<?php endif; ?>

	</div><!-- [ /#top__fullcarousel ] -->
	<?php endif;
} // if ( apply_filters( 'lightning_default_slide_display', true ) ) {
?>
