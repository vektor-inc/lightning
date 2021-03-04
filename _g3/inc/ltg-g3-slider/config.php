<?php
/**
 * VK Advanced Slider Config
 *
 * @package Katawara
 */

if ( ! class_exists( 'LTG_G3_Slider' ) ) {

	global $slide_otion_name;
	$slide_otion_name = 'lightning_theme_options';
	/**
	 * Default Options
	 */
	function lightning_g3_slider_default_options() {
		$img_url = get_template_directory_uri() . '/assets/images/';
		// $options = lighting_get_options();
		$options = array(
			'top_slide_prefix'              => 'lightning_',
			'top_slide_display'             => 'display',
			'top_slide_time'                => 4000,
			'top_slide_image_1'             => get_template_directory_uri() . '/assets/images/top_image_1.jpg',
			'top_slide_image_mobile_1'      => '',
			'top_slide_alt_1'      			=> '',
			'top_slide_url_1'               => __( 'https://lightning.nagoya/', 'lightning' ),
			'top_slide_link_blank_1'        => false,
			'top_slide_text_title_1'        => __( 'Simple and Customize easy <br>WordPress theme.', 'lightning' ),
			'top_slide_text_caption_1'      => __( '100% GPL Lisence and adopting the bootstrap', 'lightning' ),
			'top_slide_text_btn_1'          => __( 'READ MORE', 'lightning' ),
			'top_slide_text_align_1'        => 'left',
			'top_slide_text_color_1'        => '#000',
			'top_slide_cover_color_1'       => '',
			'top_slide_cover_opacity_1'     => '',
			'top_slide_text_shadow_use_1'   => true,
			'top_slide_text_shadow_color_1' => '#fff',

			'top_slide_image_2'             => get_template_directory_uri() . '/assets/images/top_image_2.jpg',
			'top_slide_image_mobile_2'      => '',
			'top_slide_alt_2'      			=> '',
			'top_slide_url_2'               => esc_url( home_url() ),
			'top_slide_link_blank_2'        => false,
			'top_slide_text_title_2'        => __( 'Johnijirou On Snow', 'lightning' ),
			'top_slide_text_caption_2'      => __( 'Growing up everyday', 'lightning' ),
			'top_slide_text_btn_2'          => __( 'READ MORE', 'lightning' ),
			'top_slide_text_align_2'        => 'left',
			'top_slide_text_color_2'        => '#000',
			'top_slide_cover_color_2'       => '',
			'top_slide_cover_opacity_2'     => '',
			'top_slide_text_shadow_use_2'   => true,
			'top_slide_text_shadow_color_2' => '#fff',
		);
		$options = apply_filters( 'lightning_g3_slider_default_options', $options );
		return $options;
	}
	require_once dirname( __FILE__ ) . '/package/class-ltg-g3-slider.php';
}

global $vk_advansed_slider_prefix;
$vk_advansed_slider_prefix = 'Lightning ';
