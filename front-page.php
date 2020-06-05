<?php
/**
 * Front Page Template of Lightning
 *
 * @package Lightning
 */

get_header();
do_action( 'lightning_top_slide_before' );
if ( empty( $lightning_theme_options['top_slide_hide'] ) ) {
	if ( '3' === $bootstrap ) {
		$old_file_name[] = 'module_slide.php';
		if ( locate_template( $old_file_name, false, false ) ) {
			locate_template( $old_file_name, true, false );
		} else {
			get_template_part( 'template-parts/slide', 'bs3' );
		}
	} else {
		get_template_part( 'template-parts/slide', 'bs4' );
	}
}
do_action( 'lightning_top_slide_after' );
?>

<div class="<?php lightning_the_class_name( 'siteContent' ); ?>">
	<?php do_action( 'lightning_siteContent_prepend' ); ?>
	<div class="container">
		<?php do_action( 'lightning_siteContent_container_prepend' ); ?>
		<div class="row">
			<div class="<?php lightning_the_class_name( 'mainSection' ); ?>">

				<?php
				do_action( 'lightning_home_content_top_widget_area_before' );
				if ( is_active_sidebar( 'home-content-top-widget-area' ) ) {
					dynamic_sidebar( 'home-content-top-widget-area' );
				}
				do_action( 'lightning_home_content_top_widget_area_after' );

				if ( apply_filters( 'is_lightning_home_content_display', true ) ) {
					if ( have_posts() ) {
						if ( 'page' === get_option( 'show_on_front' ) ) {
							while ( have_posts() ) {
								the_post();
								// Add class only Here.
								$article_outer_class = '';
								$article_outer_class = apply_filters( 'lightning_article_outer_class', $article_outer_class );
								?>

								<article id="post-<?php the_ID(); ?>" <?php post_class( $article_outer_class ); ?>>

									<?php do_action( 'lightning_entry_body_before' ); ?>

									<div class="<?php lightning_the_class_name( 'entry-body' ); ?>">
										<?php the_content(); ?>
									</div>

									<?php
									do_action( 'lightning_entry_body_after' );
									wp_link_pages(
										array(
											'before' => '<div class="page-link">Pages:',
											'after'  => '</div>',
										)
									);
									?>
								</article>
								<?php
							}
						} else {
							do_action( 'lightning_loop_before' );
							?>

							<div class="<?php lightning_the_class_name( 'postList' ); ?>">
								<?php
								if ( apply_filters( 'is_lightning_extend_loop', false ) ) {
									do_action( 'lightning_extend_loop' );
								} else {
									/**
									 * Dealing with old files
									 * Actually, it's ok to only use get_template_part().
									 * It is measure for before version 7.0 that loaded module_loop_***.php.
									 */
									$loop_post_type  = lightning_get_post_type();
									$old_file_name[] = 'module_loop_' . $loop_post_type['slug'] . '.php';
									$old_file_name[] = 'module_loop_post.php';
									$require_once    = false;

									while ( have_posts() ) {
										the_post();

										if ( locate_template( $old_file_name, false, $require_once ) ) {
											locate_template( $old_file_name, true, $require_once );
										} else {
											get_template_part( 'template-parts/post/loop', $loop_post_type['slug'] );
										}
									}
								}
								the_posts_pagination(
									array(
										'mid_size'  => 1,
										'prev_text' => '&laquo;',
										'next_text' => '&raquo;',
										'type'      => 'list',
										'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'lightning' ) . ' </span>',
									)
								);
								?>
							</div><!-- [ /.postList ] -->

							<?php
						}
					} else {
						?>
						<div class="well"><p><?php esc_html_e( 'No posts.', 'lightning' ); ?></p></div>
						<?php
					}
				}
				do_action( 'lightning_loop_after' );
				do_action( 'lightning_mainSection_append' );
				?>
			</div><!-- [ /.mainSection ] -->

			<?php if ( lightning_is_subsection_display() ) { ?>
				<div class="<?php lightning_the_class_name( 'sideSection' ); ?>">
					<?php get_sidebar(); ?>
				</div><!-- [ /.subSection ] -->
				<?php
			}
			// Hook for Add Sidebar.
			do_action( 'lightning_additional_sidebar' );
			?>

		</div><!-- [ /.row ] -->
		<?php do_action( 'lightning_site_content_container_append' ); ?>
	</div><!-- [ /.container ] -->
	<?php do_action( 'lightning_site_content_append' ); ?>
</div><!-- [ /.siteContent ] -->
<?php
get_footer();
