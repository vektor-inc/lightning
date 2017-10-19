<?php
$lightning_theme_options = get_option('lightning_theme_options');

// count top slide
$top_slide_count = 0;
$top_slide_count_max = 5;
$top_slide_count_max = apply_filters('lightning_top_slide_count_max',$top_slide_count_max);

for ( $i = 1; $i <= $top_slide_count_max; ) {
    if ( !isset( $lightning_theme_options['top_slide_image_'.$i] ) ){
        if ( $i <= 3  )
            $top_slide_count ++;
    } else if ( $lightning_theme_options['top_slide_image_'.$i] ) {
        $top_slide_count ++;
    }
    $i++;
}

$top_slide_count = apply_filters('lightning_top_slide_count',$top_slide_count);

if ($top_slide_count) : ?>
<div id="top__fullcarousel" data-interval="false" class="carousel slide" data-ride="carousel">
<div class="carousel-inner">

    <?php if ($top_slide_count >= 2 ) :?>
        <!-- Indicators -->
        <ol class="carousel-indicators">
        <?php for ( $i = 0; $i <= $top_slide_count - 1; ) { ?>
	        <li data-target="#top__fullcarousel" data-slide-to="<?php echo $i; ?>"></li>
	        <?php
	        $i++;
        } ?>
        </ol>
    <?php endif; ?>

    <?php
    for ( $i = 1; $i <= $top_slide_count_max; ) {

			$top_slide_url = '';

			// If Alt exist
			$top_slide_alt = '';
			if ( ! empty( $lightning_theme_options['top_slide_alt_'.$i] ) ) {
				$top_slide_alt = $lightning_theme_options['top_slide_alt_'.$i];
			} else if ( ! empty( $lightning_theme_options['top_slide_title_'.$i] ) ) {
				$top_slide_alt = $lightning_theme_options['top_slide_title_'.$i];
			} else {
				$top_slide_alt = "";
			}
			if ( ! empty( $lightning_theme_options['top_slide_image_'.$i] ) ) {
				$link_target = ( isset( $lightning_theme_options['top_slide_link_blank_'.$i] ) && $lightning_theme_options['top_slide_link_blank_'.$i] ) ? ' target="_blank"' : '';
				?>
				<div class="item item-<?php echo $i ?><?php if ($i == 1) echo ' active';?>">

					<?php if ( lightning_is_slide_outer_link( $lightning_theme_options, $i ) ) :?>
						<a href="<?php echo esc_url( $lightning_theme_options['top_slide_url_'.$i] );?>"<?php echo $link_target; ?>>
					<?php endif; ?>

					<picture>
						<?php if ( ! empty( $lightning_theme_options['top_slide_image_mobile_'.$i] ) ) : ?>
					  	<source media="(max-width: 767px)" srcset="<?php echo esc_attr( $lightning_theme_options['top_slide_image_mobile_'.$i] )?>">
						<?php endif; ?>
					  <img src="<?php echo esc_attr( $lightning_theme_options['top_slide_image_'.$i] )?>" alt="<?php echo esc_attr($top_slide_alt); ?>">
					</picture>

					<?php if ( lightning_is_slide_outer_link( $lightning_theme_options, $i ) ) :?>
						</a>
					<?php endif; ?>

					<?php
					$style = '';
					if ( ! empty( $lightning_theme_options['top_slide_text_align_'.$i] ) ){
						$style = ' style="text-align:'.esc_attr( $lightning_theme_options['top_slide_text_align_'.$i] ).'"';
					}
					 ?>
					<div class="slide-text-set"<?php echo $style;?>>
						<div class="container">

	              <?php
	              // If Text Title exist
	              if ( isset($lightning_theme_options['top_slide_text_title_'.$i] ) && $lightning_theme_options['top_slide_text_title_'.$i] ):
									if ( function_exists('lightning_top_slide_font_style') ) {
										$top_slide_font_style = lightning_top_slide_font_style( $lightning_theme_options, $i );
									} else {
										$top_slide_font_style = '';
									}
								?>
	              	<h3 class="slide-text-title" style="<?php echo esc_attr( $top_slide_font_style );?>"><?php echo nl2br( esc_textarea(  $lightning_theme_options['top_slide_text_title_'.$i] ) ); ?></h3>
	              <?php endif; ?>

	              <?php
	              // If Text caption exist
	              if ( isset( $lightning_theme_options['top_slide_text_caption_'.$i] ) && $lightning_theme_options['top_slide_text_caption_'.$i] ): ?>
								<div class="slide-text-caption" style="<?php echo esc_attr( $top_slide_font_style );?>">
									<?php echo nl2br( esc_textarea( $lightning_theme_options['top_slide_text_caption_'.$i] ) ); ?>
								</div>
	              <?php endif; ?>

	              <?php
	              // If Button exist
	              if ( ! empty( $lightning_theme_options['top_slide_url_'.$i] ) && ! empty( $lightning_theme_options['top_slide_btn_text_'.$i] ) ) :
									$text_color = ( ! empty( $lightning_theme_options['top_slide_text_color_'.$i] ) ) ? $lightning_theme_options['top_slide_text_color_'.$i] : '#fff';
									$color_key = ( ! empty( $lightning_theme_options['color_key'] ) ) ? $lightning_theme_options['color_key'] : '#337ab7';
									// Shadow
									$box_shadow = '';
									$text_shadow = '';
									if ( isset( $lightning_theme_options[ 'top_slide_text_shadow_use_'.$i ] ) && $lightning_theme_options[ 'top_slide_text_shadow_use_'.$i ] ) {
										if ( ! empty( $lightning_theme_options[ 'top_slide_text_shadow_color_'.$i ] ) ){
											$box_shadow = 'box-shadow:0 0 2px '.$lightning_theme_options[ 'top_slide_text_shadow_color_'.$i ].';';
											$text_shadow = 'text-shadow:0 0 2px '.$lightning_theme_options[ 'top_slide_text_shadow_color_'.$i ].';';
										} else {
											$box_shadow = 'box-shadow:0 0 2px #000;';
											$text_shadow = 'text-shadow:0 0 2px #000;';
										}
									}

									echo '<style type="text/css">';
									echo '.item-'.$i.' .btn-ghost { border-color:'.$text_color.';color:'.$text_color.';'.$box_shadow.$text_shadow.' }';
									echo '.item-'.$i.' .btn-ghost:hover { border-color:'.$color_key.'; background-color:'.$color_key.'; color:#fff; text-shadow:none; }';
									echo '</style>';
									?>
									<a class="btn btn-ghost" href="<?php echo esc_url( $top_slide_url ); ?>" <?php echo $link_target; ?>><?php echo wp_kses_post( $lightning_theme_options['top_slide_btn_text_'.$i] ); ?></a>
								<?php endif; ?>

	            </div><!-- .container -->
						</div><!-- [ /.slide-text-set ] -->
	      </div><!-- [ /.item ] -->

			<?php } // if ( $top_slide_image_src ) { ?>
      <?php $i++;
    } ?>
</div><!-- [ /.carousel-inner ] -->

<?php if ($top_slide_count >= 2 ) :?>
    <a class="left carousel-control" href="#top__fullcarousel" data-slide="prev"><i class="icon-prev fa fa-angle-left"></i></a>
    <a class="right carousel-control" href="#top__fullcarousel" data-slide="next"><i class="icon-next fa fa-angle-right"></i></a>
<?php endif; ?>

</div><!-- [ /#top__fullcarousel ] -->
<?php endif; ?>
