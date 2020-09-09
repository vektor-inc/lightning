<?php
// add_action( 'after_setup_theme', 'lightning_options_compatible' );
/**
 * This is converter that old options to new options value
 * This function is also used in test-lightning.php
 *
 * @return void
 */
function lightning_options_compatible() {
	$options = get_option( 'lightning_theme_options' );
	global $wp_query;

	$additional_post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'names'
	);

	$archive_post_types = array( 'post' ) + $additional_post_types;

	if ( isset( $options['top_sidebar_hidden'] ) ) {
		if ( $options['top_sidebar_hidden'] ) {
			if ( isset( $options['layout']['front-page'] ) && $options['layout']['front-page'] === 'col-two' ) {

			} else {
				$options['layout']['front-page'] = 'col-one-no-subsection';
			}
		}
		$options['top_sidebar_hidden'] = null;
		update_option( 'lightning_theme_options', $options );
	}
	if ( isset( $options['layout']['archive'] ) ) {
		foreach ( $archive_post_types as $archive_post_type ) {
			// Old parameter exist && not default
			if ( $options['layout']['archive'] && $options['layout']['archive'] !== 'default' ){
				// New parameter is not exist.
				if ( empty ( $options['layout'][ 'archive-' . $archive_post_type ] ) ){
					$options['layout'][ 'archive-' . $archive_post_type ] = $options['layout']['archive'];
				}
			}
		}
		$options['layout']['archive'] = null;
		update_option( 'lightning_theme_options', $options );
		
	}
	if ( isset( $options['layout']['single'] ) && $options['layout']['single'] != 'default' ) {
		foreach ( $archive_post_types as $archive_post_type ) {
			$options['layout'][ 'single-' . $archive_post_type ] = $options['layout']['single'];
		}
		$options['layout']['single'] = null;
		update_option( 'lightning_theme_options', $options );
		
	}
	if ( isset( $options['layout']['page'] ) && $options['layout']['page'] != 'default' ) {
		$options['layout']['single-page'] = $options['layout']['page'];
		$options['layout']['page'] = null;
		update_option( 'lightning_theme_options', $options );
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

/*
  Deactive Lightning Advanced Unit
/*-------------------------------------------*/
add_action( 'init', 'lightning_deactive_adv_unit' );
function lightning_deactive_adv_unit() {
	$plugin_path = 'lightning-advanced-unit/lightning_advanced_unit.php';
	VK_Helpers::deactivate_plugin( $plugin_path );
}
