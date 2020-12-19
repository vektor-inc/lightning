<?php 
lightning_get_template_part( 'header' );

do_action( 'lightning_header_before' );

lightning_get_template_part( 'template-parts/site-header' );

do_action( 'lightning_header_after' );

lightning_get_template_part( 'template-parts/page-header' );