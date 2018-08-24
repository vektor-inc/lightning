<?php

if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {

	class Vk_Font_Awesome_Versions {

		private static $version_default = '5.0_WebFonts_CSS';

		static function init() {
			add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_font_awesome' ), 3 );
			add_action( 'admin_init', array( __CLASS__, 'load_admin_font_awesome' ) );
			add_action( 'wp_head', array( __CLASS__, 'dynamic_css' ), 3 );
			add_filter( 'body_class', array( __CLASS__, 'add_body_class_fa_version' ) );
		}

		static function versions() {
			global $font_awesome_directory_uri;
			$versions = array(
				'5.0_SVG_JS'       => array(
					'label'   => '5.0 SVG with JS ( ' . __( 'Not recommended', 'lightning' ) . ' )',
					'version' => '5.0',
					'type'    => 'svg-with-js',
					'url_css' => $font_awesome_directory_uri . 'versions/5.0.13/web-fonts-with-css/css/fontawesome-all.min.css',
					'url_js'  => $font_awesome_directory_uri . 'versions/5.0.13/svg-with-js/js/fontawesome-all.min.js',
				),
				'5.0_WebFonts_CSS' => array(
					'label'   => '5.0 Web Fonts with CSS',
					'version' => '5.0',
					'type'    => 'web-fonts-with-css',
					'url_css' => $font_awesome_directory_uri . 'versions/5.0.13/web-fonts-with-css/css/fontawesome-all.min.css',
					'url_js'  => '',
				),
				'4.7'              => array(
					'label'   => '4.7',
					'version' => '4.7',
					'type'    => 'web-fonts-with-css',
					'url_css' => $font_awesome_directory_uri . 'versions/4.7.0/css/font-awesome.min.css',
					'url_js'  => '',
				),
			);
			return $versions;
		}

		public static function current_info() {
			$versions                = self::versions();
			$vk_font_awesome_version = get_option( 'vk_font_awesome_version', self::$version_default );
			$current_info            = $versions[ $vk_font_awesome_version ];
			return $current_info;
		}

		public static function ex_and_link() {
			$current = self::current_info();
			if ( $current['version'] == '5.0' ) {
				$ex_and_link = '<strong>Font Awesome 5</strong><br>' . __( 'Ex ) ', 'lightning' ) . 'far fa-file-alt [ <a href="//fontawesome.com/icons?d=gallery&m=free" target="_blank">Icon list</a> ]';
			} else {
				$ex_and_link = '<strong>Font Awesome 4.7</strong><br>' . __( 'Ex ) ', 'lightning' ) . 'fa-file-text-o [ <a href="//fontawesome.com/v4.7.0/icons/" target="_blank">Icon list</a> ]';
			}
			return $ex_and_link;
		}

		/**
		 * When use Font Awesome 4,7 then print 'fa '.
		 * @var strings;
		 */
		public static function print_fa() {
			$fa                   = '';
			$font_awesome_current = self::current_info();
			if ( $font_awesome_current['version'] == '4.7' ) {
				$fa = 'fa ';
			}
			return $fa;
		}

		static function load_font_awesome() {
			$current = self::current_info();
			if ( $current['type'] === 'svg-with-js' ) {
				wp_enqueue_script( 'font-awesome-js', $current['url_js'], array(), $current['version'] );
				// [ Danger ] This script now causes important errors
				// wp_add_inline_script( 'font-awesome-js', 'FontAwesomeConfig = { searchPseudoElements: true };', 'before' );
			} else {
				wp_enqueue_style( 'font-awesome', $current['url_css'], array(), $current['version'] );
			}
		}

		static function load_admin_font_awesome() {
			$current = self::current_info();
			if ( ! $current['type'] === 'web-fonts-with-css' ) {
				add_editor_style( $current['css'] );
			}
		}

		/**
	 * add body class
	 * @return [type] [description]
	 */
		static function add_body_class_fa_version( $class ) {
			$current = get_option( 'vk_font_awesome_version', self::$version_default );
			if ( $current == '4.7' ) {
				$class[] = 'fa_v4';
			} elseif ( $current == '5.0_WebFonts_CSS' ) {
				$class[] = 'fa_v5_css';
			} elseif ( $current == '5.0_SVG_JS' ) {
				$class[] = 'fa_v5_svg';
			}
			return $class;
		}

		/**
		 * Output dynbamic css according to Font Awesome versions
		 * @return [type] [description]
		 */
		static function dynamic_css() {
			$current     = get_option( 'vk_font_awesome_version', self::$version_default );
			$dynamic_css = '';
			if ( $current == '4.7' ) {
				$dynamic_css = '.tagcloud a:before { font-family:FontAwesome;content:"\f02b"; }';
			} elseif ( $current == '5.0_WebFonts_CSS' ) {
				$dynamic_css = '.tagcloud a:before { font-family: "Font Awesome 5 Free";content: "\f02b";font-weight: bold; }';
			} elseif ( $current == '5.0_SVG_JS' ) {
				$dynamic_css = '.tagcloud a:before { content:"" }';
			}
			// delete before after space
			$dynamic_css = trim( $dynamic_css );
			// convert tab and br to space
			$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
			// Change multiple spaces to single space
			$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );

			global $set_enqueue_handle_style;
			wp_add_inline_style( $set_enqueue_handle_style, $dynamic_css );
		}

		public static function class_switch( $class_v4 = '', $class_v5 = '' ) {
			$current = self::current_info();
			if ( $current['version'] == '5.0' ) {
				return $class_v5;
			} else {
				return $class_v4;
			}
		}

		/*-------------------------------------------*/
		/*	customize_register
		/*-------------------------------------------*/
		static function customize_register( $wp_customize ) {

			global $vk_font_awesome_version_prefix;

			$wp_customize->add_section(
				'VK Font Awesome', array(
					'title'    => $vk_font_awesome_version_prefix . __( 'Font Awesome', 'lightning' ),
					'priority' => 450,
				)
			);

			$wp_customize->add_setting(
				'vk_font_awesome_version', array(
					'default'           => '4.7',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$versions = Vk_Font_Awesome_Versions::versions();
			foreach ( $versions as $key => $value ) {
				$choices[ $key ] = $value['label'];
			}

			$wp_customize->add_control(
				'vk_font_awesome_version', array(
					'label'       => __( 'Font Awesome Version', 'lightning' ),
					'section'     => 'VK Font Awesome',
					'settings'    => 'vk_font_awesome_version',
					'description' => __( '4.7 will be abolished in the near future.', 'lightning' ),
					'type'        => 'select',
					'priority'    => '',
					'choices'     => $choices,
				)
			);
		} // static function customize_register( $wp_customize ) {

	} // Vk_Font_Awesome_Versions
	Vk_Font_Awesome_Versions::init();
} // if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {
