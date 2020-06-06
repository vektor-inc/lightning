<?php
/**
 * WooCommerce Template of Lightning
 *
 * @package Lightning
 */

get_header();
if ( lightning_is_page_header_and_breadcrumb() ) {

	// Dealing with old files.
	// Actually, it's ok to only use get_template_part().

	// Page Header.
	$old_file_name[] = 'module_pageTit.php';
	if ( locate_template( $old_file_name, false, false ) ) {
		locate_template( $old_file_name, true, false );
	} else {
		get_template_part( 'template-parts/page-header' );
	}

	// BreadCrumb.
	do_action( 'lightning_breadcrumb_before' );
	$old_file_name[] = 'module_panList.php';
	if ( locate_template( $old_file_name, false, false ) ) {
		locate_template( $old_file_name, true, false );
	} else {
		get_template_part( 'template-parts/breadcrumb' );
	}
	do_action( 'lightning_breadcrumb_after' );

}
?>

<div class="<?php lightning_the_class_name( 'siteContent' ); ?>">
	<?php do_action( 'lightning_siteContent_prepend' ); ?>
	<div class="container">
		<?php do_action( 'lightning_siteContent_container_prepend' ); ?>
		<div class="row">
			<div class="<?php lightning_the_class_name( 'mainSection' ); ?>" id="main" role="main">
				<?php
				do_action( 'lightning_mainSection_prepend' );
				// Add class only Here.
				$article_outer_class = '';
				$article_outer_class = apply_filters( 'lightning_article_outer_class', $article_outer_class );
				?>

				<div id="post-<?php the_ID(); ?>" <?php post_class( $article_outer_class ); ?>>
					<?php do_action( 'lightning_entry_body_before' ); ?>
					<div class="entry-body">
						<?php woocommerce_content(); ?>
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

					/*
					Woocommerce_after_main_content hook.
					@hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
					do_action( 'woocommerce_after_main_content' );
					*/
					?>
				</div>

				<?php do_action( 'lightning_mainSection_append' ); ?>
			</div><!-- [ /.mainSection ] -->

			<?php if ( lightning_is_subsection_display() ) { ?>
				<div class="<?php lightning_the_class_name( 'sideSection' ); ?>">
					<?php
					/**
					 * Woocommerce_sidebar hook.
					 *
					 * @hooked woocommerce_get_sidebar - 10
					 */
					do_action( 'woocommerce_sidebar' );
					get_sidebar( get_post_type() );
					?>
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
