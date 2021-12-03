<?php
/**
 * VK Swiper Config
 *
 * @package Lightning G3
 */

/**
 * Swiper is used only on the front page.
 * If you do not use the slider on the front page, Swiper will not be loaded.
 */

$options = get_option( 'lightning_theme_options' );
if ( ! empty( $options['top_slide_display'] ) && 'hide' === $options['top_slide_display'] ) {
	return;
}

if ( ! class_exists( 'VK_Swiper' ) ) {
	global $vk_swiper_url;
	global $vk_swiper_path;
	$vk_swiper_url  = get_parent_theme_file_uri( 'inc/vk-swiper/package/' );
	$vk_swiper_path = get_parent_theme_file_path( 'inc/vk-swiper/package/' );
	require_once dirname( __FILE__ ) . '/package/class-vk-swiper.php';
	// Load Swiper js and css (all pages).
	$vk_swiper = new VK_Swiper();
	// enqueue_swiper がないバージョンの vk swiper は 内部で enqueue している.
	if ( method_exists( $vk_swiper, 'enqueue_swiper' ) ) {
		$vk_swiper->enqueue_swiper();
	}
}

/**
 * Enqueue swiper scripts
 *
 * vk_blocksで設定したload_separate_optionが設定されトップページスライドショーがオンの時swiperを読み込む
 */
function lightning_enqueue_swiper() {
	// $options = get_option( 'vk_blocks_options' );
	if ( is_front_page() || isset( $options['load_separate_option'] ) ) {
		if ( apply_filters( 'lightning_default_slide_display', true ) ) {
			wp_enqueue_style( 'vk-swiper-style' );
			wp_enqueue_script( 'vk-swiper-script' );
		}
	}
}
add_action( 'wp_enqueue_scripts', 'lightning_enqueue_swiper' );
