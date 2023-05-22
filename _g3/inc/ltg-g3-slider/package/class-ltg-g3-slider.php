<?php
/**
 * VK Advanced Slider
 *
 * @package Lightning G3
 */

if ( ! class_exists( 'LTG_G3_Slider' ) ) {
	/**
	 * VK Advanced Slider
	 */
	class LTG_G3_Slider {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'customize_register', array( __CLASS__, 'register_customize' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'add_slide_script' ) );
		}

		/**
		 * Display HTML
		 */
		public static function display_html() {
			$options = get_option( 'lightning_theme_options' );
			$default = lightning_g3_slider_default_options();
			$options = wp_parse_args( $options, $default );

			if ( 'hide' !== $options['top_slide_display'] ) {
				do_action( 'lightning_top_slide_before' );
				if ( 'display' === $options['top_slide_display'] ) {
					echo self::get_slide_html();
				}
				do_action( 'lightning_top_slide_after' );
			}
		}

		/**
		 * Slide Count Max
		 */
		public static function slide_count_max() {
			$slide_count_max = 3;
			$slide_count_max = apply_filters( 'lightning_top_slide_count_max', $slide_count_max );
			return $slide_count_max;
		}

		/**
		 * Slide Count
		 */
		public static function slide_count() {
			$slide_count     = 0;
			$slide_count_max = self::slide_count_max();
			$options         = get_option( 'lightning_theme_options' );
			$default         = lightning_g3_slider_default_options();
			$options         = wp_parse_args( $options, $default );

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
			$options = get_option( 'lightning_theme_options' );
			$default = lightning_g3_slider_default_options();
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
			$options = get_option( 'lightning_theme_options' );
			$default = lightning_g3_slider_default_options();
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
				'ltg_g3_slider',
				array(
					'title'    => $vk_advansed_slider_prefix . __( 'Home page slide show', 'lightning' ),
					'priority' => 520,
				)
			);

			// Hide Slide.
			$wp_customize->add_setting(
				'lightning_theme_options[top_slide_display]',
				array(
					'default'           => 'display',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( 'VK_Helpers', 'sanitize_choice' ),
				)
			);

			$wp_customize->add_control(
				'lightning_theme_options[top_slide_display]',
				array(
					'label'    => __( 'Display Setting', 'lightning' ),
					'section'  => 'ltg_g3_slider',
					'settings' => 'lightning_theme_options[top_slide_display]',
					'type'     => 'select',
					'choices'  => array(
						'display' => __( 'Display Slides', 'lightning' ),
						'hide'    => __( 'Hide Slide', 'lightning' ),
					),
				)
			);

			// Stop time.
			$wp_customize->add_setting(
				'lightning_theme_options[top_slide_time]',
				array(
					'default'           => 4000,
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( 'VK_Helpers', 'sanitize_number' ),
				)
			);

			$wp_customize->add_control(
				new VK_Custom_Text_Control(
					$wp_customize,
					'lightning_theme_options[top_slide_time]',
					array(
						'label'       => __( 'Slide rest time', 'lightning' ),
						'section'     => 'ltg_g3_slider',
						'settings'    => 'lightning_theme_options[top_slide_time]',
						'type'        => 'text',
						'description' => '',
						'input_after' => __( 'millisecond', 'lightning' ),
					)
				)
			);

			// Array of allowed swiper effects by kurudrive
			$ltg_g3_swiper_effects = array(
				'slide'     => 'slide',
				'fade'      => 'fade',
				'coverflow' => 'coverflow',
				'flip'      => 'flip',
			);
			$ltg_g3_swiper_effects = apply_filters( 'ltg_g3_swiper_effects', $ltg_g3_swiper_effects );

			// Slide interval effect.
			$wp_customize->add_setting(
				'lightning_theme_options[top_slide_effect]',
				array(
					'default'           => 'slide',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$wp_customize->add_control(
				'lightning_theme_options[top_slide_effect]',
				array(
					'label'       => __( 'Slide effect', 'lightning' ),
					'section'     => 'ltg_g3_slider',
					'settings'    => 'lightning_theme_options[top_slide_effect]',
					'type'        => 'select',
					'choices'     => $ltg_g3_swiper_effects,
					'description' => '',
					'input_after' => '',
				)
			);

			// Slide transition time.
			$wp_customize->add_setting(
				'lightning_theme_options[top_slide_speed]',
				array(
					'default'           => 2000,
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( 'VK_Helpers', 'sanitize_number' ),
				)
			);

			$wp_customize->add_control(
				new VK_Custom_Text_Control(
					$wp_customize,
					'lightning_theme_options[top_slide_speed]',
					array(
						'label'       => __( 'Slide transition time', 'lightning' ),
						'section'     => 'ltg_g3_slider',
						'settings'    => 'lightning_theme_options[top_slide_speed]',
						'type'        => 'text',
						'description' => '',
						'input_after' => __( 'millisecond', 'lightning' ),
					)
				)
			);

			$slide_count_max = self::slide_count_max();

			$default_options = lightning_g3_slider_default_options();

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
				'top_slide_text_shadow_color',
			);

			for ( $i = 1; $i <= $slide_count_max; $i++ ) {

				foreach ( $fields as $k => $v ) {
					if ( ! empty( $default_options[ $v . '_' . $i ] ) ) {
						$customize_default[ $v ] = $default_options[ $v . '_' . $i ];
					} else {
						$customize_default[ $v ] = '';
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
							'label'            => __( 'Slide', 'lightning' ) . ' [' . $i . ']',
							'section'          => 'ltg_g3_slider',
							'type'             => 'text',
							'custom_title_sub' => '',
							'custom_html'      => '',
						)
					)
				);

				$wp_customize->selective_refresh->add_partial(
					'lightning_theme_options[top_slide_text_title_' . $i . ']',
					array(
						'selector'        => '.item-' . $i . ' picture',
						'render_callback' => '',
					)
				);

				// text title.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_text_title_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_title'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'wp_kses_post',
					)
				);

				$wp_customize->add_control(
					'lightning_theme_options[top_slide_text_title_' . $i . ']',
					array(
						'label'       => '[' . $i . '] ' . __( 'Slide title', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
						'section'     => 'ltg_g3_slider',
						'settings'    => 'lightning_theme_options[top_slide_text_title_' . $i . ']',
						'type'        => 'textarea',
						'description' => '',
					)
				);

				// text caption.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_text_caption_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_caption'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'wp_kses_post',
					)
				);

				$wp_customize->add_control(
					'lightning_theme_options[top_slide_text_caption_' . $i . ']',
					array(
						'label'       => '[' . $i . '] ' . __( 'Slide text', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
						'section'     => 'ltg_g3_slider',
						'settings'    => 'lightning_theme_options[top_slide_text_caption_' . $i . ']',
						'type'        => 'textarea',
						'description' => '',
					)
				);

				// btn text.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_text_btn_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_btn'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'wp_kses_post',
					)
				);

				$wp_customize->add_control(
					new VK_Custom_Text_Control(
						$wp_customize,
						'lightning_theme_options[top_slide_text_btn_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Button text', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
							'section'     => 'ltg_g3_slider',
							'settings'    => 'lightning_theme_options[top_slide_text_btn_' . $i . ']',
							'type'        => 'text',
							'description' => __( 'If you do not fill in the link url and button text that, button is do not display.', 'lightning' ),
						)
					)
				);

				// url.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_url_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_url'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'esc_url_raw',
					)
				);

				$wp_customize->add_control(
					'lightning_theme_options[top_slide_url_' . $i . ']',
					array(
						'label'    => '[' . $i . '] ' . __( 'Slide image link url', 'lightning' ),
						'section'  => 'ltg_g3_slider',
						'settings' => 'lightning_theme_options[top_slide_url_' . $i . ']',
						'type'     => 'text',
					)
				);

				// link blank.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_link_blank_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_link_blank'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => array( 'VK_Helpers', 'sanitize_checkbox' ),
					)
				);

				$wp_customize->add_control(
					'lightning_theme_options[top_slide_link_blank_' . $i . ']',
					array(
						'label'    => __( 'Open in new window.', 'lightning' ),
						'section'  => 'ltg_g3_slider',
						'settings' => 'lightning_theme_options[top_slide_link_blank_' . $i . ']',
						'type'     => 'checkbox',
					)
				);

				// text position.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_text_align_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_align'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => array( 'VK_Helpers', 'sanitize_choice' ),
					)
				);

				$wp_customize->add_control(
					'lightning_theme_options[top_slide_text_align_' . $i . ']',
					array(
						'label'    => '[' . $i . '] ' . __( 'Position to display text', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
						'section'  => 'ltg_g3_slider',
						'settings' => 'lightning_theme_options[top_slide_text_align_' . $i . ']',
						'type'     => 'radio',
						'choices'  => array(
							'left'   => __( 'Left', 'lightning' ),
							'center' => __( 'Center', 'lightning' ),
							'right'  => __( 'Right', 'lightning' ),
						),
					)
				);

				// color.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_text_color_' . $i . ']',
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
						'lightning_theme_options[top_slide_text_color_' . $i . ']',
						array(
							'label'    => '[' . $i . '] ' . __( 'Slide text color', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
							'section'  => 'ltg_g3_slider',
							'settings' => 'lightning_theme_options[top_slide_text_color_' . $i . ']',

						)
					)
				);

				// top_slide_text_shadow_use.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_text_shadow_use_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_text_shadow_use'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => array( 'VK_Helpers', 'sanitize_checkbox' ),
					)
				);
				$wp_customize->add_control(
					'lightning_theme_options[top_slide_text_shadow_use_' . $i . ']',
					array(
						'label'    => __( 'Use text shadow', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
						'section'  => 'ltg_g3_slider',
						'settings' => 'lightning_theme_options[top_slide_text_shadow_use_' . $i . ']',
						'type'     => 'checkbox',
					)
				);

				// top_slide_text_shadow_color.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_text_shadow_color_' . $i . ']',
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
						'lightning_theme_options[top_slide_text_shadow_color_' . $i . ']',
						array(
							'label'    => '[' . $i . '] ' . __( 'Text shadow color', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
							'section'  => 'ltg_g3_slider',
							'settings' => 'lightning_theme_options[top_slide_text_shadow_color_' . $i . ']',
						)
					)
				);

				// image.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_image_' . $i . ']',
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
						'lightning_theme_options[top_slide_image_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Slide Image', 'lightning' ),
							'section'     => 'ltg_g3_slider',
							'settings'    => 'lightning_theme_options[top_slide_image_' . $i . ']',
							'description' => __( 'Recommended image size : 1900*600px', 'lightning' ),
						)
					)
				);

				// image mobile.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_image_mobile_' . $i . ']',
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
						'lightning_theme_options[top_slide_image_mobile_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Slide image for mobile', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
							'section'     => 'ltg_g3_slider',
							'settings'    => 'lightning_theme_options[top_slide_image_mobile_' . $i . ']',
							'description' => '',
						)
					)
				);

				// alt.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_alt_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_alt'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => 'sanitize_text_field',
					)
				);

				$wp_customize->add_control(
					new VK_Custom_Text_Control(
						$wp_customize,
						'lightning_theme_options[top_slide_alt_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Slide image alt', 'lightning' ),
							'section'     => 'ltg_g3_slider',
							'settings'    => 'lightning_theme_options[top_slide_alt_' . $i . ']',
							'type'        => 'text',
							'description' => __( 'This title text is print to alt tag.', 'lightning' ),
						)
					)
				);

				// color.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_cover_color_' . $i . ']',
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
						'lightning_theme_options[top_slide_cover_color_' . $i . ']',
						array(
							'label'    => '[' . $i . '] ' . __( 'Slide cover color', 'lightning' ) . ' (' . __( 'optional', 'lightning' ) . ')',
							'section'  => 'ltg_g3_slider',
							'settings' => 'lightning_theme_options[top_slide_cover_color_' . $i . ']',
						)
					)
				);

				// opacity.
				$wp_customize->add_setting(
					'lightning_theme_options[top_slide_cover_opacity_' . $i . ']',
					array(
						'default'           => $customize_default['top_slide_cover_opacity'],
						'type'              => 'option',
						'capability'        => 'edit_theme_options',
						'sanitize_callback' => array( 'VK_Helpers', 'sanitize_number_percentage' ),
					)
				);
				$wp_customize->add_control(
					new VK_Custom_Text_Control(
						$wp_customize,
						'lightning_theme_options[top_slide_cover_opacity_' . $i . ']',
						array(
							'label'       => '[' . $i . '] ' . __( 'Slide cover opacity', 'lightning' ),
							'section'     => 'ltg_g3_slider',
							'settings'    => 'lightning_theme_options[top_slide_cover_opacity_' . $i . ']',
							'type'        => 'text',
							'description' => __( 'Please input 0 - 100 number', 'lightning' ),
							'input_after' => '%',
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

			$options = get_option( 'lightning_theme_options' );

			if ( 'hide' === isset( $options['top_slide_display'] ) ) {
				return;
			}

			$default = lightning_g3_slider_default_options();
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

			$slider_prefix = esc_html( $options['top_slide_prefix'] );

			$tag = 'var ' . $slider_prefix . 'swiper = new Swiper(\'.' . $slider_prefix . 'swiper\', ' . $swiper_paras . ');';

			wp_add_inline_script( 'vk-swiper-script', $tag, 'after' );
		}

		/**
		 * Get Slide HTML
		 */
		public static function get_slide_html() {
			// count top slide.
			$slide_count_max = self::slide_count_max();
			$slide_count     = self::slide_count();

			$options       = get_option( 'lightning_theme_options' );
			$default       = lightning_g3_slider_default_options();
			$options       = wp_parse_args( $options, $default );
			$slider_prefix = esc_html( $options['top_slide_prefix'] );

			$slide_html = '';

			if ( $slide_count ) {

				// class 名の swiper が２つ記載してあるように見えるが一つ目は $slider_prefix と結合される.
				$slide_html .= '<div class="' . $slider_prefix . 'swiper swiper swiper-container ltg-slide">';
				$slide_html .= '<div class="swiper-wrapper ltg-slide-inner">';

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

						$slide_html .= '<img src="' . esc_attr( $options[ 'top_slide_image_' . $i ] ) . '" alt="' . esc_attr( $slide_alt ) . '" class="ltg-slide-item-img">';
						$slide_html .= '</picture>';

						// ltg-slide-cover.
						$cover_style = self::slide_cover_style( $i );

						if ( $cover_style ) {
							$cover_style = ( $cover_style ) ? ' style="' . $cover_style . '"' : '';
							$slide_html .= '<div class="ltg-slide-cover"' . $cover_style . '></div>';
						}

						/*
						  mini_content
						/*-------------------------------------------*/
						$slide_html .= '<div class="ltg-slide-text-set mini-content">';

						$mini_content_args = array(
							'outer_class'    => 'mini-content-container-' . $i . ' container',
							'title_tag'      => 'h3',
							'title_class'    => 'ltg-slide-text-title',
							'caption_tag'    => 'div',
							'caption_class'  => 'ltg-slide-text-caption',
							'btn_class'      => 'btn btn-ghost',
							'btn_ghost'      => true,
							'btn_color_text' => '#333',
							'btn_color_bg'   => '#337ab7',
						);

						if ( ! empty( $options[ 'top_slide_text_color_' . $i ] ) ) {
							$mini_content_args['text_color'] = $options[ 'top_slide_text_color_' . $i ];
						}
						if ( ! empty( $options[ 'top_slide_text_align_' . $i ] ) ) {
							$mini_content_args['text_align'] = $options[ 'top_slide_text_align_' . $i ];
						}
						if ( ! empty( $options[ 'top_slide_text_shadow_use_' . $i ] ) ) {
							$mini_content_args['shadow_use'] = $options[ 'top_slide_text_shadow_use_' . $i ];
						}

						if ( ! empty( $options[ 'top_slide_text_shadow_color_' . $i ] ) ) {
							$mini_content_args['shadow_color'] = $options[ 'top_slide_text_shadow_color_' . $i ];
						}

						if ( ! empty( $options[ 'top_slide_text_title_' . $i ] ) ) {
							$mini_content_args['title_text'] = $options[ 'top_slide_text_title_' . $i ];
						}
						if ( ! empty( $options[ 'top_slide_text_caption_' . $i ] ) ) {
							$mini_content_args['caption_text'] = $options[ 'top_slide_text_caption_' . $i ];
						}
						if ( ! empty( $options[ 'top_slide_text_btn_' . $i ] ) ) {
							$mini_content_args['btn_text'] = $options[ 'top_slide_text_btn_' . $i ];
						}
						if ( ! empty( $options[ 'top_slide_url_' . $i ] ) ) {
							$mini_content_args['btn_url'] = $options[ 'top_slide_url_' . $i ];
						}
						if ( ! empty( $options[ 'top_slide_link_blank_' . $i ] ) ) {
							$mini_content_args['btn_target'] = '_blank';
						}
						if ( ! empty( $options[ 'top_slide_text_color_' . $i ] ) ) {
							$mini_content_args['btn_color_text'] = $options[ 'top_slide_text_color_' . $i ];
						}
						if ( ! empty( $options['color_key'] ) ) {
							$mini_content_args['btn_color_bg'] = $options['color_key'];
						}

						$slide_html .= VK_Component_Mini_Contents::get_view( $mini_content_args );

						$slide_html .= '</div><!-- .mini-content -->';

						if ( self::is_slide_outer_link( $i ) ) {
							$slide_html .= '</a>';
						}

						$slide_html .= '</div><!-- [ /.item ] -->';

					}
				}

				$slide_html .= '</div><!-- [ /.swiper-wrapper ] -->';
				if ( $slide_count >= 2 ) {
					// Add Pagination.
					$slide_html .= '<div class="swiper-pagination swiper-pagination-white"></div>';
					// Add Arrows.
					$slide_html .= '<div class="ltg-slide-button-next swiper-button-next swiper-button-white"></div>';
					$slide_html .= '<div class="ltg-slide-button-prev swiper-button-prev swiper-button-white"></div>';
				}

				$slide_html .= '</div><!-- [ /.swiper-container ] -->';

			}

			return $slide_html;
		}
	}
	new LTG_G3_Slider();
}
