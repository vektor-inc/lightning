<?php
// add_action( 'after_setup_theme', 'lightning_options_compatible' );

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