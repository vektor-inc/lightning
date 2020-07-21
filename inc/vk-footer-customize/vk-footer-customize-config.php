<?php
/**
 * Footer Customize Config
 *
 * @package Lightning Pro
 */

if ( ! class_exists( 'VK_Footer_Customize' ) ) {
	global $vk_footer_customize_hook_style;
	$vk_footer_customize_hook_style = 'lightning-design-style';

	require_once dirname( __FILE__ ) . '/package/class-widget-area-setting.php';
}
global $vk_footer_customize_prefix;
$vk_footer_customize_prefix = lightning_get_prefix_customize_panel();

global $vk_footer_customize_priority;
$vk_footer_customize_priority = 540;
