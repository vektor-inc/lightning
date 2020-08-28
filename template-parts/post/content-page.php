<?php
$options = get_option( 'lightning_theme_options' );
$deahult = lightning_default_comment_options();
$options = wp_parse_args( $options, $deahult );

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
			<?php if ( empty( $options['hide_comment'][get_post_type()] ) && post_type_supports( get_post_type(), 'comments' ) ) : ?>
				<?php comments_template( '', true );?>
			<?php endif; ?>
			<?php do_action( 'lightning_comment_after' ); ?>
		</article><!-- [ /#post-<?php the_ID(); ?> ] -->
	<?php endwhile;
}
?>