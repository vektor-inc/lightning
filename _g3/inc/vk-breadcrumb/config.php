<?php // phpcs:ignore
/**
 * VK_Breadcrumb fall back alias
 *
 * VK_Breadcrumb is instead to VkBreadcrumb composer lib.
 * But some user probably using VK_Breadcrumb in childe themes that
 * this is fall back alias
 *
 * Original file is bellow
 * lightning/vendor/vektor-inc/vk-breadcrumb/src/VkBreadcrumb.php
 *
 *  @since 14.16.2
 */

use VektorInc\VK_Breadcrumb\VkBreadcrumb;

if ( ! class_exists( 'VK_Breadcrumb' ) ) {

	/**
	 * VK_Breadcrumb alias
	 */
	class VK_Breadcrumb extends VkBreadcrumb {

	}

}
