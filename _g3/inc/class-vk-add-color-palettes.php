<?php
/**
 * VK add color palettes
 *
 * @package vk-add-color-palettes
 */

/**
 * VK_Add_Color_Palettes
 */
class VK_Add_Color_Palettes {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_filter( 'block_editor_settings_all', array( __CLASS__, 'additional_color_palette' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_color_palettet_css' ), 11 );
		// 11 指定が無いと先に読み込んでしまって効かない
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'add_color_palettet_css_to_editor' ), 11 );
	}

	/**
	 * Add color palettes
	 *
	 * @param array $editor_settings : editor_settings.
	 * @param array $block_editor_context : block_editor_context.
	 * @return array $editor_settings :  editor_settings.
	 */
	public static function additional_color_palette( $editor_settings, $block_editor_context ) {
		$options         = lightning_get_theme_options();
		$color_key       = ! empty( $options['color_key'] ) ? esc_html( $options['color_key'] ) : '#337ab7';
		$vk_helpers      = new VK_Helpers();
		$color_key_dark  = $vk_helpers->color_auto_modifi( $color_key, 0.8 );
		$color_key_vivid = $vk_helpers->color_auto_modifi( $color_key, 1.1 );
		$add_color       = array(
			array(
				'name'  => __( 'Primary', 'lightning' ),
				'slug'  => 'vk-primary',
				'color' => $color_key,
			),
			array(
				'name'  => __( 'Primary dark', 'lightning' ),
				'slug'  => 'vk-primary-dark',
				'color' => $color_key_dark,
			),
			array(
				'name'  => __( 'Primary vivid', 'lightning' ),
				'slug'  => 'vk-primary-vivid',
				'color' => $color_key_vivid,
			),
		);

		$editor_settings['__experimentalFeatures']['color']['palette']['core'] = array_merge(
			$editor_settings['__experimentalFeatures']['color']['palette']['core'],
			$add_color
		);
		return $editor_settings;
	}

	/**
	 * Create color palettes css
	 *
	 * @return string
	 */
	public static function inline_css() {
		$dynamic_css = '
        /* VK Color Palettes */
        .has-vk-primary-color {
            color:var( --vk-color-primary);
        }
        .has-vk-primary-dark-color {
            color:var( --vk-color-primary-dark);
        }
        .has-vk-primary-vivid-color {
            color:var( --vk-color-primary-vivid);
        }
        .has-vk-primary-background-color {
            background-color:var( --vk-color-primary);
        }
        .has-vk-primary-dark-background-color {
            background-color:var( --vk-color-primary-dark);
        }
        .has-vk-primary-vivid-background-color {
            background-color:var( --vk-color-primary-vivid);
        }
        ';
		// Delete before after space.
		$dynamic_css = trim( $dynamic_css );
		// Convert tab and br to space.
		$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
		// Change multiple spaces to single space.
		$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
		return $dynamic_css;
	}

	/**
	 * Add front css
	 *
	 * @return void
	 */
	public static function add_color_palettet_css() {
		$dynamic_css = self::inline_css();
		wp_add_inline_style( 'wp-block-library', $dynamic_css );
	}

	/**
	 * Add editor css
	 *
	 * @return void
	 */
	public static function add_color_palettet_css_to_editor() {
		$dynamic_css = self::inline_css();
		wp_add_inline_style( 'wp-edit-blocks', $dynamic_css, 11 );
	}

}

$vk_add_color_palettes = new VK_Add_Color_Palettes();
