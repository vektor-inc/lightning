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
 * Add lightning color to palette
 *
 * @since 14.11.0
 *
 * @param array $vcm_add_color_array : vk color palette array.
 * @return array marged color array
 */
function lightning_add_color_palette( $vcm_add_color_array ) {
	$options         = lightning_get_theme_options();
	$color_key       = $options['color_key'];
	$vk_helpers      = new VK_Helpers();
	$color_key_dark  = $vk_helpers->color_auto_modifi( $color_key, 0.8 );
	$color_key_vivid = $vk_helpers->color_auto_modifi( $color_key, 1.1 );
	$add_colors      = array(
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
	return array_merge( $add_colors, $vcm_add_color_array );
}
add_filter( 'vcm_add_color_array', 'lightning_add_color_palette' );
