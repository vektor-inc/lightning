<?php
add_action( 'after_setup_theme', 'lightning_options_compatible' );
/**
 * This is converter that old options to new options value
 * This function is also used in test-lightning.php
 *
 * @return void
 */
function lightning_options_compatible() {
	$options = get_option( 'lightning_theme_options' );
	if ( isset( $options['top_sidebar_hidden'] ) ) {
		if ( $options['top_sidebar_hidden'] ) {
			if ( isset( $options['layout']['front-page'] ) && $options['layout']['front-page'] === 'col-two' ) {

			} else {
				$options['layout']['front-page'] = 'col-one-no-subsection';
				update_option( 'lightning_theme_options', $options );
			}
		}
		unset( $options['top_sidebar_hidden'] );
	}
}


/*
  Deal with typo name action
/*-------------------------------------------*/
add_action( 'lightning_entry_body_after', 'lightning_ligthning_entry_body_after' );
function lightning_ligthning_entry_body_after() {
	do_action( 'ligthning_entry_body_after' );
}
add_action( 'lightning_entry_body_before', 'lightning_ligthning_entry_body_before' );
function lightning_ligthning_entry_body_before() {
	do_action( 'ligthning_entry_body_before' );
}
