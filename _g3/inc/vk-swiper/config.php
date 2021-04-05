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
if ( isset( $options['top_slide_display'] ) && 'hide' === $options['top_slide_display'] ) {
	return;
}

if ( ! class_exists( 'VK_Swiper' ) ) {
	require_once dirname( __FILE__ ) . '/package/class-vk-swiper.php';
}

