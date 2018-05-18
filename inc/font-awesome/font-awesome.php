<?php

if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {

	class Vk_Font_Awesome_Versions {

		static function init() {
			add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
			add_action( 'wp_enqueue_scripts', array( __CLASS__, 'load_font_awesome' ) );
			add_action( 'admin_init', array( __CLASS__, 'load_admin_font_awesome' ) );
		}

		static public function version_info() {
			global $font_awesome_directory_uri;
			$font_awesome_directory_uri = get_template_directory_uri() . '/inc/font-awesome/';
			$vk_font_awesome_version    = get_option( 'vk_font_awesome_version', '4.7' );
			$version_array              = array(
				'5.0' => $font_awesome_directory_uri . 'versions/5.0.13/web-fonts-with-css/css/fontawesome-all.min.css',
				'4.7' => $font_awesome_directory_uri . 'versions/4.7.0//css/font-awesome.min.css',
			);
			$version_info['version']    = $vk_font_awesome_version;
			$version_info['url']        = $version_array[ $vk_font_awesome_version ];
			return $version_info;
		}

		static function load_font_awesome() {
			$version_info = $this->version_info();
			wp_enqueue_style( 'font-awesome', $version_info['url'], array(), $version_info['version'] );
		}

		static function load_admin_font_awesome() {
			$version_info = $this->version_info();
			add_editor_style( $version_info['url'] );
		}

		/*-------------------------------------------*/
		/*	customize_register
		/*-------------------------------------------*/
		static function customize_register( $wp_customize ) {

			$wp_customize->add_section(
				'VK Font Awesome', array(
					'title' => __( 'Font Awesome', 'lightning' ),
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

			$wp_customize->add_control(
				'vk_font_awesome_version', array(
					'label'       => __( 'Font Awesome Version', 'lightning' ),
					'section'     => 'VK Font Awesome',
					'settings'    => 'vk_font_awesome_version',
					'description' => __( '4.7 will be abolished in the near future.', 'lightning' ),
					'type'        => 'select',
					'priority'    => '',
					'choices'     => array(
						'5.0' => '5.0 (' . __( 'Recommended', 'lightning' ) . ')',
						'4.7' => '4.7',
					),
				)
			);
		} // static function customize_register( $wp_customize ) {

	} // Vk_Font_Awesome_Versions
	Vk_Font_Awesome_Versions::init();
} // if ( ! class_exists( 'Vk_Font_Awesome_Versions' ) ) {
