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

/*
  Deactive Lightning Advanced Unit
/*-------------------------------------------*/
add_action( 'init', 'lightning_deactive_adv_unit' );
function lightning_deactive_adv_unit() {
	$plugin_path = 'lightning-advanced-unit/lightning_advanced_unit.php';
	lightning_deactivate_plugin( $plugin_path );
}
function lightning_deactivate_plugin( $plugin_path ) {
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( is_plugin_active( $plugin_path ) ) {
		$active_plugins = get_option( 'active_plugins' );
		// delete item
		$active_plugins = array_diff( $active_plugins, array( $plugin_path ) );
		// re index
		$active_plugins = array_values( $active_plugins );
		update_option( 'active_plugins', $active_plugins );
	}
}