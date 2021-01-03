<?php
global $bootstrap;
$in_same_term   = apply_filters( 'lightning_prev_next_post_in_same_term', false );
$excluded_terms = apply_filters( 'lightning_prev_next_post_excluded_terms', '' );
$taxonomy       = apply_filters( 'lightning_prev_next_post_taxonomy', 'category' );
?>

<?php
$post_previous = get_previous_post( $in_same_term, $excluded_terms, $taxonomy );
$post_next     = get_next_post( $in_same_term, $excluded_terms, $taxonomy );
if ( $post_previous || $post_next ) {
	$options = array(
		'layout'                     => 'card-intext',
		'display_image'              => true,
		'display_image_overlay_term' => false,
		'display_excerpt'            => false,
		'display_title'               => false,
		'display_date'               => true,
		'display_btn'                => false,
		'image_default_url'          => get_template_directory_uri() . '/assets/images/no-image.png',
		'overlay'                    => '',
		'body_prepend'               => '',
		'body_append'                => '',
	);
	?>

<div class="vk_posts next-prev">

	<?php
	if ( $post_previous ) {
		
		$options['overlay'] = '<span class="vk_post_imgOuter_singleTermLabel">' . __( 'Previous article', 'lightning' ) . '</span>';
		$options['class_outer']  = 'vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-md-6 next-prev-prev';
		$options                 = apply_filters( 'lightning_next_prev_options', $options );
		$options                 = apply_filters( 'lightning_next_prev_options_prev', $options );
		$post                    = $post_previous;
		/*
		Reason of $post = $post_previous;
		To use WordPress normal template-tag filter ( Like a 'post_thumbnail_html' filter )
			*/
		VK_Component_Posts::the_view( $post, $options );
		wp_reset_postdata();
	} else {
		echo '<div class="vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-md-6"></div>';
	} // if ( $post_previous ) {
	?>

	<?php
	if ( $post_next ) {
		$options['overlay'] = '<span class="vk_post_imgOuter_singleTermLabel">' . __( 'Next article', 'lightning' ) . '</span>';
		$options['class_outer']  = 'vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-md-6 next-prev-next';
		$options                 = apply_filters( 'lightning_next_prev_options', $options );
		$options                 = apply_filters( 'lightning_next_prev_options_next', $options );
		$post                    = $post_next;
		VK_Component_Posts::the_view( $post, $options );
		wp_reset_postdata();
	} else {
		echo '<div class="vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-md-6"></div>';
	} // if ( $post_next ) {
	?>

	</div>
	<?php
} // if ( $post_previous || $post_next ) {
?>
