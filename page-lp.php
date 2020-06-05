<?php
/**
 * LP Page Template of Lightning
 * Template Name: Landing Page ( not recommended )
 *
 * @package Lightning
 */

get_header();
?>

<div class="<?php lightning_the_class_name( 'siteContent' ); ?>">
	<?php do_action( 'lightning_siteContent_prepend' ); ?>
	<div class="container">
		<?php do_action( 'lightning_siteContent_container_prepend' ); ?>
		<div class="row">
			<div class="<?php lightning_the_class_name( 'mainSection' ); ?>" id="main" role="main">
				<?php
				do_action( 'lightning_mainSection_prepend' );

				if ( have_posts() ) {
					while ( have_posts() ) {
						the_post();

						global $post;
						$widget_id = 'lp-widget-' . $post->ID;
						if ( is_active_sidebar( $widget_id ) ) {
							dynamic_sidebar( $widget_id );
						}

						$content = get_the_content();
						if ( $content ) {
							?>

							<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

								<?php do_action( 'lightning_entry_body_before' ); ?>
								<div class="<?php lightning_the_class_name( 'entry-body' ); ?>">
									<?php the_content(); ?>
								</div>
								<?php
								do_action( 'lightning_entry_body_after' );
								$args = array(
									'before'      => '<nav class="page-link"><dl><dt>Pages :</dt><dd>',
									'after'       => '</dd></dl></nav>',
									'link_before' => '<span class="page-numbers">',
									'link_after'  => '</span>',
									'echo'        => 1,
								);
								wp_link_pages( $args );
								?>
							</div>
							<?php
						}
					}
				}
				do_action( 'lightning_mainSection_append' );
				?>
			</div><!-- [ /.mainSection ] -->

		</div><!-- [ /.row ] -->
		<?php do_action( 'lightning_site_content_container_append' ); ?>
	</div><!-- [ /.container ] -->
	<?php do_action( 'lightning_site_content_append' ); ?>
</div><!-- [ /.siteContent ] -->
<?php
get_footer();
