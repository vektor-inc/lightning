<?php
/**
 * VK Slider Config
 *
 * @package vektor-inc/lightning
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
			'top_slide_alt_1'               => '',
			'top_slide_url_1'               => __( 'https://lightning.vektor-inc.co.jp/en/', 'lightning' ),
			'top_slide_link_blank_1'        => false,
			'top_slide_text_title_1'        => __( 'Accelerate your business', 'lightning' ),
			'top_slide_text_caption_1'      => __( 'Lorem ipsum dolor sit amet, consectetur <br>adipiscing elit,sed do eiusmod tempor.', 'lightning' ),
			'top_slide_text_btn_1'          => __( 'READ MORE', 'lightning' ),
			'top_slide_text_align_1'        => 'left',
			'top_slide_text_color_1'        => '#000',
			'top_slide_cover_color_1'       => '',
			'top_slide_cover_opacity_1'     => '',
			'top_slide_text_shadow_use_1'   => true,
			'top_slide_text_shadow_color_1' => '#fff',

			'top_slide_image_2'             => get_template_directory_uri() . '/assets/images/top_image_2.jpg',
			'top_slide_image_mobile_2'      => '',
			'top_slide_alt_2'               => '',
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

/**
 * Swiper の矢印・ページネーションの色調整
 *
 * Swiper は色を指定しないと矢印が既定の青色になるため、白に上書きする。
 * 矢印の色は以前からサイト全体（:root）に指定しているため、範囲を変えずに維持する。
 *
 * ページネーションは対象をトップページのスライダー（.ltg-slide）に限定する。
 * 白くする指定を :root に置くとサイト全体の Swiper に及び、色を指定していない
 * 他のスライダー（VK Blocks など）のドットまで既定の青から白に変わってしまい、
 * 白背景のページでは見づらくなる。この関数の目的と無関係な副作用になるため範囲を絞る。
 * カスタムプロパティは継承するので、.ltg-slide に置けば配下のドットに効く。
 *
 * --swiper-pagination-color はアクティブなドット（.swiper-pagination-bullet-active）と
 * プログレスバーの塗り（.swiper-pagination-progressbar-fill）の色になる。
 * 非アクティブなドットは別の変数（--swiper-pagination-bullet-inactive-color / -opacity）
 * で描画されるため、こちらも合わせて白にする。ドットはクリックで移動できる操作子
 * （pagination.clickable）なので、現在位置だけでなく全ドットが判別できる必要がある。
 * 既定の不透明度 20% では影まで薄まって明るい画像でも暗い画像でも判別できないため、
 * 半透明をやめて 1（不透明）にする。半透明では中間調の背景で 3:1 を割るのを避けられない。
 * 現在位置は不透明度差ではなく形の差で示す（components/_slide.scss を参照）。
 *
 * 明るい画像の上でも白が判別できるようにする影は overwrite/_swiper.scss で指定している。
 *
 * @since vk swiper composer version
 * @return void
 */
function lightning_add_swiper_adjustment_css() {
	$css  = ':root{--swiper-navigation-color: #fff;}';
	$css .= '.ltg-slide{--swiper-pagination-color: #fff;--swiper-pagination-bullet-inactive-color: #fff;--swiper-pagination-bullet-inactive-opacity: 1;}';
	wp_add_inline_style( 'lightning-common-style', $css );
}
add_action( 'wp_enqueue_scripts', 'lightning_add_swiper_adjustment_css', 11 );
