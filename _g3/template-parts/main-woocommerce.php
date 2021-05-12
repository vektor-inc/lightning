	<?php do_action( 'lightning_entry_body_before' ); ?>
	<div class="<?php lightning_the_class_name( 'entry-body' ); ?>">
		<?php woocommerce_content(); ?>
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
	<?php
		/**
		 * woocommerce_after_main_content hook.
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		// do_action( 'woocommerce_after_main_content' );
	?>