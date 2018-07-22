<?php get_header(); ?>

<?php get_template_part( 'module_slide' ); ?>

<div class="section siteContent">
	<div class="container">
		<div class="row">

			<?php
			if ( lightning_is_frontpage_onecolumn() ) {
				$main_col = 'col-md-12';
			} else {
				$main_col = 'col-md-8';
			}
			?>

			<div class="<?php echo $main_col; ?> mainSection">

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
						while ( have_posts() ) :
							the_post();
?>

							<?php get_template_part( 'module_loop_post' ); ?>

						<?php endwhile; ?>

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

			</div><!-- [ /.mainSection ] -->

			<?php if ( ! lightning_is_frontpage_onecolumn() ) : ?>

				<div class="col-md-3 col-md-offset-1 subSection sideSection">
					<?php get_sidebar(); ?>
				</div><!-- [ /.subSection ] -->

			<?php endif; ?>

		</div><!-- [ /.row ] -->
	</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
