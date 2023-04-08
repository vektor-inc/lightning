<?php
if ( apply_filters( 'is_lightning_extend_single', false ) ) :
	do_action( 'lightning_extend_single' );
else :
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();

			$template = 'template-parts/post/article-' . esc_attr( $post->post_name ) . '.php';
			$return   = locate_template( $template );
			if ( $return && $post->post_name != get_post_type() ) {
				locate_template( $template, true );
			} else {
				get_template_part( 'template-parts/post/article', get_post_type() );
			}

		endwhile;
	endif; // if ( have_posts() ) :
endif; // if ( apply_filters( 'is_lightning_extend_single', false ) ) :
?>

<?php
if ( is_single() ) {
	get_template_part( 'template-parts/post/next-prev', get_post_type() );
}

