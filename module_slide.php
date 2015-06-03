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
            <img src="<?php echo esc_attr($top_slide_image_src); ?>" alt="<?php echo esc_attr($top_slide_title); ?>">
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

            <div class="carousel-overfixTxt hidden-xs hidden-sm">
            <h2>スコアって何？</h2>
                    <p class="lead-small">ゴルフは遊び！！！&nbsp;
                    <br>ナイスショットのときは全身で喜んで&nbsp;
                    <br>ミスしたときは少しふてくされるくらいがちょうどいい
                    <br>青空の下、寝ころびたくなるようなフェアウェイ
                    <br>そんな雄大な大自然の中で&nbsp;
                    <br>のびのびと、華麗に、自由に、&nbsp;
                    <br>ゴルフをできる喜びを今一度思い出しませんか？&nbsp;
                    <br>大切な遊び心を“grandeur”とともに・・・</p>
            </div>

</div><!-- [ /#top__fullcarousel ] -->
<?php endif; ?>

<div class="section sectionBox hidden-md hidden-lg topConcept">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
            <h2 class="text-center">スコアって何？</h2>
                    <p class="lead-small text-center">ゴルフは遊び！！！&nbsp;
                    <br>ナイスショットのときは全身で喜んで&nbsp;
                    <br>ミスしたときは少しふてくされるくらいがちょうどいい
                    <br>青空の下、寝ころびたくなるようなフェアウェイ
                    <br>そんな雄大な大自然の中で&nbsp;
                    <br>のびのびと、華麗に、自由に、&nbsp;
                    <br>ゴルフをできる喜びを今一度思い出しませんか？&nbsp;
                    <br>大切な遊び心を“grandeur”とともに・・・</p>
</div></div></div></div>