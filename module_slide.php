<?php
$bvII_theme_options = get_option('bvII_theme_options');
$top_slide_count = ( isset( $bvII_theme_options['top_slide_count'] ) ) ? $bvII_theme_options['top_slide_count'] : 3 ;
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
    for ( $i = 1; $i <= $top_slide_count; ) {

            // Reset $top_slide_image_src
            $top_slide_image_src = '';
            $top_slide_url = '';

            // If 1st slide no set, set default image.
            if ( $i <= 3 ){
                if ( isset( $bvII_theme_options['top_slide_image_'.$i] )) {
                    $top_slide_image_src = $bvII_theme_options['top_slide_image_'.$i];
                } else {
                    $top_slide_image_src = get_template_directory_uri().'/images/top_image_'.$i.'.jpg';
                }
            } else {
                if ( isset( $bvII_theme_options['top_slide_image_'.$i] ))
                    $top_slide_image_src = $bvII_theme_options['top_slide_image_'.$i];
            }
            $top_slide_title = '';
            $top_slide_title = ( isset($bvII_theme_options['top_slide_title_'.$i])) ? $bvII_theme_options['top_slide_title_'.$i] : '';
            if ( $top_slide_image_src ) { ?>
            <div class="item<?php if ($i == 1) echo ' active';?>">

            <?php
            if ( isset( $bvII_theme_options['top_slide_url_'.$i] ) && $bvII_theme_options['top_slide_url_'.$i] ): ?>
                <a href="<?php echo esc_url( $bvII_theme_options['top_slide_url_'.$i] ); ?>">
            <?php endif; ?>

            <img src="<?php echo esc_attr($top_slide_image_src); ?>" alt="<?php echo esc_attr($top_slide_title); ?>">

            <?php
            if ( isset( $bvII_theme_options['top_slide_url_'.$i] ) && $bvII_theme_options['top_slide_url_'.$i] ): ?></a><?php endif; 
            ?>

            <!-- <div class="carousel-caption">
                <h2>Title</h2>
                <p>Description</p>
            </div>
            -->
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