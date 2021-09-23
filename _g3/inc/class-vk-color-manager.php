<?php
/**
 * VK Color Manager
 *
 * @package vk-color-manager
 */

/**
 * VK_Color_Manager
 */
class VK_Color_Manager {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
		add_filter( 'block_editor_settings_all', array( __CLASS__, 'additional_color_palette' ), 10, 2 );
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_color_palettet_css' ), 11 );
		// 11 指定が無いと先に読み込んでしまって効かない
		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'add_color_palettet_css_to_editor' ), 11 );
	}

	/**
	 * Customizer
	 *
	 * @param object $wp_customize : customize object.
	 */
	public static function customize_register( $wp_customize ) {

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
			$label = __( 'Custom color', 'lightning' ) . ' ' . $i;
			if ( 1 === $i ) {
				$label .= ' ( ' . __( 'Key color', 'lightning' ) . ' )';
			}
			$wp_customize->add_control(
				new WP_Customize_Color_Control(
					$wp_customize,
					'vk_color_manager_options[color_custom_' . $i . ']',
					array(
						'label'    => $label,
						'section'  => 'colors',
						'settings' => 'vk_color_manager_options[color_custom_' . $i . ']',
						'priority' => 600,
					)
				)
			);
		}

	}

	/**
	 * Add color palettes
	 *
	 * @param array $editor_settings : editor_settings.
	 * @param array $block_editor_context : block_editor_context.
	 * @return array $editor_settings :  editor_settings.
	 */
	public static function additional_color_palette( $editor_settings, $block_editor_context ) {
		$color_key       = self::get_color_key();
		$vk_helpers      = new VK_Helpers();
		$color_key_dark  = $vk_helpers->color_auto_modifi( $color_key, 0.8 );
		$color_key_vivid = $vk_helpers->color_auto_modifi( $color_key, 1.1 );
		$add_color       = array(
			array(
				'name'  => __( 'Key color', 'lightning' ),
				'slug'  => 'vk-primary',
				'color' => $color_key,
			),
			array(
				'name'  => __( 'Key color (dark)', 'lightning' ),
				'slug'  => 'vk-primary-dark',
				'color' => $color_key_dark,
			),
			array(
				'name'  => __( 'Key color (vivid)', 'lightning' ),
				'slug'  => 'vk-primary-vivid',
				'color' => $color_key_vivid,
			),
		);

		$options_color = get_option( 'vk_color_manager_options' );
		if ( $options_color ) {
			for ( $i = 2; $i <= 5; $i++ ) {
				if ( ! empty( $options_color[ 'color_custom_' . $i ] ) ) {
					$add_color[] = array(
						'name'  => __( 'Custom color', 'lightning' ) . ' ' . $i,
						'slug'  => 'vk-custom-' . $i,
						'color' => $options_color[ 'color_custom_' . $i ],
					);
				}
			}
		}

		$editor_settings['__experimentalFeatures']['color']['palette']['core'] = array_merge(
			$editor_settings['__experimentalFeatures']['color']['palette']['core'],
			$add_color
		);
		return $editor_settings;
	}

	/**
	 * Get Key Color
	 *
	 * @return string $color_key
	 */
	public static function get_color_key() {
		$options_old   = lightning_get_theme_options();
		$color_key_old = ! empty( $options['color_key'] ) ? esc_html( $options['color_key'] ) : '';

		$options_color = get_option( 'vk_color_manager_options' );
		if ( ! empty( $options_color['color_custom_1'] ) ) {
			$color_key = $options_color['color_custom_1'];
		} elseif ( $color_key_old ) {
			$color_key = $color_key_old;
		} else {
			$color_key = '#337ab7';
		}
		return $color_key;
	}

	/**
	 * Create color palettes css
	 *
	 * @return string
	 */
	public static function inline_css() {
		$options_color = get_option( 'vk_color_manager_options' );
		$color_key     = self::get_color_key();

		$vk_helpers      = new VK_Helpers();
		$color_key_dark  = $vk_helpers->color_auto_modifi( $color_key, 0.8 );
		$color_key_vivid = $vk_helpers->color_auto_modifi( $color_key, 1.1 );

		$dynamic_css  = '
		/* VK Color Palettes */
		:root {
			--vk-color-primary:' . $color_key . ';
			--vk-color-primary-dark:' . $color_key_dark . ';
			--vk-color-primary-vivid:' . $color_key_vivid . ';
		}
		';
		$dynamic_css .= '
        .has-vk-primary-color {
            color:var(--vk-color-primary);
        }
        .has-vk-primary-dark-color {
            color:var(--vk-color-primary-dark);
        }
        .has-vk-primary-vivid-color {
            color:var(--vk-color-primary-vivid);
        }
        .has-vk-primary-background-color {
            background-color:var(--vk-color-primary);
        }
        .has-vk-primary-dark-background-color {
            background-color:var(--vk-color-primary-dark);
        }
        .has-vk-primary-vivid-background-color {
            background-color:var(--vk-color-primary-vivid);
        }
        ';

		if ( $options_color ) {
			for ( $i = 2; $i <= 5; $i++ ) {
				if ( ! empty( $options_color[ 'color_custom_' . $i ] ) ) {
					$dynamic_css .= ':root{
						--vk-color-custom-' . $i . ':' . $options_color[ 'color_custom_' . $i ] . '
					}';
					$dynamic_css .= '
					.has-vk-custom-' . $i . '-color {
						color:var(--vk-color-custom-' . $i . ');
					}
					.has-vk-custom-' . $i . '-background-color {
						background-color:var(--vk-color-custom-' . $i . ');
					}
					';
				}
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
	public static function add_color_palettet_css() {
		$dynamic_css = self::inline_css();
		wp_add_inline_style( 'lightning-design-style', $dynamic_css );
		// wp_add_inline_style( 'wp-block-library', $dynamic_css );
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

$vk_color_manager = new VK_Color_Manager();
