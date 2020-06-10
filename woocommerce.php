<?php get_header(); ?>

<?php
if ( lightning_is_page_header_and_breadcrumb() ){

	// Dealing with old files.
	// Actually, it's ok to only use get_template_part().
	/*-------------------------------------------*/
	/* Page Header
	/*-------------------------------------------*/
	$old_file_name[] = 'module_pageTit.php';
	if ( locate_template( $old_file_name, false, false ) ) {
		locate_template( $old_file_name, true, false );
	} else {
		get_template_part( 'template-parts/page-header' );
	}
	/*-------------------------------------------*/
	/* BreadCrumb
	/*-------------------------------------------*/
	do_action( 'lightning_breadcrumb_before' );
	$old_file_name[] = 'module_panList.php';
	if ( locate_template( $old_file_name, false, false ) ) {
		locate_template( $old_file_name, true, false );
	} else {
		get_template_part( 'template-parts/breadcrumb' );
	}
	do_action( 'lightning_breadcrumb_after' );

} // if ( lightning_is_page_header_and_top_breadcrumb() ){
?>

<div class="<?php lightning_the_class_name( 'siteContent' ); ?>">
<?php do_action( 'lightning_siteContent_prepend' ); ?>
<div class="container">
<?php do_action( 'lightning_siteContent_container_prepend' ); ?>
<div class="row">
<div class="<?php lightning_the_class_name( 'mainSection' ); ?>" id="main" role="main">
<?php do_action( 'lightning_mainSection_prepend' ); ?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php do_action( 'lightning_entry_body_before' ); ?>
	<div class="<?php lightning_the_class_name( 'entry-body' ); ?>">
	<?php woocommerce_content(); ?>
	</div>
	<?php
	$args = array(
		'before'      => '<nav class="page-link"><dl><dt>Pages :</dt><dd>',
		'after'       => '</dd></dl></nav>',
		'link_before' => '<span class="page-numbers">',
		'link_after'  => '</span>',
		'echo'        => 1,
	);
	wp_link_pages( $args );
	?>

	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		// do_action( 'woocommerce_after_main_content' );
	?>
	</article><!-- [ /#post-<?php the_ID(); ?> ] -->


<?php do_action( 'lightning_mainSection_append' ); ?>
</div><!-- [ /.mainSection ] -->

<?php if ( lightning_is_subsection_display() ) : ?>
<div class="<?php lightning_the_class_name( 'sideSection' ); ?>">
	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
	?>
<?php get_sidebar( get_post_type() ); ?>
</div><!-- [ /.subSection ] -->
<?php endif; ?>

<?php do_action( 'lightning_additional_section' ); ?>

</div><!-- [ /.row ] -->
<?php do_action( 'lightning_siteContent_container_apepend' ); ?>
</div><!-- [ /.container ] -->
<?php do_action( 'lightning_siteContent_apepend' ); ?>
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
