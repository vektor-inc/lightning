<?php
/*
 * Template Name: No sidebar ( not recommended )
 */
get_header(); ?>

<?php
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
$old_file_name[] = 'module_panList.php';
if ( locate_template( $old_file_name, false, false ) ) {
	locate_template( $old_file_name, true, false );
} else {
	get_template_part( 'template-parts/breadcrumb' );
}
?>

<div class="<?php lightning_the_class_name( 'siteContent' ); ?>">
<?php do_action( 'lightning_siteContent_prepend' ); ?>
<div class="container">
<?php do_action( 'lightning_siteContent_container_prepend' ); ?>
<div class="row">
<div class="<?php lightning_the_class_name( 'mainSection' ); ?>" id="main" role="main">
<?php do_action( 'lightning_mainSection_prepend' ); ?>

	<?php
	if ( have_posts() ) {
		while ( have_posts() ) :
			the_post();
		?>

			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php do_action( 'lightning_entry_body_before' ); ?>
	<div class="<?php lightning_the_class_name( 'entry-body' ); ?>">
	<?php the_content(); ?>
	</div>
	<?php do_action( 'lightning_entry_body_after' ); ?>

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
			</div><!-- [ /#post-<?php the_ID(); ?> ] -->

	<?php
	endwhile;
	};
?>
<?php do_action( 'lightning_mainSection_append' ); ?>
</div><!-- [ /.mainSection ] -->

</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
