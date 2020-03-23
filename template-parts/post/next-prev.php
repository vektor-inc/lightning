<?php
global $bootstrap;
$in_same_term   = apply_filters( 'lightning_prev_next_post_in_same_term', false );
$excluded_terms = apply_filters( 'lightning_prev_next_post_excluded_terms', '' );
$taxonomy       = apply_filters( 'lightning_prev_next_post_taxonomy', 'category' );
if ( $bootstrap == '3' ) { ?>
	<nav>
		<ul class="pager">
		<li class="previous"><?php previous_post_link( '%link', '%title', $in_same_term, $excluded_terms, $taxonomy ); ?></li>
		<li class="next"><?php next_post_link( '%link', '%title', $in_same_term, $excluded_terms, $taxonomy ); ?></li>
		</ul>
	</nav>
<?php } ?>

<?php
if ( $bootstrap == '4' ) {
	$post_previous = get_previous_post( $in_same_term, $excluded_terms, $taxonomy );
	$post_next     = get_next_post( $in_same_term, $excluded_terms, $taxonomy );
	if ( $post_previous || $post_next ) {
		$options = array(
			'layout'                     => 'card-horizontal',
			'display_image'              => true,
			'display_image_overlay_term' => true,
			'display_excerpt'            => false,
			'display_date'               => true,
			'display_btn'                => false,
			'image_default_url'          => get_template_directory_uri() . '/assets/images/no-image.png',
			'overlay'                    => '',
			'body_prepend'               => '',
			'body_append'                => '',
		);
		?>

	<div class="vk_posts postNextPrev">

		<?php
		if ( $post_previous ) {
			$options['body_prepend'] = '<p class="postNextPrev_label">' . __( 'Previous article', 'lightning' ) . '</p>';
			$options['class_outer']  = 'card-sm vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-md-6';
			$options                 = apply_filters( 'lightning_next_prev_options', $options );
			VK_Component_Posts::the_view( $post_previous, $options );
			// get_template_part( 'module_loop_post_card' );
		} else {
			echo '<div class="vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-md-6"></div>';
		} // if ( $post_previous ) {
		?>

		<?php
		if ( $post_next ) {
			$options['body_prepend'] = '<p class="postNextPrev_label">' . __( 'Next article', 'lightning' ) . '</p>';
			$options['class_outer']  = 'card-sm vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-md-6 card-horizontal-reverse postNextPrev_next';
			$options                 = apply_filters( 'lightning_next_prev_options', $options );
			VK_Component_Posts::the_view( $post_next, $options );
		} else {
			echo '<div class="vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-md-6"></div>';
		} // if ( $post_next ) {
		?>

		</div>
		<?php
	} // if ( $post_previous || $post_next ) {
} // if ( $bootstrap == '4' ) {
?>
