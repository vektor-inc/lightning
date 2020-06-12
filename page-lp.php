<?php
/*
 * Template Name: Landing Page ( not recommended )
 */
get_header(); ?>

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

			<?php
			global $post;
			$widget_id = 'lp-widget-' . $post->ID;
			if ( is_active_sidebar( $widget_id ) ) :
				dynamic_sidebar( $widget_id );
			endif;

			$content = get_the_content();
			if ( $content ) :
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

	<?php endif; ?>

	<?php
	endwhile;
	};
?>
<?php do_action( 'lightning_mainSection_append' ); ?>
</div><!-- [ /.mainSection ] -->

</div><!-- [ /.row ] -->
<?php do_action( 'lightning_siteContent_container_apepend' ); ?>
</div><!-- [ /.container ] -->
<?php do_action( 'lightning_siteContent_apepend' ); ?>
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
