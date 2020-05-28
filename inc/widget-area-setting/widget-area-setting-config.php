<?php
/**
 * Widget Setting Config
 *
 * @package Lightning
 */

if ( ! class_exists( 'Widget_Area_Setting' ) ) {
	require_once 'package/class-widget-area-setting.php';

	global $widget_area_setting_prefix;
	$widget_area_setting_prefix = lightning_get_prefix_customize_panel();

}
