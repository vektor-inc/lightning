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
            $top_slide_image_src = lightning_top_slide_image_src($i);

            // If Alt exist
            $top_slide_alt = '';
            if ( ! empty( $lightning_theme_options['top_slide_alt_'.$i] ) ) {
              $top_slide_alt = $lightning_theme_options['top_slide_alt_'.$i];
            } else if ( ! empty( $lightning_theme_options['top_slide_title_'.$i] ) ) {
              $top_slide_alt = $lightning_theme_options['top_slide_title_'.$i];
            } else {
              $top_slide_alt = "";
            }
            if ( $top_slide_image_src ) {
							$link_target = ( isset( $lightning_theme_options['top_slide_link_blank_'.$i] ) && $lightning_theme_options['top_slide_link_blank_'.$i] ) ? ' target="_blank"' : '';
							?>
            <div class="item item-<?php echo $i ?><?php if ($i == 1) echo ' active';?>">

            <?php

						?>

						<?php if ( lightning_is_slide_outer_link( $lightning_theme_options, $i ) ) :?>
							<a href="<?php echo esc_url( $lightning_theme_options['top_slide_url_'.$i] );?>"<?php echo $link_target; ?>>
						<?php endif; ?>

							<img src="<?php echo esc_attr($top_slide_image_src); ?>" alt="<?php echo esc_attr($top_slide_alt); ?>">

            <?php if ( lightning_is_slide_outer_link( $lightning_theme_options, $i ) ) :?>
							</a>
            <?php endif; ?>

            <div class="carousel-caption">

              <?php
              // If Text Title exist
              if ( isset($lightning_theme_options['top_slide_text_title_'.$i] ) && $lightning_theme_options['top_slide_text_title_'.$i] ): ?>
              	<h3 class="slide-title"><?php echo esc_html( $lightning_theme_options['top_slide_text_title_'.$i] ); ?></h3>
              <?php endif; ?>

              <?php
              // If Text caption exist
              if ( isset( $lightning_theme_options['top_slide_text_caption_'.$i] ) && $lightning_theme_options['top_slide_text_caption_'.$i] ): ?>
							<div class="slide-caption">
								<?php echo nl2br( esc_textarea( $lightning_theme_options['top_slide_text_caption_'.$i] ) ); ?>
							</div>
              <?php endif; ?>

              <?php
              // If Button exist
              if ( ! empty( $lightning_theme_options['top_slide_url_'.$i] ) && ! empty( $lightning_theme_options['top_slide_btn_text_'.$i] ) ) : ?>
								<a class="btn btn-primary btn-ghost" href="<?php echo esc_url( $top_slide_url ); ?>" <?php echo $link_target; ?>><?php echo wp_kses_post( $lightning_theme_options['top_slide_btn_text_'.$i] ); ?></a>
							<?php endif; ?>

            </div><!-- .carousel-caption -->

            </div><!-- [ /.item ] -->

            <?php } ?>

        <?php $i++;
    } ?>
</div><!-- [ /.carousel-inner ] -->

<?php if ($top_slide_count >= 2 ) :?>
    <a class="left carousel-control" href="#top__fullcarousel" data-slide="prev"><i class="icon-prev fa fa-angle-left"></i></a>
    <a class="right carousel-control" href="#top__fullcarousel" data-slide="next"><i class="icon-next fa fa-angle-right"></i></a>
<?php endif; ?>

</div><!-- [ /#top__fullcarousel ] -->
<?php endif; ?>
