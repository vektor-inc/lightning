<?php
/*
 * Template Name: Landing Page for Page Builder ( not recommended )
 */
get_header(); ?>

<div class="<?php lightning_the_class_name( 'siteContent' ); ?>">
<?php do_action( 'lightning_siteContent_prepend' ); ?>

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
}
?>

</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
