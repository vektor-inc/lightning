<?php
/**
 * Widget Setting
 *
 * @package VK Footer Customize
 */

if ( ! class_exists( 'Widget_Area_Setting' ) ) {
	/**
	 * Widget_Area_Setting
	 */
	class Widget_Area_Setting {
		/**
		 * Constructor.
		 */
		public function __construct() {
			global $vk_footer_setting_name;
			add_action( 'customize_register', array( __CLASS__, 'resister_customize' ) );
			add_action( 'wp_head', array( __CLASS__, 'enqueue_style' ), 5 );
			add_filter( $vk_footer_setting_name . '_footer_widget_area_count', array( __CLASS__, 'set_footter_widget_area_count' ) );
		}

		/**
		 * Default Options
		 */
		public static function default_options() {
			$default = array(
				'footer_upper_widget_padding_delete' => 'false',
				'footer_widget_area_count'           => 3,
			);
			return $default;
		}

		/**
		 * Customizer.
		 *
		 * @param \WP_Customize_Manager $wp_customize Customizer.
		 */
		public static function resister_customize( $wp_customize ) {
			$default = self::default_options();

			global $vk_footer_selector;
			global $vk_footer_setting_name;
			global $vk_footer_customize_prefix;
			global $vk_footer_customize_priority;
			if ( ! $vk_footer_customize_priority ) {
				$vk_footer_customize_priority = 540;
			}
			$priority = $vk_footer_customize_priority + 2;

			// add section.
			$wp_customize->add_section(
				'vk_footer_option',
				array(
					'title'    => $vk_footer_customize_prefix . __( 'Footer settings', 'vk-footer-customize-textdomain' ),
					'priority' => $vk_footer_customize_priority,
				)
			);

			// Footer Upper Widget Area Heading.
			$wp_customize->add_setting(
				'footer-widget-setting',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				new Custom_Html_Control(
					$wp_customize,
					'footer-widget-setting',
					array(
						'label'            => __( 'Footer Widget Setting', 'vk-footer-customize-textdomain' ),
						'section'          => 'vk_footer_option',
						'type'             => 'text',
						'custom_title_sub' => '',
						'custom_html'      => '',
						'priority'         => $priority,
					)
				)
			);

			// Padding Bottom.
			$wp_customize->add_setting(
				$vk_footer_setting_name . '[footer_upper_widget_padding_delete]',
				array(
					'default'           => 'false',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( 'VK_Helpers', 'sanitize_choice' ),
				)
			);
			$wp_customize->add_control(
				$vk_footer_setting_name . '[footer_upper_widget_padding_delete]',
				array(
					'label'    => __( 'Footer Upper Widget Padding', 'vk-footer-customize-textdomain' ),
					'section'  => 'vk_footer_option',
					'settings' => $vk_footer_setting_name . '[footer_upper_widget_padding_delete]',
					'type'     => 'select',
					'choices'  => array(
						'false' => __( 'Nothing to do', 'vk-footer-customize-textdomain' ),
						'true'  => __( 'Delete Padding', 'vk-footer-customize-textdomain' ),
					),
					'priority' => $priority,
				)
			);
			$wp_customize->selective_refresh->add_partial(
				$vk_footer_setting_name . '[footer_upper_widget_padding_delete]',
				array(
					'selector'        => $vk_footer_selector,
					'render_callback' => '',
				)
			);

			// Number of Footer Widget area.
			$wp_customize->add_setting(
				$vk_footer_setting_name . '[footer_widget_area_count]',
				array(
					'default'           => 3,
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( 'VK_Helpers', 'sanitize_number' ),
				)
			);
			$wp_customize->add_control(
				$vk_footer_setting_name . '[footer_widget_area_count]',
				array(
					'label'       => __( 'Footer Widget Area Count', 'vk-footer-customize-textdomain' ),
					'section'     => 'vk_footer_option',
					'settings'    => $vk_footer_setting_name . '[footer_widget_area_count]',
					'type'        => 'select',
					'choices'     => array(
						1 => __( '1 column', 'vk-footer-customize-textdomain' ),
						2 => __( '2 column', 'vk-footer-customize-textdomain' ),
						3 => __( '3 column', 'vk-footer-customize-textdomain' ),
						4 => __( '4 column', 'vk-footer-customize-textdomain' ),
						6 => __( '6 column', 'vk-footer-customize-textdomain' ),
					),
					'description' => __( '* If you save and reload after making changes, the number of the widget area setting panels  will increase or decrease.', 'vk-footer-customize-textdomain' ),
					'priority'    => $priority,
				)
			);
			$wp_customize->selective_refresh->add_partial(
				$vk_footer_setting_name . '[footer_widget_area_count]',
				array(
					'selector'        => $vk_footer_selector,
					'render_callback' => '',
				)
			);
		}

		/**
		 * Enqueue Style.
		 */
		public static function enqueue_style() {
			global $vk_footer_selector;
			global $vk_footer_setting_name;
			global $vk_footer_customize_hook_style;

			$options = get_option( $vk_footer_setting_name );
			$default = self::default_options();
			$options = wp_parse_args( $options, $default );

			$dynamic_css = '';
			if ( 'true' === $options['footer_upper_widget_padding_delete'] ) {
				$dynamic_css  = $vk_footer_selector . '{';
				$dynamic_css .= 'padding:0;';
				$dynamic_css .= '}';
			}
			wp_add_inline_style( $vk_footer_customize_hook_style, $dynamic_css );
		}

		/**
		 * Footer Widget Area Count.
		 *
		 * @param int $footer_widget_area_count Footer Widget Area Count.
		 */
		public static function set_footter_widget_area_count( $footer_widget_area_count ) {
			global $vk_footer_setting_name;
			$footer_widget_area_count = 3;
			$options                  = get_option( $vk_footer_setting_name );
			if ( ! empty( $options['footer_widget_area_count'] ) ) {
				$footer_widget_area_count = (int) $options['footer_widget_area_count'];
			}
			return $footer_widget_area_count;
		}

	}
	new Widget_Area_Setting();
}
