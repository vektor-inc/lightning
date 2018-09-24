<?php get_header(); ?>

<?php get_template_part( 'module_pageTit' ); ?>
<?php get_template_part( 'module_panList' ); ?>

<div class="section siteContent">
<div class="container">
<div class="row">

<div class="col-md-8 mainSection" id="main" role="main">

	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php do_action( 'ligthning_entry_body_before' ); ?>
	<div class="entry-body">
	<?php woocommerce_content(); ?>
	</div>
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
	</div><!-- [ /#post-<?php the_ID(); ?> ] -->



</div><!-- [ /.mainSection ] -->

<div class="col-md-3 col-md-offset-1 subSection sideSection">
	<?php
		/**
		 * woocommerce_sidebar hook.
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		do_action( 'woocommerce_sidebar' );
	?>
<?php get_sidebar( get_post_type() ); ?>
</div><!-- [ /.subSection ] -->

</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
