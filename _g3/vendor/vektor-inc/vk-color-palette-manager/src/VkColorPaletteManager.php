<?php //phpcs:ignore
/**
 * VK_Color_Palette_Manager
 *
 * @package vektor-inc/vk-color-palette-manager
 * @license GPL-2.0+
 *
 * @version 0.0.15
 */

namespace VektorInc\VK_Color_Palette_Manager;

use WP_Customize_Color_Control;
use VK_Custom_Html_Control;

/**
 * VK_Color_Palette_Manager
 */
class VkColorPaletteManager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
		add_filter( 'block_editor_settings_all', array( __CLASS__, 'additional_color_palette' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_color_palette_css' ), 11 );
		// 11 指定が無いと先に読み込んでしまって効かない
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'add_color_palette_css_to_editor' ), 11 );
		load_textdomain( 'vk-color-palette-manager', dirname( __FILE__ ) . '/languages/vk-color-palette-manager-' . get_locale() . '.mo' );
	}

	/**
	 * Customizer
	 *
	 * @param object $wp_customize : customize object.
	 */
	public static function customize_register( $wp_customize ) {

		if ( class_exists( 'VK_Custom_Html_Control' ) ) {
			$wp_customize->add_setting(
				'color_palette_title',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				new VK_Custom_Html_Control(
					$wp_customize,
					'color_palette_title',
					array(
						'label'            => '',
						'section'          => 'colors',
						'type'             => 'text',
						'custom_title_sub' => __( 'Color Palette Setting', 'vk-color-palette-manager' ),
						'custom_html'      => __( 'This color is reflected in the block editor\'s color palette.', 'vk-color-palette-manager' ),
						'priority'         => 1000,
					)
				)
			);
		}

		for ( $i = 1; $i <= 5; $i++ ) {
			$wp_customize->add_setting(
				'vk_color_manager_options[color_custom_' . $i . ']',
				array(
					'default'           => '',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_hex_color',
				)
			);
			$label = __( 'Custom color', 'vk-color-palette-manager' ) . ' ' . $i;
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'vk_color_manager_options[color_custom_' . $i . ']',
					array(
						'label'    => $label,
						'section'  => 'colors',
						'settings' => 'vk_color_manager_options[color_custom_' . $i . ']',
						'priority' => 1000,
					)
				)
			);
		}
	}

	/**
	 * Additional color palette array
	 */
	public static function add_color_array() {
		$options_color       = get_option( 'vk_color_manager_options' );
		$vcm_add_color_array = array();
		if ( $options_color ) {
			for ( $i = 1; $i <= 5; $i++ ) {
				if ( ! empty( $options_color[ 'color_custom_' . $i ] ) ) {
					$vcm_add_color_array[] = array(
						'name'  => __( 'Custom color', 'vk-color-palette-manager' ) . ' ' . $i,
						'slug'  => 'vk-color-custom-' . $i,
						'color' => $options_color[ 'color_custom_' . $i ],
					);
				}
			}
		}
		return apply_filters( 'vcm_add_color_array', $vcm_add_color_array );
	}

	/**
	 * Add color palettes
	 *
	 * @param array $editor_settings : editor_settings.
	 * @param array $block_editor_context : block_editor_context.
	 * @return array $editor_settings :  editor_settings.
	 */
	public static function additional_color_palette( $editor_settings, $block_editor_context ) {
		$add_color = self::add_color_array();
		if ( ! empty( $add_color ) ) {
			if ( ! empty( $editor_settings['__experimentalFeatures']['color']['palette']['core'] ) ) {
				$editor_settings['__experimentalFeatures']['color']['palette']['core'] = array_merge(
					$editor_settings['__experimentalFeatures']['color']['palette']['core'],
					$add_color
				);
			} else {
				$editor_settings['__experimentalFeatures']['color']['palette']['core'] = $add_color;
			}
			$editor_settings['colors'] = array_merge(
				$editor_settings['colors'],
				$add_color
			);
		}
		return $editor_settings;
	}

	/**
	 * Create color palettes css
	 *
	 * @return string
	 */
	public static function inline_css() {
		$options_color = get_option( 'vk_color_manager_options' );
		$colors        = self::add_color_array();

		$dynamic_css = '/* VK Color Palettes */';
		foreach ( $colors as $key => $color ) {
			if ( ! empty( $color['color'] ) ) {
				// 色はこのクラスでだけの利用なら直接指定でも良いが、他のクラス名で応用できるように一旦css変数に格納している.
				$dynamic_css .= ':root{ --' . $color['slug'] . ':' . $color['color'] . '}';
				// .has- だけだと負けるので :root は迂闊に消さないように注意
				$dynamic_css .= ':root .has-' . $color['slug'] . '-color { color:var(--' . $color['slug'] . '); }';
				$dynamic_css .= ':root .has-' . $color['slug'] . '-background-color { background-color:var(--' . $color['slug'] . '); }';
			}
		}

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
	public static function add_color_palette_css() {
		$dynamic_css = self::inline_css();
		wp_add_inline_style( 'wp-block-library', $dynamic_css );
	}

	/**
	 * Add editor css
	 *
	 * @return void
	 */
	public static function add_color_palette_css_to_editor() {
		$dynamic_css = self::inline_css();
		wp_add_inline_style( 'wp-edit-blocks', $dynamic_css, 11 );
	}

}
