<?php
/**
 * VK Color Palette Manager Setting.
 *
 * @package vektor-inc/lightning
 * @since 14.11.0
 */
// /vendor/vektor-inc/vk-color-palette-manager/src/
use VektorInc\VK_Color_Palette_Manager\VkColorPaletteManager;

$vk_color_palette_manager = new VkColorPaletteManager();
/**
 * Lightning Color Palette Theme
 *
 * @since 14.11.0
 *
 */
function lightning_color_palette_theme() {
	$options         = lightning_get_theme_options();
	$color_key       = $options['color_key'];
	$vk_helpers      = new VK_Helpers();
	$color_key_dark  = $vk_helpers->color_auto_modifi( $color_key, 0.8 );
	$color_key_vivid = $vk_helpers->color_auto_modifi( $color_key, 1.1 );
	$colors          = array(
		array(
			'name'  => __( 'Key color', 'lightning' ),
			'slug'  => 'vk-color-primary',
			'color' => $color_key,
		),
		array(
			'name'  => __( 'Key color (dark)', 'lightning' ),
			'slug'  => 'vk-color-primary-dark',
			'color' => $color_key_dark,
		),
		array(
			'name'  => __( 'Key color (vivid)', 'lightning' ),
			'slug'  => 'vk-color-primary-vivid',
			'color' => $color_key_vivid,
		),
	);
	add_theme_support( 'editor-color-palette', $colors );
}
add_action( 'after_setup_theme', 'lightning_color_palette_theme' );
