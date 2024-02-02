<?php
/**
 * Singular entry template
 *
 * @package lightning
 */

if ( is_page() ) {
	$entry_tag = 'div';
} else {
	$entry_tag = 'article';
}
?>
<<?php echo esc_attr( $entry_tag ); ?> id="post-<?php the_ID(); ?>" <?php post_class( apply_filters( 'lightning_article_outer_class', 'entry entry-full' ) ); ?>>

	<?php
	// check single or loop that true.
	$is_entry_header_display = false; // is_page() and so on .
	if ( is_single() || is_archive() ) {
		$is_entry_header_display = apply_filters( 'lightning_is_entry_header', true );
	}
	?>

	<?php if ( $is_entry_header_display ) : ?>

		<header class="<?php lightning_the_class_name( 'entry-header' ); ?>">
			<h1 class="entry-title">
				<?php if ( is_single() ) : ?>
					<?php the_title(); ?>
				<?php else : ?>
					<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
					</a>
				<?php endif; ?>
			</h1>
			<?php lightning_the_entry_meta(); ?>
		</header>

	<?php endif; ?>

	<?php do_action( 'lightning_entry_body_before' ); ?>

	<div class="<?php lightning_the_class_name( 'entry-body' ); ?>">
		<?php do_action( 'lightning_entry_body_prepend' ); ?>
		<?php the_content(); ?>
		<?php do_action( 'lightning_entry_body_apppend' ); ?>
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

	<?php do_action( 'lightning_entry_footer_before' ); ?>

	<?php if ( apply_filters( 'lightning_is_entry_footer', true ) ) : ?>

		<?php
			/**********************************************
			 * Category and tax data
			 */
			$args           = array(
				// translators: taxonomy name.
				'template'      => __( '<dl><dt>%s</dt><dd>%l</dd></dl>', 'lightning' ), // phpcs:ignore
				'term_template' => '<a href="%1$s">%2$s</a>',
			);
			$taxonomies     = VK_Helpers::get_display_taxonomies( get_the_ID(), $args );
			$taxnomies_html = '';

			if ( $taxonomies ) :
				?>

				<div class="<?php lightning_the_class_name( 'entry-footer' ); ?>">

					<?php
					foreach ( $taxonomies as $key => $value ) {
						$taxnomies_html .= '<div class="entry-meta-data-list entry-meta-data-list--' . $key . '">' . $value . '</div>';
					} // foreach

					$taxnomies_html = apply_filters( 'lightning_taxnomiesHtml', $taxnomies_html ); // phpcs:ignore
					echo wp_kses_post( $taxnomies_html );

					// tag list.
					$tags_list = get_the_tag_list();
					if ( $tags_list ) {
						?>
						<div class="entry-meta-data-list entry-meta-data-list--post_tag">
							<dl>
							<dt><?php esc_html_e( 'Tags', 'lightning' ); ?></dt>
							<dd class="tagcloud"><?php echo wp_kses_post( $tags_list ); ?></dd>
							</dl>
						</div><!-- [ /.entry-tag ] -->
					<?php } ?>
					<?php do_action( 'lightning_entry_footer_append' ); ?>
				</div><!-- [ /.entry-footer ] -->

		<?php endif; ?>

	<?php endif; ?>

</<?php echo esc_attr( $entry_tag ); ?>><!-- [ /#post-<?php the_ID(); ?> ] -->
