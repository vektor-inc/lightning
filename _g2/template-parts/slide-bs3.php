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
		?>
	<div id="top__fullcarousel" data-interval="<?php echo $interval; ?>" class="carousel slide slide-main" data-ride="carousel">
<div class="carousel-inner">

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
				<div class="item item-<?php echo $i; ?>
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

					<?php

					/*
					  mini_content
					/*-------------------------------------------*/

					$mini_content_args['style_class']  = 'mini-content-' . $i;
					$mini_content_args['align']        = ( ! empty( $lightning_theme_options[ 'top_slide_text_align_' . $i ] ) ) ? $lightning_theme_options[ 'top_slide_text_align_' . $i ] : '';
					$mini_content_args['title']        = ( ! empty( $lightning_theme_options[ 'top_slide_text_title_' . $i ] ) ) ? $lightning_theme_options[ 'top_slide_text_title_' . $i ] : '';
					$mini_content_args['caption']      = ( ! empty( $lightning_theme_options[ 'top_slide_text_caption_' . $i ] ) ) ? $lightning_theme_options[ 'top_slide_text_caption_' . $i ] : '';
					$mini_content_args['text_color']   = ( ! empty( $lightning_theme_options[ 'top_slide_text_color_' . $i ] ) ) ? $lightning_theme_options[ 'top_slide_text_color_' . $i ] : '#333';
					$mini_content_args['link_url']     = ( ! empty( $lightning_theme_options[ 'top_slide_url_' . $i ] ) ) ? $lightning_theme_options[ 'top_slide_url_' . $i ] : '';
					$mini_content_args['link_target']  = ( ! empty( $lightning_theme_options[ 'top_slide_link_blank_' . $i ] ) ) ? ' target="_blank"' : '';
					$mini_content_args['btn_text']     = ( ! empty( $lightning_theme_options[ 'top_slide_text_btn_' . $i ] ) ) ? $lightning_theme_options[ 'top_slide_text_btn_' . $i ] : '';
					$mini_content_args['btn_color']    = ( ! empty( $lightning_theme_options[ 'top_slide_text_color_' . $i ] ) ) ? $lightning_theme_options[ 'top_slide_text_color_' . $i ] : '#337ab7';
					$mini_content_args['btn_bg_color'] = ( ! empty( $lightning_theme_options['color_key'] ) ) ? $lightning_theme_options['color_key'] : '#337ab7';
					$mini_content_args['shadow_use']   = ( ! empty( $lightning_theme_options[ 'top_slide_text_shadow_use_' . $i ] ) ) ? $lightning_theme_options[ 'top_slide_text_shadow_use_' . $i ] : false;
					$mini_content_args['shadow_color'] = ( ! empty( $lightning_theme_options[ 'top_slide_text_shadow_color_' . $i ] ) ) ? $lightning_theme_options[ 'top_slide_text_shadow_color_' . $i ] : '#fff';

					// lightning_mini_content( $mini_content_args );

					$style = '';
					if ( $mini_content_args['align'] ) {
						$style = ' style="text-align:' . esc_attr( $mini_content_args['align'] ) . '"';
					}
					?>
					<div class="slide-text-set mini-content <?php echo esc_attr( $mini_content_args['style_class'] ); ?>"<?php echo $style; ?>>
						<div class="container">

					<?php

							$font_style = '';
					if ( $mini_content_args['text_color'] ) {
						$font_style .= 'color:' . $mini_content_args['text_color'] . ';';
					} else {
						$font_style .= '';
					}

					if ( $mini_content_args['shadow_use'] ) {
						if ( $mini_content_args['shadow_color'] ) {
							$font_style .= 'text-shadow:0 0 2px ' . $mini_content_args['shadow_color'];
						} else {
							$font_style .= 'text-shadow:0 0 2px #000';
						}
					}

							$font_style = ( $font_style ) ? ' style="' . esc_attr( $font_style ) . '"' : '';

							// If Text Title exist
					if ( $mini_content_args['title'] ) :
						?>
					  <h3 class="slide-text-title"<?php echo $font_style; ?>>
										<?php echo nl2br( wp_kses_post( $mini_content_args['title'] ) ); ?>
								  </h3>
					<?php endif; ?>

					<?php
					// If Text caption exist
					if ( $mini_content_args['caption'] ) :
						?>
									  <div class="slide-text-caption"<?php echo $font_style; ?>>
											<?php echo nl2br( wp_kses_post( $mini_content_args['caption'] ) ); ?>
									  </div>
					<?php endif; ?>

					<?php
					// If Button exist
					if ( $mini_content_args['link_url'] && $mini_content_args['btn_text'] ) :
									// Shadow
									$box_shadow  = '';
									$text_shadow = '';
						if ( $mini_content_args['shadow_use'] ) {
							if ( $mini_content_args['shadow_color'] ) {
								$box_shadow  = 'box-shadow:0 0 2px ' . $mini_content_args['shadow_color'] . ';';
								$text_shadow = 'text-shadow:0 0 2px ' . $mini_content_args['shadow_color'] . ';';
							} else {
								$box_shadow  = 'box-shadow:0 0 2px #000;';
								$text_shadow = 'text-shadow:0 0 2px #000;';
							}
						}

									$style_class = esc_attr( $mini_content_args['style_class'] );
									echo '<style type="text/css">';
									echo '.' . $style_class . ' .btn-ghost { border-color:' . $mini_content_args['text_color'] . ';color:' . $mini_content_args['text_color'] . ';' . $box_shadow . $text_shadow . ' }';
									echo '.' . $style_class . ' .btn-ghost:hover { border-color:' . $mini_content_args['btn_bg_color'] . '; background-color:' . $mini_content_args['btn_bg_color'] . '; color:#fff; text-shadow:none; }';
									echo '</style>';
						?>
									<a class="btn btn-ghost" href="<?php echo esc_url( $mini_content_args['link_url'] ); ?>"<?php echo $mini_content_args['link_target']; ?>><?php echo wp_kses_post( $mini_content_args['btn_text'] ); ?></a>
									<?php endif; ?>

					</div><!-- .container -->
							</div><!-- [ /.slide-text-set.mini-content  ] -->
			  </div><!-- [ /.item ] -->

				<?php } // if ( $top_slide_image_src ) { ?>
			<?php
			$i++;
		}
		?>
	</div><!-- [ /.carousel-inner ] -->

		<?php if ( $top_slide_count >= 2 ) : ?>
	<a class="left carousel-control" href="#top__fullcarousel" data-slide="prev"><i class="icon-prev fa fa-angle-left"></i></a>
	<a class="right carousel-control" href="#top__fullcarousel" data-slide="next"><i class="icon-next fa fa-angle-right"></i></a>
	<?php endif; ?>

	</div><!-- [ /#top__fullcarousel ] -->
	<?php endif;
} // if ( apply_filters( 'lightning_default_slide_display', true ) ) {
?>
