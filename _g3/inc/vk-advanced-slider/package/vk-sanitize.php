<?php
/**
 * Sanitize
 *
 * @package Katawara
 */

/**
 * Sanitize Number of Range
 *
 * @param int    $number number.
 * @param object $setting setting.
 */
function vk_sanitize_number_range( $number, $setting ) {
	$number = absint( $number );
	$atts   = $setting->manager->get_control( $setting->id )->input_attrs;
	$min    = ( isset( $atts['min'] ) ? $atts['min'] : $number );
	$max    = ( isset( $atts['max'] ) ? $atts['max'] : $number );
	$step   = ( isset( $atts['step'] ) ? $atts['step'] : 1 );
	return ( $min <= $number && $number <= $max && is_int( $number / $step ) ? $number : $setting->default );
}
