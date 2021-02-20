<?php
/**
 * VK Advanced Slider
 *
 * @package Lightning G3
 */

if ( ! class_exists( 'VK_Advanced_Slider' ) ) {
	/**
	 * VK Advanced Slider
	 */
	class VK_Advanced_Slider {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'customize_register', array( __CLASS__, 'register_customize' ) );
			add_shortcode( 'vk_advanced_slider', array( __CLASS__, 'get_slide_html' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_slide_script' ) );
			// add_action( 'init', array( __CLASS__, 'register_sidebar' ) );
		}

		/**
		 * Register Sidebar
		 */
		// public static function register_sidebar() {
		// 	register_sidebar(
		// 		array(
		// 			'name'          => __( 'Slide Widget Area', 'katawara' ),
		// 			'id'            => 'slide-widget',
		// 			'before_widget' => '<section class="widget %2$s l-container" id="%1$s">',
		// 			'after_widget'  => '</section>',
		// 			'before_title'  => '',
		// 			'after_title'   => '',
		// 		)
		// 	);
		// }

		/**
		 * Display HTML
		 */
		public static function display_html() {
			$options = get_option( 'vk_advanced_slider_option' );
			$default = vk_advanced_slider_default_options();
			$options = wp_parse_args( $options, $default );

			if ( 'hide' !== $options['top_slide_display'] ) {
				do_action( 'vk_advanced_slider_before' );
				if ( 'display' === $options['top_slide_display'] ) {
					echo do_shortcode( '[vk_advanced_slider]' );
				// } elseif ( 'widget' === $options['top_slide_display'] ) {
				// 	if ( is_active_sidebar( 'slide-widget' ) ) {
				// 		dynamic_sidebar( 'slide-widget' );
				// 	}
				}
				do_action( 'vk_advanced_slider_after' );
			}
		}

		/**
		 * Slide Count Max
		 */
		public static function slide_count_max() {
			$slide_count_max = 3;
			$slide_count_max = apply_filters( 'vk_advanced_slider_count_max', $slide_count_max );
			return $slide_count_max;
		}

		/**
		 * Slide Count
		 */
		public static function slide_count() {
			$slide_count     = 0;
			$slide_count_max = self::slide_count_max();
			$options = get_option( 'vk_advanced_slider_option' );
			$default = vk_advanced_slider_default_options();
			$options = wp_parse_args( $options, $default );

			for ( $i = 1; $i <= $slide_count_max; $i++ ) {
				if ( ! empty( $options[ 'top_slide_image_' . $i ] ) ) {
					$slide_count ++;
				}
			}
			return $slide_count;
		}

		/**
		 * IS Slide Outer Link
		 *
		 * @param int $i Slide Count.
		 */
		public static function is_slide_outer_link( $i ) {
			$options = get_option( 'vk_advanced_slider_option' );
			$default = vk_advanced_slider_default_options();
			$options = wp_parse_args( $options, $default );

			if ( ! empty( $options[ 'top_slide_url_' . $i ] ) && empty( $options[ 'top_slide_text_btn_' . $i ] ) ) {
				return true;
			} else {
				return false;
			}
		}

		/**
		 * Slide Cover Style
		 *
		 * @param int $i Slide Count.
		 */
		public static function slide_cover_style( $i ) {
			$options = get_option( 'vk_advanced_slider_option' );
			$default = vk_advanced_slider_default_options();
			$options = wp_parse_args( $options, $default );

			$cover_style = '';

			if ( ! empty( $options[ 'top_slide_cover_color_' . $i ] ) && ! empty( $options[ 'top_slide_cover_opacity_' . $i ] ) ) {
				// bgcolor.
				$cover_style = 'background-color:' . $options[ 'top_slide_cover_color_' . $i ] . ';';

				// opacity.
				$opacity      = VK_Helpers::sanitize_number_percentage( $options[ 'top_slide_cover_opacity_' . $i ] ) / 100;
				$cover_style .= 'opacity:' . $opacity;

			}
			return $cover_style;
		}

		/**
		 * Swiper Paramater
		 *
		 * @param string $paras paramater.
		 */
		public static function swiper_paras_json( $paras = '' ) {

			$default = array(
				'slidesPerView' => 1,
				'spaceBetween'  => 0,
				'loop'          => true,
				'autoplay'      => array(
					'delay' => 2000,
				),
				'pagination'    => array(
					'el'        => '.swiper-pagination',
					'clickable' => true,
				),
				'navigation'    => array(
					'nextEl' => '.swiper-button-next',
					'prevEl' => '.swiper-button-prev',
				),
			);

			$paras = wp_parse_args( $paras, $default );
			$json  = wp_json_encode( $paras );
			return $json;
		}

		/**
		 * Customizer.
		 *
		 * @param \WP_Customize_Manager $wp_customize Customizer.
		 */
		public static function register_customize( $wp_customize ) {
			// require_once dirname( __FILE__ ) . '/class-custom-text-control.php';

			global $vk_advansed_slider_prefix;

			$wp_customize->add_section(
				'vk_advanced_slider',
				array(
					'title'    => $vk_advansed_slider_prefix . __( 'Home page slide show', 'katawara' ),
					'priority' => 520,
				)
			);

			// Hide Slide.
			$wp_customize->add_setting(
				'vk_advanced_slider_option[top_slide_display]',
				array(
					'default'           => 'display',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( 'VK_Helpers', 'sanitize_choice' ),
				)
			);

			$wp_customize->add_control(
				'vk_advanced_slider_option[top_slide_display]',
				array(
					'label'    => __( 'Display Setting', 'katawara' ),
					'section'  => 'vk_advanced_slider',
					'settings' => 'vk_advanced_slider_option[top_slide_display]',
					'type'     => 'select',
					'choices'  => array(
						'display' => __( 'Display Slides', 'katawara' ),
						'widget'  => __( 'Display Widgets', 'katawara' ),
						'hide'    => __( 'Hide Slide', 'katawara' ),
					),
				)
			);

			// Slide interval time.
			$wp_customize->add_setting(
				'vk_advanced_slider_option[top_slide_effect]',
				array(
					'default'           => 'slide',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				'vk_advanced_slider_option[top_slide_effect]',
				array(
					'label'       => __( 'Slide effect', 'katawara' ),
					'section'     => 'vk_advanced_slider',
					'settings'    => 'vk_advanced_slider_option[top_slide_effect]',
					'type'        => 'select',
					'choices'     => array(
						'slide'     => 'slide',
						'fade'      => 'fade',
						// 'cube'      => 'cube',
						'coverflow' => 'coverflow',
						'flip'      => 'flip',
					),
					'priority'    => 604,
					'description' => '',
					'input_after' => '',
				)
			);

			// Slide transition time.
			$wp_customize->add_setting(
				'vk_advanced_slider_option[top_slide_speed]',
				array(
					'default'           => 2000,
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( 'VK_Helpers', 'sanitize_number' ),
				)
			);

			$wp_customize->add_control(
				new Custom_Text_Control(
					$wp_customize,
					'vk_advanced_slider_option[top_slide_speed]',
					array(
						'label'       => __( 'Slide transition time', 'katawara' ),
						'section'     => 'vk_advanced_slider',
						'settings'    => 'vk_advanced_slider_option[top_slide_speed]',
						'type'        => 'text',
						'priority'    => 605,
						'description' => '',
						'input_after' => __( 'millisecond', 'katawara' ),
					)
				)
			);

			// slide image.
			$priority = 610;

			$slide_count_max = self::slide_count_max();

			$default_options = vk_advanced_slider_default_options();

			$fields = array(
				'top_slide_image',
				'top_slide_image_mobile',
				'top_slide_alt',
				'top_slide_cover_color',
				'top_slide_cover_opacity',
				'top_slide_url',
				'top_slide_link_blank',
				'top_slide_text_title',
				'top_slide_text_caption',
				'top_slide_text_btn',
				'top_slide_text_align',
				'top_slide_text_color',
				'top_slide_text_shadow_use',
				'top_slide_text_shadow_color'
			);

			for ( $i = 1; $i <= $slide_count_max; $i++ ) {

				foreach ( $fields as $k => $v ){
					if ( ! empty( $default_options[$v . '_' . $i] ) ){
						$customize_default[$v] = $default_options[$v . '_' . $i];
					} else {
						$customize_default[$v] = '';
					}
				}

				// slide_title.
				$wp_customize->add_setting(
					'slide_title_' . $i,
					array(
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				$wp_customize->add_control(
					new VK_Custom_Html_Control(
						$wp_customize,
						'slide_title_' . $i,
						array(
							'label'            => __( 'Slide', 'katawara' ) . ' [' . $i . ']',
							'section'          => 'vk_advanced_slider',
							'type'             => 'text',
							'custom_title_sub' => '',
							'custom_html'      => '',
							'priority'         => $priority,
						)
					)
				);

				// image.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_image_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_image'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'esc_url_raw',
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize,
						'vk_advanced_slider_option[top_slide_image_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Slide Image', 'katawara' ),
							'section'     => 'vk_advanced_slider',
							'settings'    => 'vk_advanced_slider_option[top_slide_image_' . $i . ']',
							'description' => __( 'Recommended image size : 1900*1069px', 'katawara' ),
						)
					)
				);

				$wp_customize->selective_refresh->add_partial(
					'vk_advanced_slider_option[top_slide_image_' . $i . ']',
					array(
						'selector'        => '.item-' . $i . ' picture',
						'render_callback' => '',
					)
				);

				// image mobile.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_image_mobile_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_image_mobile'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'esc_url_raw',
					)
				);

				$wp_customize->add_control(
					new WP_Customize_Image_Control(
						$wp_customize,
						'vk_advanced_slider_option[top_slide_image_mobile_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Slide image for mobile', 'katawara' ) . ' (' . __( 'optional', 'katawara' ) . ')',
							'section'     => 'vk_advanced_slider',
							'settings'    => 'vk_advanced_slider_option[top_slide_image_mobile_' . $i . ']',
							'description' => '',
						)
					)
				);

				// alt.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_alt_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_alt'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				$wp_customize->add_control(
					new Custom_Text_Control(
						$wp_customize,
						'vk_advanced_slider_option[top_slide_alt_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Slide image alt', 'katawara' ),
							'section'     => 'vk_advanced_slider',
							'settings'    => 'vk_advanced_slider_option[top_slide_alt_' . $i . ']',
							'type'        => 'text',
							'description' => __( 'This title text is print to alt tag.', 'katawara' ),
						)
					)
				);

				// color.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_cover_color_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_cover_color'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'sanitize_hex_color',
					)
				);
				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						'vk_advanced_slider_option[top_slide_cover_color_' . $i . ']',
						array(
							'label'    => '[' . $i . '] ' . __( 'Slide cover color', 'katawara' ) . ' (' . __( 'optional', 'katawara' ) . ')',
							'section'  => 'vk_advanced_slider',
							'settings' => 'vk_advanced_slider_option[top_slide_cover_color_' . $i . ']',
						)
					)
				);

				// opacity.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_cover_opacity_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_cover_opacity'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => array( 'VK_Helpers', 'sanitize_number_percentage' ),
					)
				);
				$wp_customize->add_control(
					new Custom_Text_Control(
						$wp_customize,
						'vk_advanced_slider_option[top_slide_cover_opacity_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Slide cover opacity', 'katawara' ),
							'section'     => 'vk_advanced_slider',
							'settings'    => 'vk_advanced_slider_option[top_slide_cover_opacity_' . $i . ']',
							'type'        => 'text',
							'description' => __( 'Please input 0 - 100 number', 'katawara' ),
							'input_after' => '%',
						)
					)
				);

				// url.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_url_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_url'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'esc_url_raw',
					)
				);

				$wp_customize->add_control(
					'vk_advanced_slider_option[top_slide_url_' . $i . ']',
					array(
						'label'    => '[' . $i . '] ' . __( 'Slide image link url', 'katawara' ),
						'section'  => 'vk_advanced_slider',
						'settings' => 'vk_advanced_slider_option[top_slide_url_' . $i . ']',
						'type'     => 'text',
					)
				);

				// link blank.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_link_blank_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_link_blank'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => array( 'VK_Helpers', 'sanitize_checkbox' ),
					)
				);

				$wp_customize->add_control(
					'vk_advanced_slider_option[top_slide_link_blank_' . $i . ']',
					array(
						'label'    => __( 'Open in new window.', 'katawara' ),
						'section'  => 'vk_advanced_slider',
						'settings' => 'vk_advanced_slider_option[top_slide_link_blank_' . $i . ']',
						'type'     => 'checkbox',
					)
				);

				// text title.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_text_title_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_title'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'wp_kses_post',
					)
				);

				$wp_customize->add_control(
					'vk_advanced_slider_option[top_slide_text_title_' . $i . ']',
					array(
						'label'       => '[' . $i . '] ' . __( 'Slide title', 'katawara' ) . ' (' . __( 'optional', 'katawara' ) . ')',
						'section'     => 'vk_advanced_slider',
						'settings'    => 'vk_advanced_slider_option[top_slide_text_title_' . $i . ']',
						'type'        => 'textarea',
						'description' => '',
					)
				);

				// text caption.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_text_caption_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_caption'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'wp_kses_post',
					)
				);

				$wp_customize->add_control(
					'vk_advanced_slider_option[top_slide_text_caption_' . $i . ']',
					array(
						'label'       => '[' . $i . '] ' . __( 'Slide text', 'katawara' ) . ' (' . __( 'optional', 'katawara' ) . ')',
						'section'     => 'vk_advanced_slider',
						'settings'    => 'vk_advanced_slider_option[top_slide_text_caption_' . $i . ']',
						'type'        => 'textarea',
						'description' => '',
					)
				);

				// btn text.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_text_btn_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_btn'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'wp_kses_post',
					)
				);

				$wp_customize->add_control(
					new Custom_Text_Control(
						$wp_customize,
						'vk_advanced_slider_option[top_slide_text_btn_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Button text', 'katawara' ) . ' (' . __( 'optional', 'katawara' ) . ')',
							'section'     => 'vk_advanced_slider',
							'settings'    => 'vk_advanced_slider_option[top_slide_text_btn_' . $i . ']',
							'type'        => 'text',
							'description' => __( 'If you do not fill in the link url and button text that, button is do not display.', 'katawara' ),
						)
					)
				);

				// text position.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_text_align_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_align'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => array( 'VK_Helpers', 'sanitize_choice' ),
					)
				);

				$wp_customize->add_control(
					'vk_advanced_slider_option[top_slide_text_align_' . $i . ']',
					array(
						'label'    => '[' . $i . '] ' . __( 'Position to display text', 'katawara' ) . ' (' . __( 'optional', 'katawara' ) . ')',
						'section'  => 'vk_advanced_slider',
						'settings' => 'vk_advanced_slider_option[top_slide_text_align_' . $i . ']',
						'type'     => 'radio',
						'choices'  => array(
							'left'   => __( 'Left', 'katawara' ),
							'center' => __( 'Center', 'katawara' ),
							'right'  => __( 'Right', 'katawara' ),
						),
					)
				);

				// color.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_text_color_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_color'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'sanitize_hex_color',
					)
				);
				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						'vk_advanced_slider_option[top_slide_text_color_' . $i . ']',
						array(
							'label'    => '[' . $i . '] ' . __( 'Slide text color', 'katawara' ) . ' (' . __( 'optional', 'katawara' ) . ')',
							'section'  => 'vk_advanced_slider',
							'settings' => 'vk_advanced_slider_option[top_slide_text_color_' . $i . ']',

						)
					)
				);

				// top_slide_text_shadow_use.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_text_shadow_use_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_shadow_use'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => array( 'VK_Helpers', 'sanitize_checkbox' ),
					)
				);
				$wp_customize->add_control(
					'vk_advanced_slider_option[top_slide_text_shadow_use_' . $i . ']',
					array(
						'label'    => __( 'Use text shadow', 'katawara' ) . ' (' . __( 'optional', 'katawara' ) . ')',
						'section'  => 'vk_advanced_slider',
						'settings' => 'vk_advanced_slider_option[top_slide_text_shadow_use_' . $i . ']',
						'type'     => 'checkbox',
					)
				);

				// top_slide_text_shadow_color.
				$wp_customize->add_setting(
					'vk_advanced_slider_option[top_slide_text_shadow_color_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_shadow_color'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'sanitize_hex_color',
					)
				);
				$wp_customize->add_control(
					new WP_Customize_Color_Control(
						$wp_customize,
						'vk_advanced_slider_option[top_slide_text_shadow_color_' . $i . ']',
						array(
							'label'    => '[' . $i . '] ' . __( 'Text shadow color', 'katawara' ) . ' (' . __( 'optional', 'katawara' ) . ')',
							'section'  => 'vk_advanced_slider',
							'settings' => 'vk_advanced_slider_option[top_slide_text_shadow_color_' . $i . ']',
						)
					)
				);
			}
		}

		/**
		 * Add Sweier Setting
		 */
		public static function add_slide_script() {
			$slide_count_max = self::slide_count_max();
			$slide_count     = self::slide_count();

			$options = get_option( 'vk_advanced_slider_option' );
			$default = vk_advanced_slider_default_options();
			$options = wp_parse_args( $options, $default );

			if ( $slide_count < 2 ) {
				$paras['loop'] = false;
			}

			if ( empty( $options['top_slide_time'] ) ) {
				$paras['autoplay']['delay'] = 4000;
			} else {
				$paras['autoplay']['delay'] = esc_attr( $options['top_slide_time'] );
			}

			if ( empty( $options['top_slide_effect'] ) ) {
				$paras['effect'] = 'slide';
			} else {
				$paras['effect'] = esc_attr( $options['top_slide_effect'] );
			}

			if ( ! empty( $options['top_slide_speed'] ) ) {
				$paras['speed'] = intval( $options['top_slide_speed'] );
			}

			$swiper_paras = self::swiper_paras_json( $paras );

			$slider_prefix = esc_html($options['top_slide_prefix']);

			$tag = 'var ' . $slider_prefix . 'swiper = new Swiper(\'.' . $slider_prefix . 'swiper-container\', ' . $swiper_paras . ');';
			wp_add_inline_script( 'swiper-js', $tag, 'after' );
		}

		/**
		 * Get Slide HTML
		 */
		public static function get_slide_html() {
			// count top slide.
			$slide_count_max = self::slide_count_max();
			$slide_count     = self::slide_count();

			$options = get_option( 'vk_advanced_slider_option' );
			$default = vk_advanced_slider_default_options();
			$options = wp_parse_args( $options, $default );
			$slider_prefix = esc_html($options['top_slide_prefix']);

			$slide_html = '';

			if ( $slide_count ) {

				$slide_html .= '<div class="' . $slider_prefix . 'swiper-container swiper-container slide slide-main">';
				$slide_html .= '<div class="swiper-wrapper slide-inner">';

				// Why end point is $slide_count_max that not $slide_count, image exist 1,2,5.
				for ( $i = 1; $i <= $slide_count_max; $i++ ) {

					$slide_url = '';

					// If Alt exist.
					$slide_alt = '';
					if ( ! empty( $options[ 'top_slide_alt_' . $i ] ) ) {
						$slide_alt = $options[ 'top_slide_alt_' . $i ];
					} elseif ( ! empty( $options[ 'top_slide_title_' . $i ] ) ) {
						$slide_alt = $options[ 'top_slide_title_' . $i ];
					} else {
						$slide_alt = '';
					}

					// Slide Display.
					if ( ! empty( $options[ 'top_slide_image_' . $i ] ) ) {
						$link_target = ( isset( $options[ 'top_slide_link_blank_' . $i ] ) && $options[ 'top_slide_link_blank_' . $i ] ) ? ' target="_blank"' : '';

						// 画像１つのdiv.
						$slide_html .= '<div class="swiper-slide item-' . $i . '">';

						if ( self::is_slide_outer_link( $i ) ) {

							$slide_html .= '<a href="' . esc_url( $options[ 'top_slide_url_' . $i ] ) . '"' . $link_target . '>';
						}

						$slide_html .= '<picture>';

						// If Mobile Image exist.
						if ( ! empty( $options[ 'top_slide_image_mobile_' . $i ] ) ) {
							$slide_html .= '<source media="(max-width: 767px)" srcset="' . esc_attr( $options[ 'top_slide_image_mobile_' . $i ] ) . '">';
						}

						$slide_html .= '<img src="' . esc_attr( $options[ 'top_slide_image_' . $i ] ) . '" alt="' . esc_attr( $slide_alt ) . '" class="slide-item-img">';
						$slide_html .= '</picture>';

						// slide-cover.
						$cover_style = self::slide_cover_style( $i );

						if ( $cover_style ) {
							$cover_style = ( $cover_style ) ? ' style="' . $cover_style . '"' : '';
							$slide_html .= '<div class="slide-cover"' . $cover_style . '></div>';
						}

						if ( self::is_slide_outer_link( $i ) ) {
							$slide_html .= '</a>';
						}

						// mini_content.
						$mini_content_args['style_class']  = 'mini-content-' . $i;
						$mini_content_args['align']        = ( ! empty( $options[ 'top_slide_text_align_' . $i ] ) ) ? $options[ 'top_slide_text_align_' . $i ] : '';
						$mini_content_args['title']        = ( ! empty( $options[ 'top_slide_text_title_' . $i ] ) ) ? $options[ 'top_slide_text_title_' . $i ] : '';
						$mini_content_args['caption']      = ( ! empty( $options[ 'top_slide_text_caption_' . $i ] ) ) ? $options[ 'top_slide_text_caption_' . $i ] : '';
						$mini_content_args['text_color']   = ( ! empty( $options[ 'top_slide_text_color_' . $i ] ) ) ? $options[ 'top_slide_text_color_' . $i ] : '#333';
						$mini_content_args['link_url']     = ( ! empty( $options[ 'top_slide_url_' . $i ] ) ) ? $options[ 'top_slide_url_' . $i ] : '';
						$mini_content_args['link_target']  = ( ! empty( $options[ 'top_slide_link_blank_' . $i ] ) ) ? ' target="_blank"' : '';
						$mini_content_args['btn_text']     = ( ! empty( $options[ 'top_slide_text_btn_' . $i ] ) ) ? $options[ 'top_slide_text_btn_' . $i ] : '';
						$mini_content_args['btn_color']    = ( ! empty( $options[ 'top_slide_text_color_' . $i ] ) ) ? $options[ 'top_slide_text_color_' . $i ] : '#337ab7';
						$mini_content_args['btn_bg_color'] = ( ! empty( $options['color_key'] ) ) ? $options['color_key'] : '#337ab7';
						$mini_content_args['shadow_use']   = ( ! empty( $options[ 'top_slide_text_shadow_use_' . $i ] ) ) ? $options[ 'top_slide_text_shadow_use_' . $i ] : false;
						$mini_content_args['shadow_color'] = ( ! empty( $options[ 'top_slide_text_shadow_color_' . $i ] ) ) ? $options[ 'top_slide_text_shadow_color_' . $i ] : '#fff';

						$style = '';
						$slide_text_class = '';
						if ( $mini_content_args['align'] ) {
							$style = ' style="text-align:' . esc_attr( $mini_content_args['align'] ) . '"';
							$slide_text_class = 'slide-text-set--align--' . $mini_content_args['align'] . ' ';
						}

						$slide_html .= '<div class="slide-text-set ' . $slide_text_class . 'mini-content ' . esc_attr( $mini_content_args['style_class'] ) . '"' . $style . '>';
						$slide_html .= '<div class="container">';

						$font_style = '';
						if ( $mini_content_args['text_color'] ) {
							$font_style .= 'color:' . $mini_content_args['text_color'] . ';';
						} else {
							$font_style .= '';
						}

						if ( $mini_content_args['shadow_use'] ) {
							if ( $mini_content_args['shadow_color'] ) {
								$font_style .= 'text-shadow:0 0 2px ' . $mini_content_args['shadow_color'];
							} else {
								$font_style .= 'text-shadow:0 0 2px #000';
							}
						}

						$font_style = ( $font_style ) ? ' style="' . esc_attr( $font_style ) . '"' : '';

						// If Text Title exist.
						if ( $mini_content_args['title'] ) {

							$slide_html .= '<h3 class="slide-text-title"' . $font_style . '>';
							$slide_html .= nl2br( wp_kses_post( $mini_content_args['title'] ) );
							$slide_html .= '</h3>';

						}

						// If Text caption exist.
						if ( $mini_content_args['caption'] ) {
							$slide_html .= '<div class="slide-text-caption"' . $font_style . '>';
							$slide_html .= nl2br( wp_kses_post( $mini_content_args['caption'] ) );
							$slide_html .= '</div>';
						}

						// If Button exist.
						if ( $mini_content_args['link_url'] && $mini_content_args['btn_text'] ) {
							// Shadow.
							$box_shadow  = '';
							$text_shadow = '';
							if ( $mini_content_args['shadow_use'] ) {
								if ( $mini_content_args['shadow_color'] ) {
									$box_shadow  = 'box-shadow:0 0 2px ' . $mini_content_args['shadow_color'] . ';';
									$text_shadow = 'text-shadow:0 0 2px ' . $mini_content_args['shadow_color'] . ';';
								} else {
									$box_shadow  = 'box-shadow:0 0 2px #000;';
									$text_shadow = 'text-shadow:0 0 2px #000;';
								}
							}

							$style_class = esc_attr( $mini_content_args['style_class'] );
							$slide_html .= '<style type="text/css">';
							$slide_html .= '.' . $style_class . ' .btn-ghost { 
								--vk-color-text-body: ' . $mini_content_args['text_color'] . ';' . $box_shadow . $text_shadow . ' }';
							$slide_html .= '.' . $style_class . ' .btn-ghost:hover { border-color:' . $mini_content_args['btn_bg_color'] . '; background-color:' . $mini_content_args['btn_bg_color'] . '; color:#fff; text-shadow:none; }';
							$slide_html .= '</style>';
							$slide_html .= '<a class="btn btn-ghost" href="' . esc_url( $mini_content_args['link_url'] ) . '"' . $mini_content_args['link_target'] . '>' . wp_kses_post( $mini_content_args['btn_text'] ) . '</a>';

						}

						$slide_html .= '</div><!-- .container -->';
						$slide_html .= '</div><!-- [ /.slide-text-set.mini-content  ] -->';
						$slide_html .= '</div><!-- [ /.item ] -->';

					}
				}

				$slide_html .= '</div><!-- [ /.swiper-wrapper ] -->';
				if ( $slide_count >= 2 ) {
					// Add Pagination.
					$slide_html .= '<div class="swiper-pagination swiper-pagination-white"></div>';
					// Add Arrows.
					$slide_html .= '<div class="swiper-button-next swiper-button-white"></div>';
					$slide_html .= '<div class="swiper-button-prev swiper-button-white"></div>';
				}

				$slide_html .= '</div><!-- [ /.swiper-container ] -->';

			}

			return $slide_html;
		}
	}
	new VK_Advanced_Slider();
}
