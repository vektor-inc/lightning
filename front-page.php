<?php get_header(); ?>

<?php
if ( $bootstrap == '3' ) {
	$old_file_name[] = 'module_slide.php';
	if ( locate_template( $old_file_name, false, false ) ) {
		locate_template( $old_file_name, true, false );
	} else {
		get_template_part( 'template-parts/slide', 'bs3' );
	}
} else {
	get_template_part( 'template-parts/slide', 'bs4' );
}
?>

<div class="<?php lightning_the_class_name( 'siteContent' ); ?>">
<?php do_action( 'lightning_siteContent_prepend' ); ?>
<div class="container">
<?php do_action( 'lightning_siteContent_container_prepend' ); ?>
<div class="row">

			<div class="<?php lightning_the_class_name( 'mainSection' ); ?>">

			<?php do_action( 'lightning_home_content_top_widget_area_before' ); ?>

			<?php if ( is_active_sidebar( 'home-content-top-widget-area' ) ) : ?>
				<?php dynamic_sidebar( 'home-content-top-widget-area' ); ?>
			<?php endif; ?>

			<?php do_action( 'lightning_home_content_top_widget_area_after' ); ?>

			<?php if ( apply_filters( 'is_lightning_home_content_display', true ) ) : ?>

				<?php if ( have_posts() ) : ?>

					<?php if ( 'page' == get_option( 'show_on_front' ) ) : ?>

						<?php
						while ( have_posts() ) :
							the_post();
							?>

						<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
						<div class="entry-body">
							<?php the_content(); ?>
						</div>
							<?php
							wp_link_pages(
								array(
									'before' => '<div class="page-link">' . 'Pages:',
									'after'  => '</div>',
								)
							);
							?>
						 </article><!-- [ /#post-<?php the_ID(); ?> ] -->

					<?php endwhile; ?>

				<?php else : ?>

					<div class="postList">

						<?php
						/**
						 * Dealing with old files
						 * Actually, it's ok to only use get_template_part().
						 * It is measure for before version 7.0 that loaded module_loop_***.php.
						 */
						$postType = lightning_get_post_type();
						$old_file_name[] = 'module_loop_' . $postType['slug'] . '.php';
						$old_file_name[] = 'module_loop_post.php';
						$require_once    = false;

						while ( have_posts() ) :
							the_post();

							if ( locate_template( $old_file_name, false, $require_once ) ) {
								locate_template( $old_file_name, true, $require_once );
							} else {
								get_template_part( 'template-parts/post/loop', $postType['slug'] );
							}

						endwhile; ?>

						<?php
						the_posts_pagination(
							array(
								'mid_size'           => 1,
								'prev_text'          => '&laquo;',
								'next_text'          => '&raquo;',
								'type'               => 'list',
								'before_page_number' => '<span class="meta-nav screen-reader-text">' . __( 'Page', 'lightning' ) . ' </span>',
							)
						);
						?>

					</div><!-- [ /.postList ] -->

				<?php endif; // if ( 'page' == get_option('show_on_front') ) : ?>

			<?php else : ?>

				<div class="well"><p><?php _e( 'No posts.', 'lightning' ); ?></p></div>

			<?php endif; // have_post() ?>

			<?php endif; // if ( apply_filters( 'is_lightning_home_top_posts_display', true ) ) : ?>

			<?php do_action( 'lightning_mainSection_append' ); ?>
			</div><!-- [ /.mainSection ] -->

			<?php if ( lightning_is_subsection_display() ) : ?>
				<div class="<?php lightning_the_class_name( 'sideSection' ); ?>">
					<?php get_sidebar(); ?>
				</div><!-- [ /.subSection ] -->
			<?php endif; ?>

</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
