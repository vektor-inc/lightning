<?php
/**
 * One Column Single Template of Lightning
 * Template Name: One column Single
 * Template Post type: post, page
 *
 * @package Lightning
 */

get_header();

// Dealing with old files.
// Actually, it's ok to only use get_template_part().
/**
 * Page Header
 */
$old_file_name[] = 'module_pageTit.php';
if ( locate_template( $old_file_name, false, false ) ) {
	locate_template( $old_file_name, true, false );
} else {
	get_template_part( 'template-parts/page-header' );
}
/**
 * BreadCrumb
 */
$old_file_name[] = 'module_panList.php';
if ( locate_template( $old_file_name, false, false ) ) {
	locate_template( $old_file_name, true, false );
} else {
	get_template_part( 'template-parts/breadcrumb' );
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

				if ( apply_filters( 'is_lightning_extend_single', false ) ) :
					do_action( 'lightning_extend_single' );
				else :
					if ( have_posts() ) :
						while ( have_posts() ) :
							the_post();
							?>
							<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
								<header class="<?php lightning_the_class_name( 'entry-header' ); ?>">
									<?php get_template_part( 'template-parts/post/meta' ); ?>
									<h1 class="entry-title"><?php the_title(); ?></h1>
								</header>

								<?php do_action( 'lightning_entry_body_before' ); ?>

								<div class="<?php lightning_the_class_name( 'entry-body' ); ?>">
									<?php the_content(); ?>
								</div>

								<?php do_action( 'lightning_entry_body_after' ); ?>

								<div class="<?php lightning_the_class_name( 'entry-footer' ); ?>">

									<?php
									$args = array(
										'before'      => '<nav class="page-link"><dl><dt>Pages :</dt><dd>',
										'after'       => '</dd></dl></nav>',
										'link_before' => '<span class="page-numbers">',
										'link_after'  => '</span>',
										'echo'        => 1,
									);
									wp_link_pages( $args );

									/**
									 *  Category and tax data
									 */
									$args          = array(
										// translators:.
										'template'      => __( '<dl><dt>%s</dt><dd>%l</dd></dl>', 'lightning' ),
										'term_template' => '<a href="%1$s">%2$s</a>',
									);
									$taxonomies    = get_the_taxonomies( $post->ID, $args );
									$taxnomiesHtml = '';
									if ( $taxonomies ) {
										foreach ( $taxonomies as $key => $value ) {
											if ( 'post_tag' !== $key ) {
												$taxnomiesHtml .= '<div class="entry-meta-dataList">' . $value . '</div>';
											}
										}
									}
									$taxnomiesHtml = apply_filters( 'lightning_taxnomiesHtml', $taxnomiesHtml );
									echo wp_kses_post( $taxnomiesHtml );

									// tag list.
									$tags_list = get_the_tag_list();
									if ( $tags_list ) {
										?>
										<div class="entry-meta-dataList entry-tag">
											<dl>
												<dt><?php esc_html_e( 'Tags', 'lightning' ); ?></dt>
												<dd class="tagcloud"><?php echo wp_kses_post( $tags_list ); ?></dd>
											</dl>
										</div>
										<?php
									}
									?>

								</div>

								<?php do_action( 'lightning_comment_before' ); ?>
								<?php comments_template( '', true ); ?>
								<?php do_action( 'lightning_comment_after' ); ?>

							</article>

							<?php
						endwhile;
					endif;
				endif;
				get_template_part( 'template-parts/post/next-prev' );
				do_action( 'lightning_mainSection_append' );
				?>
			</div>
		</div>
	</div>
</div>
<?php get_footer(); ?>
