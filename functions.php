<?php

$current_skin = get_option( 'lightning_design_skin' );
if ( $current_skin === 'origin3' ){
	require get_parent_theme_file_path( '/_g3/functions.php' );
	return;
} else {
	require get_parent_theme_file_path( '/functions_g2.php' );
	return;
}
