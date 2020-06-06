<?php
/**
 * Loop Template of Lightning
 *
 * @package Lightning
 */

global $lightning_theme_options;
?>

<div class="entry-meta">

	<?php if ( 'product' !== get_post_type() ) : ?>

		<span class="published entry-meta_items"><?php the_date(); ?></span>
		<?php
		// Post update.
		$meta_hidden_update = ( isset( $lightning_theme_options['postUpdate_hidden'] ) && $lightning_theme_options['postUpdate_hidden'] ) ? ' entry-meta_hidden' : '';
		?>

		<span class="entry-meta_items entry-meta_updated<?php echo esc_attr( $meta_hidden_update ); ?>">/ <?php esc_html_e( 'Last updated', 'lightning' ); ?> : <span class="updated"><?php the_modified_date( '' ); ?></span></span>

		<?php
		// Post author.

		// For post type where author does not exist.
		$author = get_the_author();
		if ( $author ) :
			$meta_hidden_author = ( isset( $lightning_theme_options['postAuthor_hidden'] ) && $lightning_theme_options['postAuthor_hidden'] ) ? ' entry-meta_hidden' : '';
			?>

			<span class="vcard author entry-meta_items entry-meta_items_author<?php echo esc_attr( $meta_hidden_author ); ?>"><span class="fn"><?php echo esc_html( $author ); ?></span></span>

			<?php
		endif;
	endif;
	$taxonomies = get_the_taxonomies();
	if ( $taxonomies ) {
		/*
			Get $the_taxonomy name
			$the_taxonomy   = key( $taxonomies );
			To avoid WooCommerce default tax
		*/
		foreach ( $taxonomies as $key => $value ) {
			if ( 'product_type' !== $key ) {
				$the_taxonomy = $key;
				break;
			}
		}

		$terms      = get_the_terms( get_the_ID(), $the_taxonomy );
		$term_url   = esc_url( get_term_link( $terms[0]->term_id, $the_taxonomy ) );
		$term_name  = esc_html( $terms[0]->name );
		$term_color = '';
		if ( class_exists( 'Vk_term_color' ) ) {
				$term_color = Vk_term_color::get_term_color( $terms[0]->term_id );
				$term_color = ( $term_color ) ? ' style="background-color:' . $term_color . ';border:none;"' : '';
		}
		echo '<span class="entry-meta_items entry-meta_items_term"><a href="' . esc_url( $term_url ) . '" class="btn btn-xs btn-primary entry-meta_items_term_button"' . esc_attr( $term_color ) . '>' . esc_html( $term_name ) . '</a></span>';
	}
	?>
</div>
