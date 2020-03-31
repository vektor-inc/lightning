<?php
/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {

	class Vk_Font_Awesome_Versions {

		private static $version_default = '5_WebFonts_CSS';
		// public static $hook_point = apply_filters( 'vkfa_enqueue_point', 'wp_enqueue_scripts' );

		function __construct() {

			/**
			 * Reason of Using through the after_setup_theme is 
			 * to be able to change the action hook point of css load from theme..
			 */
			add_action( 'after_setup_theme', array( __CLASS__, 'load_css_action' ) );

			add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
			add_action( 'admin_init', array( __CLASS__, 'load_admin_font_awesome' ) );
			add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'load_gutenberg_font_awesome' ) );
			add_action( 'wp_head', array( __CLASS__, 'dynamic_css' ), 3 );
			add_filter( 'body_class', array( __CLASS__, 'add_body_class_fa_version' ) );
		}

		public static function load_css_action() {
			$hook_point = apply_filters( 'vkfa_enqueue_point', 'wp_enqueue_scripts' );
			add_action( $hook_point, array( __CLASS__, 'load_font_awesome' ) );
		}

		static function versions() {
			global $font_awesome_directory_uri;
			$versions = array(
				'5_SVG_JS'       => array(
					'label'   => '5 SVG with JS ( ' . __( 'Not recommended', 'lightning' ) . ' )',
					'version' => '5.11.2',
					'type'    => 'svg-with-js',
					/* [ Notice ] use editor css*/
					'url_css' => $font_awesome_directory_uri . 'versions/5/css/all.min.css',
					'url_js'  => $font_awesome_directory_uri . 'versions/5/js/all.min.js',
				),
				'5_WebFonts_CSS' => array(
					'label'   => '5 Web Fonts with CSS',
					'version' => '5.11.2',
					'type'    => 'web-fonts-with-css',
					'url_css' => $font_awesome_directory_uri . 'versions/5/css/all.min.css',
					'url_js'  => '',
				),
				'4.7'            => array(
					'label'   => '4.7 ( ' . __( 'Not recommended', 'lightning' ) . ' )',
					'version' => '4.7',
					'type'    => 'web-fonts-with-css',
					'url_css' => $font_awesome_directory_uri . 'versions/4.7.0/css/font-awesome.min.css',
					'url_js'  => '',
				),
			);
			return $versions;
		}

		public static function get_option_fa() {
			$current = get_option( 'vk_font_awesome_version', self::$version_default );
			if ( $current == '5.0_WebFonts_CSS' ) {
				update_option( 'vk_font_awesome_version', '5_WebFonts_CSS' );
				$current = '5_WebFonts_CSS';
			} elseif ( $current == '5.0_SVG_JS' ) {
				update_option( 'vk_font_awesome_version', '5_SVG_JS' );
				$current = '5_SVG_JS';
			}
			return $current;
		}

		public static function current_info() {
			$versions       = self::versions();
			$current_option = self::get_option_fa();
			$current_info   = $versions[ $current_option ];
			return $current_info;
		}

		public static function ex_and_link() {
			$current_option = self::get_option_fa();
			if ( $current_option == '5_WebFonts_CSS' || $current_option == '5_SVG_JS' ) {
				$ex_and_link = '<strong>Font Awesome 5</strong><br>' . __( 'Ex ) ', 'lightning' ) . 'far fa-file-alt [ <a href="//fontawesome.com/icons?d=gallery&m=free" target="_blank">Icon list</a> ]';
			} else {
				$ex_and_link = '<strong>Font Awesome 4.7</strong><br>' . __( 'Ex ) ', 'lightning' ) . 'fa-file-text-o [ <a href="//fontawesome.com/v4.7.0/icons/" target="_blank">Icon list</a> ]';
			}
			return $ex_and_link;
		}

		/**
		 * When use Font Awesome 4,7 then print 'fa '.
		 *
		 * @var strings;
		 */
		public static function print_fa() {
			$fa             = '';
			$current_option = self::get_option_fa();
			if ( $current_option == '4.7' ) {
				$fa = 'fa ';
			}
			return $fa;
		}

		static function load_font_awesome() {
			$current = self::current_info();
			if ( $current['type'] === 'svg-with-js' ) {
				wp_enqueue_script( 'vk-font-awesome-js', $current['url_js'], array(), $current['version'] );
				// [ Danger ] This script now causes important errors
				// wp_add_inline_script( 'font-awesome-js', 'FontAwesomeConfig = { searchPseudoElements: true };', 'before' );
			} else {
				wp_enqueue_style( 'vk-font-awesome', $current['url_css'], array(), $current['version'] );
			}
		}

		static function load_admin_font_awesome() {
			$current = self::current_info();
			add_editor_style( $current['url_css'] );
		}

		static function load_gutenberg_font_awesome() {
			$current_info = self::current_info();
			wp_enqueue_style( 'gutenberg-font-awesome', $current_info['url_css'], array(), $current_info['version'] );
		}

		/**
		 * add body class
		 *
		 * @return [type] [description]
		 */
		static function add_body_class_fa_version( $class ) {
			$current_option = self::get_option_fa();
			if ( $current_option == '4.7' ) {
				$class[] = 'fa_v4';
			} elseif ( $current_option == '5_WebFonts_CSS' ) {
				$class[] = 'fa_v5_css';
			} elseif ( $current_option == '5_SVG_JS' ) {
				$class[] = 'fa_v5_svg';
			}
			return $class;
		}

		/**
		 * Output dynbamic css according to Font Awesome versions
		 *
		 * @return [type] [description]
		 */
		static function dynamic_css() {
			$current     = self::get_option_fa();
			$dynamic_css = '';
			if ( $current == '4.7' ) {
				$dynamic_css = '.tagcloud a:before { font-family:FontAwesome;content:"\f02b"; }';
			} elseif ( $current == '5_WebFonts_CSS' ) {
				$dynamic_css = '.tagcloud a:before { font-family: "Font Awesome 5 Free";content: "\f02b";font-weight: bold; }';
			} elseif ( $current == '5_SVG_JS' ) {
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
			$current_option = self::get_option_fa();
			if ( $current_option == '5_WebFonts_CSS' || $current_option == '5_SVG_JS' ) {
				return $class_v5;
			} else {
				return $class_v4;
			}
		}

		/*
		  customize_register
		/*-------------------------------------------*/
		static function customize_register( $wp_customize ) {

			global $vk_font_awesome_version_prefix_customize_panel;

			$wp_customize->add_section(
				'VK Font Awesome',
				array(
					'title'    => $vk_font_awesome_version_prefix_customize_panel . __( 'Font Awesome', 'lightning' ),
					'priority' => 450,
				)
			);

			$wp_customize->add_setting(
				'vk_font_awesome_version',
				array(
					'default'           => '5_WebFonts_CSS',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			$versions = self::versions();
			foreach ( $versions as $key => $value ) {
				$choices[ $key ] = $value['label'];
			}

			$wp_customize->add_control(
				'vk_font_awesome_version',
				array(
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

	$vk_font_awesome_versions = new Vk_Font_Awesome_Versions;
	
} // if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {
