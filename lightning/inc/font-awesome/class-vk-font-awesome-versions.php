<?php

if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {

	class Vk_Font_Awesome_Versions {
		static function init() {
			add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_font_awesome' ) );
			add_action( 'admin_init', array( __CLASS__, 'load_admin_font_awesome' ) );
			// add_action( 'wp_head', array( __CLASS__, 'edit_icon_css_change' ), 3 );
		}

		static function versions() {
			global $font_awesome_directory_uri;
			$versions = array(
				'5.0_SVG_JS'       => array(
					'label'   => '5.0 SVG with JS',
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

		static function current_info() {
			$versions                = self::versions();
			$vk_font_awesome_version = get_option( 'vk_font_awesome_version', '4.7' );
			$current_info            = $versions[ $vk_font_awesome_version ];
			return $current_info;
		}

		static public function ex_and_link() {
			global $vk_font_awesome_version_textdomain;
			$current = self::current_info();
			if ( $current['version'] == '5.0' ) {
				$ex_and_link = '<strong>Font Awesome 5</strong><br>' . __( 'Ex ) ', $vk_font_awesome_version_textdomain ) . 'far fa-file-alt [ <a href="//fontawesome.com/icons?d=gallery&m=free" target="_blank">Icon list</a> ]';
			} else {
				$ex_and_link = '<strong>Font Awesome 4.7</strong><br>' . __( 'Ex ) ', $vk_font_awesome_version_textdomain ) . 'fa-file-text-o [ <a href="//fontawesome.com/v4.7.0/icons/" target="_blank">Icon list</a> ]';
			}
			return $ex_and_link;
		}

		/**
		 * When use Font Awesome 4,7 then print 'fa '.
		 * @var strings;
		 */
		static public function print_fa() {
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

		static function edit_icon_css_change() {
			// $current     = self::current_info();
			// $dynamic_css = '/* font-awesome */';
			// if ( $current['version'] == '5.0' ) {
			// 	$dynamic_css .= '.veu_adminEdit a.btn:before{content:"\f044";font-family:Font Awesome\ 5 Free;}';
			// } else {
			// 	$dynamic_css .= '.veu_adminEdit a.btn:before{content:"\f040";font-family:FontAwesome;}';
			// }
			// // delete before after space
			// $dynamic_css = trim( $dynamic_css );
			// // convert tab and br to space
			// $dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
			// // Change multiple spaces to single space
			// $dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
			// wp_add_inline_style( 'vkExUnit_common_style', $dynamic_css );
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

			global $vk_font_awesome_version_textdomain;

			$wp_customize->add_section(
				'VK Font Awesome', array(
					'title' => __( 'Font Awesome', $vk_font_awesome_version_textdomain ),
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
					'label'       => __( 'Font Awesome Version', $vk_font_awesome_version_textdomain ),
					'section'     => 'VK Font Awesome',
					'settings'    => 'vk_font_awesome_version',
					'description' => __( '4.7 will be abolished in the near future.', $vk_font_awesome_version_textdomain ),
					'type'        => 'select',
					'priority'    => '',
					'choices'     => $choices,
				)
			);
		} // static function customize_register( $wp_customize ) {

	} // Vk_Font_Awesome_Versions
	Vk_Font_Awesome_Versions::init();
} // if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {
