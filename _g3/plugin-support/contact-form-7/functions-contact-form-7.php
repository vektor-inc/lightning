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
	wp_enqueue_style( 'lightning_load_cf7_editor_css', get_template_directory_uri() . '/plugin-support/contact-form-7/css/editor-style.css', array(), LIGHTNING_THEME_VERSION );
}
// ブロックエディターでも同じCSSを適用する。
add_action( 'enqueue_block_editor_assets', 'lightning_load_cf7_editor_css' );
