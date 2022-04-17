<?php
/**
 * Font Awesome Load modules
 *
 * @package vektor-inc/lightning
 */

use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;
if ( method_exists( 'VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions', 'init' ) ) {
	VkFontAwesomeVersions::init();
}

global $vkfav_customize_panel_prefix;
$vkfav_customize_panel_prefix = 'Lightning ';

global $vkfav_customize_panel_priority;
$vkfav_customize_panel_priority = 560;

global $vkfav_set_enqueue_handle_style;
$vkfav_set_enqueue_handle_style = 'lightning-design-style';
