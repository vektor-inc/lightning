<?php

// Excrude to in case of filter search
if ( ! is_search() ) {

	/*
	Archive title
	/*-------------------------------------------*/
	$archive_header_html = '';
	$post_top_info       = VK_Helpers::get_post_top_info();
	// Use post top page（ Archive title wrap to div ）.
	if ( $post_top_info['use'] || get_post_type() !== 'post' ) {
		if ( is_year() || is_month() || is_day() || is_tag() || is_tax() || is_category() ) {
			$archive_title       = get_the_archive_title();
			$archive_header_html = '<header class="archive-header"><h1 class="archive-header-title">' . $archive_title . '</h1></header>';
			echo wp_kses_post( apply_filters( 'lightning_archive-header', $archive_header_html ) );
		}
	}

	/*
	Archive description
	/*-------------------------------------------*/
	$archive_description_html = '';
	if ( is_category() || is_tax() || is_tag() ) {
		$archive_description = term_description();
		$page_number         = get_query_var( 'paged', 0 );
		if ( ! empty( $archive_description ) && $page_number == 0 ) {
			$archive_description_html = '<div class="archive-description">' . $archive_description . '</div>';
			echo wp_kses_post( apply_filters( 'lightning_archive_description', $archive_description_html ) );
		}
	}
} // if ( ! is_search() ) {

$post_type_info = VK_Helpers::get_post_type_info();

do_action( 'lightning_loop_before' );
?>

<?php if ( have_posts() ) : ?>

	<?php if ( apply_filters( 'lightning_is_extend_loop', false ) ) { ?>

		<?php do_action( 'lightning_extend_loop' ); ?>

<?php } else { ?>

	<div class="<?php lightning_the_class_name( 'post-list' ); ?> vk_posts vk_posts-mainSection">

		<?php
		global $lightning_loop_item_count;
		$lightning_loop_item_count = 0;

		while ( have_posts() ) {
			the_post();

			lightning_get_template_part( 'template-parts/loop-item', $post_type_info['slug'] );

			$lightning_loop_item_count++;
			do_action( 'lightning_loop_item_after' );

		} // while ( have_posts() ) {
		?>

	</div><!-- [ /.post-list ] -->

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

<div class="main-section-no-posts"><p><?php echo wp_kses_post( apply_filters( 'lightning_no_posts_text', __( 'No posts.', 'lightning' ) ) ); ?></p></div>

<?php endif; // have_post() ?>

<?php do_action( 'lightning_loop_after' ); ?>
