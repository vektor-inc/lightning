<?php
if ( apply_filters( 'is_lightning_extend_single', false ) ) :
	do_action( 'lightning_extend_single' );
else :
	if ( have_posts() ) :
		while ( have_posts() ) :
            the_post();
				lightning_get_template_part( 'template-parts/article', get_post_type() );
		endwhile;
	endif; // if ( have_posts() ) :
endif; // if ( apply_filters( 'is_lightning_extend_single', false ) ) :

if ( is_single() ) {
	lightning_get_template_part( 'template-parts/next-prev', get_post_type() );
}
?>