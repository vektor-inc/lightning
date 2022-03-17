<?php
/**
 * Font Awesome Load modules
 *
 * @package vektor-inc/lightning
 */

use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;

new VkFontAwesomeVersions();
global $font_awesome_directory_uri;
// phpcs:ignore
global $font_awesome_directory_uri;
$template                   = 'lightning';
$theme_root_uri             = get_theme_root_uri( $template );
$font_awesome_directory_uri = "$theme_root_uri/$template/vendor/vektor-inc/font-awesome-versions/src/";

global $vk_font_awesome_version_prefix_customize_panel;
$vk_font_awesome_version_prefix_customize_panel = 'Lightning ';

global $set_enqueue_handle_style;
$set_enqueue_handle_style = 'lightning-design-style';

global $vk_font_awesome_version_priority;
$vk_font_awesome_version_priority = 560;
