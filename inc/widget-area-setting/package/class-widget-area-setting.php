<?php
/**
 * Widget Setting
 *
 * @package Lightning
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
			add_action( 'customize_register', array( __CLASS__, 'resister_customize' ) );
			add_action( 'wp_head', array( __CLASS__, 'enqueue_style' ), 5 );
			add_filter( 'lightning_footer_widget_area_count', array( __CLASS__, 'set_footter_widget_area_count' ) );
		}

		/**
		 * Customizer.
		 *
		 * @param \WP_Customize_Manager $wp_customize Customizer.
		 */
		public static function resister_customize( $wp_customize ) {
			global $widget_area_setting_prefix;

			// add section.
			$wp_customize->add_section(
				'widget_area_setting',
				array(
					'title'    => $widget_area_setting_prefix . __( 'Widget Area Setting', 'lightning' ),
					'priority' => 512,
				)
			);

			// Footer Upper Widget Area Heading.
			$wp_customize->add_setting(
				'footer-upper-widget-area',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				new Custom_Html_Control(
					$wp_customize,
					'footer-upper-widget-area',
					array(
						'label'            => __( 'Widget area of upper footer', 'lightning' ),
						'section'          => 'widget_area_setting',
						'type'             => 'text',
						'custom_title_sub' => '',
						'custom_html'      => '',
					)
				)
			);

			// Padding Bottom.
			$wp_customize->add_setting(
				'lightning_widget_setting[footer_upper_widget_padding_delete]',
				array(
					'default'           => false,
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'lightning_sanitize_checkbox',
				)
			);

			$wp_customize->add_control(
				'lightning_widget_setting[footer_upper_widget_padding_delete]',
				array(
					'label'    => __( 'Delete Padding', 'lightning' ),
					'section'  => 'widget_area_setting',
					'settings' => 'lightning_widget_setting[footer_upper_widget_padding_delete]',
					'type'     => 'checkbox',
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'lightning_widget_setting[footer_upper_widget_padding_delete]',
				array(
					'selector'        => '.siteContent_after',
					'render_callback' => '',
				)
			);

			// Footer Widget Area Heading.
			$wp_customize->add_setting(
				'footer-widget-area',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				new Custom_Html_Control(
					$wp_customize,
					'footer-widget-area',
					array(
						'label'            => __( 'Footer widget area', 'lightning' ),
						'section'          => 'widget_area_setting',
						'type'             => 'text',
						'custom_title_sub' => '',
						'custom_html'      => '',
					)
				)
			);

			// Number of Footer Widget area.
			$wp_customize->add_setting(
				'lightning_widget_setting[footer_widget_area_count]',
				array(
					'default'           => 3,
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'lightning_sanitize_number',
				)
			);

			$wp_customize->add_control(
				'lightning_widget_setting[footer_widget_area_count]',
				array(
					'label'       => __( 'Footer Widget Area Count', 'lightning' ),
					'section'     => 'widget_area_setting',
					'settings'    => 'lightning_widget_setting[footer_widget_area_count]',
					'type'        => 'select',
					'choices'     => array(
						'1' => __( '1 column', 'lightning' ),
						'2' => __( '2 column', 'lightning' ),
						'3' => __( '3 column', 'lightning' ),
						'4' => __( '4 column', 'lightning' ),
					),
					'description' => __( '* If you save and reload after making changes, the number of the widget area setting panels  will increase or decrease.', 'lightning' ),
				)
			);

			$wp_customize->selective_refresh->add_partial(
				'lightning_widget_setting[footer_widget_area_count]',
				array(
					'selector'        => '.footerWidget',
					'render_callback' => '',
				)
			);

		}

		/**
		 * Enqueue Style.
		 */
		public static function enqueue_style() {
			$options     = get_option( 'lightning_widget_setting' );
			$dynamic_css = '';
			if ( ! empty( $options['footer_upper_widget_padding_delete'] ) ) {
				$dynamic_css  = '.siteContent_after.sectionBox{';
				$dynamic_css .= 'padding:0';
				$dynamic_css .= '}';
			}
			wp_add_inline_style( 'lightning-design-style', $dynamic_css );
		}

		/**
		 * Footer Widget Area Count.
		 *
		 * @param int $footer_widget_area_count Footer Widget Area Count.
		 */
		public static function set_footter_widget_area_count( $footer_widget_area_count ) {
			$footer_widget_area_count = 3;
			$options                  = get_option( 'lightning_widget_setting' );
			if ( isset( $options['footer_widget_area_count'] ) ) {
				if ( '1' === $options['footer_widget_area_count'] ) {
					$footer_widget_area_count = 1;
				} elseif ( '2' === $options['footer_widget_area_count'] ) {
					$footer_widget_area_count = 2;
				} elseif ( '3' === $options['footer_widget_area_count'] ) {
					$footer_widget_area_count = 3;
				} elseif ( '4' === $options['footer_widget_area_count'] ) {
					$footer_widget_area_count = 4;
				}
			}
			return $footer_widget_area_count;
		}

	}
	new Widget_Area_Setting();
}
