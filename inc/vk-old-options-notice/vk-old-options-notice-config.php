<?php
/**
 * VK Old Options Notice Config
 *
 * @package Lightning
 */

if ( ! class_exists( 'VK_Old_Options_Notice' ) ) {
	global $old_setting_array;
	$old_setting_array = array(
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'archive' => 'col-one',
				),
			),
			'callback'     => 'lightning_options_compatible',
		),
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'archive' => 'col-two',
				),
			),
			'callback'     => 'lightning_options_compatible',
		),
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'archive' => 'col-one-no-subsection',
				),
			),
			'callback'     => 'lightning_options_compatible',
		),
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'single' => 'col-one',
				),
			),
			'callback'     => 'lightning_options_compatible',
		),
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'single' => 'col-two',
				),
			),
			'callback'     => 'lightning_options_compatible',
		),
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'single' => 'col-one-no-subsection',
				),
			),
			'callback'     => 'lightning_options_compatible',
		),
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'page' => 'col-one',
				),
			),
			'callback'     => 'lightning_options_compatible',
		),
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'page' => 'col-two',
				),
			),
			'callback'     => 'lightning_options_compatible',
		),
		array(
			'data_type'    => 'option',
			'target_field' => 'lightning_theme_options',
			'old_value'    => array(
				'layout' => array(
					'page' => 'col-one-no-subsection',
				),
			),
			'callback'     => 'lightning_options_compatible',
		),
	);

	global $vk_update_link;
	$vk_update_link = 'lightning-update-link';

	require_once dirname( __FILE__ ) . '/package/class-vk-old-options-notice.php';
}

