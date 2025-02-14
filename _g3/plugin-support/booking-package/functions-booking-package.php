<?php
/**
 * Booking Package Extension
 *
 * @package         Lightning
 */

/*
	CSS読み込み
/*-------------------------------------------*/
function lightning_booking_package_load_css() {
	wp_enqueue_style( 'lightning-booking-package-style', get_template_directory_uri() . '/plugin-support/booking-package/css/style.css', array(), LIGHTNING_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'lightning_booking_package_load_css' );
