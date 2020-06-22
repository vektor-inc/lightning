<?php
/**
 * VK Old Options Notice Config
 *
 * @package Lightning
 */


	global $old_setting_array;
	$old_setting_array = array(
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'archive' => 'col-one'
				)
			),
			'callback'     => 'lightning_options_compatible',
		),
	);

	global $vk_update_link;
	$vk_update_link = 'lightning-update-link';

	require_once dirname( __FILE__ ) . '/package/class-vk-old-options-notice.php';

