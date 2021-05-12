<?php
/*
  Load Font
/*-------------------------------------------*/
function lightning_origin3_load_fonts() {
	wp_enqueue_style( 'add_google_fonts_noto_sans', 'https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Noto+Sans+JP:wght@400;700&display=swap', false );
}
add_action( 'wp_footer', 'lightning_origin3_load_fonts' );