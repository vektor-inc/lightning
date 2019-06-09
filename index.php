<?php get_header(); ?>

<?php get_template_part( 'module_pageTit' ); ?>
<?php get_template_part( 'module_panList' ); ?>

<div class="section siteContent">
<div class="container">
<div class="row">

<div class="col-md-8 mainSection" id="main" role="main">

	<?php
	/*-------------------------------------------*/
	/*  Archive title
	/*-------------------------------------------*/
	$archiveTitle_html = '';
	$page_for_posts    = lightning_get_page_for_posts();
	// Use post top page（ Archive title wrap to div ）.
	if ( $page_for_posts['post_top_use'] || get_post_type() !== 'post' ) {
		if ( is_year() || is_month() || is_day() || is_tag() || is_author() || is_tax() || is_category() ) {
			$archiveTitle      = get_the_archive_title();
			$archiveTitle_html = '<header class="archive-header"><h1>' . $archiveTitle . '</h1></header>';
		}
	}
	echo wp_kses_post( apply_filters( 'lightning_mainSection_archiveTitle', $archiveTitle_html ) );

	/*-------------------------------------------*/
	/*  Archive description
	/*-------------------------------------------*/
	$archiveDescription_html = '';
	if ( is_category() || is_tax() || is_tag() ) {
		$archiveDescription = term_description();
		$page_number        = get_query_var( 'paged', 0 );
		if ( ! empty( $archiveDescription ) && $page_number == 0 ) {
			$archiveDescription_html = '<div class="archive-meta">' . $archiveDescription . '</div>';
		}
	}
	echo wp_kses_post( apply_filters( 'lightning_mainSection_archiveDescription', $archiveDescription_html ) );

	$postType = lightning_get_post_type();

	do_action( 'lightning_loop_before' );
?>

<div class="postList">

<?php if ( have_posts() ) : ?>

	<?php if ( apply_filters( 'is_lightning_extend_loop', false ) ) : ?>

	<?php do_action( 'lightning_extend_loop' ); ?>

	<?php elseif ( file_exists( get_stylesheet_directory() . '/module_loop_' . $postType['slug'] . '.php' ) && $postType != 'post' ) : ?>

	<?php
	while ( have_posts() ) :
		the_post();
?>
	<?php get_template_part( 'module_loop_' . $postType['slug'] ); ?>
	<?php endwhile; ?>

	<?php else : ?>

	<?php
	while ( have_posts() ) :
		the_post();
?>
	<?php get_template_part( 'module_loop_post' ); ?>
	<?php endwhile; ?>

	<?php endif; // loop() ?>

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

	<?php else : // hove_posts() ?>

  <div class="well"><p><?php echo wp_kses_post( apply_filters( 'lightning_no_posts_text', __( 'No posts.', 'lightning' ) ) ); ?></p></div>

<?php endif; // have_post() ?>

</div><!-- [ /.postList ] -->

<?php do_action( 'lightning_loop_after' ); ?>

</div><!-- [ /.mainSection ] -->

<div class="col-md-3 col-md-offset-1 subSection sideSection">
<?php get_sidebar( get_post_type() ); ?>
</div><!-- [ /.subSection ] -->

</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
	<?php get_footer(); ?>
