<?php
$lightning_theme_options = get_option( 'lightning_theme_options' );
$page_comment_display = ! empty( $lightning_theme_options['page_comment_display'] ) ? true : false;
if ( have_posts() ) {
	while ( have_posts() ) :
		the_post(); ?>
		<article id="post-<?php the_ID(); ?>" <?php post_class( apply_filters( 'lightning_article_outer_class', '' ) ); ?>>

		<?php do_action( 'lightning_entry_body_before' ); ?>
		<div class="<?php lightning_the_class_name( 'entry-body' ); ?>">
			<?php do_action( 'lightning_content_before' ); ?>
			<?php the_content(); ?>
			<?php do_action( 'lightning_content_after' ); ?>
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
			<?php do_action( 'lightning_comment_before' ); ?>
			<?php if ( true === $page_comment_display ) : ?>
				<?php comments_template( '', true );?>
			<?php endif; ?>
			<?php do_action( 'lightning_comment_after' ); ?>
		</article><!-- [ /#post-<?php the_ID(); ?> ] -->
	<?php endwhile;
}
?>