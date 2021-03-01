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

	<article id="post-<?php the_ID(); ?>" <?php post_class( apply_filters( 'lightning_article_outer_class', '' ) ); ?>>

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
	</article><!-- [ /#post-<?php the_ID(); ?> ] -->

<?php
endwhile;
}
?>

<?php do_action( 'lightning_siteContent_apepend' ); ?>
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
