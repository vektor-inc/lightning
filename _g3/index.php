<?php lightning_get_template_part( 'header' ); ?>

<?php 
do_action( 'lightning_site-header_before' );
if ( apply_filters( 'lightning_is_site-header', true ) ){
    lightning_get_template_part( 'template-parts/site-header' );
}
do_action( 'lightning_site-header_after' );
?>

<div class="<?php lightning_the_class_name( 'site-body' ); ?>">
    <?php do_action( 'lightning_site-body_prepend' ); ?>

    <div class="<?php lightning_the_class_name( 'site-body__container' ); ?> container">

        <?php 
        do_action( 'lightning_page-header_before' );
        if ( lightning_is_page_header() ){
            lightning_get_template_part( 'template-parts/page-header' );
        }
        do_action( 'lightning_page-header_after' );
        ?>

        <?php 
        do_action( 'lightning_breadcrumb_before' );
        if ( lightning_is_breadcrumb() ){
            VK_Breadcrumb::the_breadcrumb();
        }
        do_action( 'lightning_breadcrumb_after' );
        ?>

        <div class="<?php lightning_the_class_name( 'main-section' ); ?>" id="main" role="main">
            <?php do_action( 'lightning_main-section_prepend' ); ?>

            <?php if ( is_singular() ){
                lightning_get_template_part( 'template-parts/main-singular' );
            } else {
                lightning_get_template_part( 'template-parts/main-archive' );
            } ?>

            <?php do_action( 'lightning_main-section_append' ); ?>
        </div><!-- [ /.main-section ] -->

        <?php 
        do_action( 'lightning_sub-section_before' );
        if ( apply_filters( 'lightning_is_sub-section', true ) ){ 
            get_sidebar( get_post_type() );
        }
        do_action( 'lightning_sub-section_after' );
        ?>

    </div><!-- [ /.site-body__container ] -->

    <?php do_action( 'lightning_site-body_apepend' ); ?>
</div><!-- [ /.site-body ] -->

<?php 
do_action( 'lightning_site-footer_before' );
if ( apply_filters( 'lightning_is_site-footer', true ) ) {
    lightning_get_template_part( 'template-parts/site-footer' );
}
do_action( 'lightning_site-footer_after' );
?>

<?php lightning_get_template_part( 'footer' ); ?>
