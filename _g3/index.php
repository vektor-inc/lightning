<?php 
lightning_get_template_part( 'header' );

do_action( 'lightning_header_before' );

lightning_get_template_part( 'template-parts/site-header' );

do_action( 'lightning_header_after' );

if ( lightning_is_page_header() ){
    lightning_get_template_part( 'template-parts/page-header' );
}

do_action( 'lightning_breadcrumb_before' );

// if ( lightning_is_breadcrumb() ){
// 	lightning_get_template_part( 'template-parts/breadcrumb' );
// }

VK_Breadcrumb::the_breadcrumb();

do_action( 'lightning_breadcrumb_after' );