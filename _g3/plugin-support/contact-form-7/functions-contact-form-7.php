<?php
/**
 * Contact Form 7 Extension
 *
 * @package         Lightning
 *
 */

/*
  CSS読み込み
/*-------------------------------------------*/
function lightning_load_cf7_editor_css() {
	// enqueue_block_assets はフロントでも動くため、ブロックエディタでのみ読み込む.
	if ( ! is_admin() ) {
		return;
	}
	if ( function_exists( 'wp_should_load_block_editor_scripts_and_styles' ) && ! wp_should_load_block_editor_scripts_and_styles() ) {
		return;
	}
	wp_enqueue_style( 'lightning_load_cf7_editor_css', get_template_directory_uri() . '/plugin-support/contact-form-7/css/editor-style.css', array(), LIGHTNING_THEME_VERSION );
}
// ブロックエディターでも同じCSSを適用する。
add_action( 'enqueue_block_assets', 'lightning_load_cf7_editor_css' );
