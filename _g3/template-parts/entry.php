<?php if ( is_page() ) {
	$tag = 'div';
} else {
	$tag = 'article';
}
?>
<<?php echo $tag; ?> id="post-<?php the_ID(); ?>" <?php post_class( apply_filters( 'lightning_article_outer_class', 'entry entry-full' ) ); ?>>

	<?php
	// check single or loop that true
	$is_entry_header_display = false;// is_page()
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
			/*
			Category and tax data
			/*-------------------------------------------*/
			$args          = array(
				'template'      => __( '<dl><dt>%s</dt><dd>%l</dd></dl>', 'lightning' ),
				'term_template' => '<a href="%1$s">%2$s</a>',
			);
			$taxonomies    = VK_Helpers::get_display_taxonomies( get_the_ID(), $args );
			$taxnomiesHtml = '';

			if ( $taxonomies ) :
				?>

				<div class="<?php lightning_the_class_name( 'entry-footer' ); ?>">

					<?php
					foreach ( $taxonomies as $key => $value ) {
						$taxnomiesHtml .= '<div class="entry-meta-data-list">' . $value . '</div>';
					} // foreach

					$taxnomiesHtml = apply_filters( 'lightning_taxnomiesHtml', $taxnomiesHtml );
					echo $taxnomiesHtml;

					// tag list
					$tags_list = get_the_tag_list();
					if ( $tags_list ) {
						?>
						<div class="entry-meta-data-list">
							<dl>
							<dt><?php _e( 'Tags', 'lightning' ); ?></dt>
							<dd class="tagcloud"><?php echo $tags_list; ?></dd>
							</dl>
						</div><!-- [ /.entry-tag ] -->
					<?php } // if ( $tags_list ) { ?>

				</div><!-- [ /.entry-footer ] -->

		<?php endif;  // if ($taxonomies) ?>
	
	<?php endif; ?>

</<?php echo $tag; ?>><!-- [ /#post-<?php the_ID(); ?> ] -->
