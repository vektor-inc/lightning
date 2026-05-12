<?php
/**
 * Snow Monkey Forms Extension
 *
 * @package         Lightning
 */

/*
	CSS読み込み
/*-------------------------------------------*/
function lightning_snow_monkey_forms_load_css() {
	wp_enqueue_style( 'lightning-snow-monkey-forms-style', get_template_directory_uri() . '/plugin-support/snow-monkey-forms/css/style.css', array(), LIGHTNING_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'lightning_snow_monkey_forms_load_css' );
