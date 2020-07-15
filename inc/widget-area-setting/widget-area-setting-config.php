<?php
/**
 * Widget Setting Config
 *
 * @package Lightning
 */

if ( ! class_exists( 'Widget_Area_Setting' ) ) {
	require_once 'package/class-widget-area-setting.php';

	global $vk_footer_customize_prefix;
	$vk_footer_customize_prefix = lightning_get_prefix_customize_panel();

	global $vk_footer_customize_priority;
	$vk_footer_customize_priority = 540;


}
