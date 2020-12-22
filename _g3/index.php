<?php lightning_get_template_part( 'header' ); ?>

<?php 
if ( lightning_is_site_header() ){
    do_action( 'lightning_header_before' );
    lightning_get_template_part( 'template-parts/header' );
    do_action( 'lightning_header_after' );
} 
?>

<div class="<?php lightning_the_class_name( 'site-body' ); ?>">
    <?php do_action( 'lightning_site-body_prepend' ); ?>

    <div class="<?php lightning_the_class_name( 'site-body__container' ); ?> container">

        <?php if ( lightning_is_page_header() ){
            do_action( 'lightning_page-header_before' );
            lightning_get_template_part( 'template-parts/page-header' );
            do_action( 'lightning_page-header_after' );
        } ?>

        <?php if ( lightning_is_breadcrumb() ){
            do_action( 'lightning_breadcrumb_before' );
            VK_Breadcrumb::the_breadcrumb();
            do_action( 'lightning_breadcrumb_after' );
        } ?>

        <div class="<?php lightning_the_class_name( 'main-section' ); ?>" id="main" role="main">
            <?php do_action( 'lightning_main-section_prepend' ); ?>

            <?php do_action( 'lightning_main-section_append' ); ?>
        </div><!-- [ /.main-section ] -->

        <?php if ( lightning_is_subsection_display() ){ ?>
            <?php get_sidebar( get_post_type() ); ?>
        <?php } ?>

    </div><!-- [ /.site-body__container ] -->

    <?php do_action( 'lightning_site-body_apepend' ); ?>
</div><!-- [ /.site-body ] -->

<?php 
if ( lightning_is_site_header() ){
    do_action( 'lightning_header_before' );
    lightning_get_template_part( 'template-parts/header' );
    do_action( 'lightning_header_after' );
} 
?>
<?php lightning_get_template_part( 'footer' ); ?>
