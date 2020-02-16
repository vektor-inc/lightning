<?php get_header(); ?>

<?php
// Dealing with old files.
// Actually, it's ok to only use get_template_part().
/*
 Page Header
/*-------------------------------------------*/
$old_file_name[] = 'module_pageTit.php';
if ( locate_template( $old_file_name, false, false ) ) {
	locate_template( $old_file_name, true, false );
} else {
	get_template_part( 'template-parts/page-header' );
}
/*
 BreadCrumb
/*-------------------------------------------*/
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
<?php do_action( 'lightning_mainSection_prepend' ); ?>

	<?php
	/*
	  Archive title
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

	/*
	  Archive description
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

<div class="<?php lightning_the_class_name( 'postList' ); ?>">

<?php if ( have_posts() ) : ?>

	<?php if ( apply_filters( 'is_lightning_extend_loop', false ) ) { ?>

		<?php do_action( 'lightning_extend_loop' ); ?>

	<?php } else { ?>

		<?php
		/**
		 * Dealing with old files
		 * Actually, it's ok to only use get_template_part().
		 * It is measure for before version 7.0 that loaded module_loop_***.php.
		 */
		$old_file_name[] = 'module_loop_' . $postType['slug'] . '.php';
		$old_file_name[] = 'module_loop_post.php';
		$require_once    = false;

		while ( have_posts() ) {
			the_post();

			if ( locate_template( $old_file_name, false, $require_once ) ) {
				locate_template( $old_file_name, true, $require_once );
			} else {
				get_template_part( 'template-parts/post/loop', $postType['slug'] );
			}
		} // while ( have_posts() ) {
		?>

	<?php } // loop() ?>

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
<?php do_action( 'lightning_mainSection_append' ); ?>
</div><!-- [ /.mainSection ] -->

<?php if ( lightning_is_subsection_display() ){ ?>
	<div class="<?php lightning_the_class_name( 'sideSection' ); ?>">
	<?php get_sidebar( get_post_type() ); ?>
	</div><!-- [ /.subSection ] -->
<?php } ?>


</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
