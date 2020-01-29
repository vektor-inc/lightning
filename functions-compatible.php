<?php
$options = get_option( 'lightning_theme_options' );
if ( isset( $options['top_sidebar_hidden'] ) ) {
	if ( $options['top_sidebar_hidden'] ) {
		$options['layout']['front-page'] = 'col-one_side-hide';
		update_option( 'lightning_theme_options', $options );
	}
	unset( $options['top_sidebar_hidden'] );
}

/*-------------------------------------------*/
/*	Deal with typo name action
/*-------------------------------------------*/
add_action( 'lightning_entry_body_after', 'lightning_ligthning_entry_body_after' );
function lightning_ligthning_entry_body_after() {
	do_action( 'ligthning_entry_body_after' );
}
add_action( 'lightning_entry_body_before', 'lightning_ligthning_entry_body_before');
function lightning_ligthning_entry_body_before() {
	do_action( 'ligthning_entry_body_before' );
}
