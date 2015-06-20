</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->

<?php if ( is_active_sidebar( 'footer-upper-widget-1' ) ) : ?>
<div class="section sectionBox shopBox">
    <div class="container ">
        <div class="row ">
            <div class="col-md-12 ">
            <?php dynamic_sidebar( 'footer-upper-widget-1' ); ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<footer class="section">
    <div class="footerMenu">
       <div class="container">
            <?php wp_nav_menu( array(
                'theme_location'    => 'Footer',
                'container'         => 'nav',
                'items_wrap'        => '<ul id="%1$s" class="%2$s nav nav-pills">%3$s</ul>',
                'fallback_cb'       => ''
            ) ); ?>
        </div>
    </div>
    <div class="container sectionBox">
        <div class="row ">
            <?php
            // Area setting
            $footer_widget_area = array(
                // Use 3 widget area
                3 => array('class' => 'col-md-4'),
                // Use 4 widget area
                4 => array('class' => 'col-md-3'),
                );
            $footer_widget_area_count = 3;

            // Print widget area
            for ( $i = 1; $i <= $footer_widget_area_count; ) {
                echo '<div class="'.$footer_widget_area[$footer_widget_area_count]['class'].'">';
                    if ( is_active_sidebar( 'footer-widget-'.$i ) ) dynamic_sidebar( 'footer-widget-'.$i );
                echo '</div>'; 
                $i++;
            }
            ?>
        </div>
    </div>
    <div class="sectionBox copySection">
        <div class="row">
            <div class="col-md-12 text-center">
            <?php lightning_footerCopyRight(); ?>
            </div>
        </div>
    </div>
</footer>
<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->

<?php wp_footer();?>
</body>
</html>