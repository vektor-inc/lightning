<?php
/*
  Load Font
/*-------------------------------------------*/
function lightning_origin3_load_fonts() {
	wp_enqueue_style( 'add_google_fonts_Lato', '//fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap&subset=japanese', false );
	wp_enqueue_style( 'add_google_fonts_noto_sans', '//fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;700&display=swap&subset=japanese', false );
}
add_action( 'wp_footer', 'lightning_origin3_load_fonts' );
