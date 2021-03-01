<?php
/**
 * VK Footer Customize Config
 *
 * @package VK Footer Customize
 */

if ( ! class_exists( 'VK_Footer_Customize' ) ) {
	global $vk_footer_customize_hook_style;
	$vk_footer_customize_hook_style = 'lightning-design-style';

	global $vk_footer_widget_area_count;
	$vk_footer_widget_area_count = 'lightning_footer_widget_area_count';

	global $vk_footer_selector;
	$vk_footer_selector = '.siteContent_after.sectionBox';

	require_once dirname( __FILE__ ) . '/package/class-vk-footer-customize.php';
}
global $vk_footer_customize_prefix;
$vk_footer_customize_prefix = lightning_get_prefix_customize_panel();

global $vk_footer_setting_name;
$vk_footer_setting_name = 'lightning_widget_setting';

global $vk_footer_customize_priority;
$vk_footer_customize_priority = 540;
