<?php
/**
 * VK Old Options Notice Config
 *
 * @package lightning
 */

/**
 * Logic of display warnning
 *
 * 1. option : get option automatically and check
 * 2. post meta and so on : compale last chack version and now check version
 */

if ( ! class_exists( 'VK_Old_Options_Notice' ) ) {

	// アップデート状況識別用のoption値保存名.
	global $vk_old_options_name;
	$vk_old_options_name = 'lightning_old_options';

	global $vk_update_link;
	$vk_update_link = 'lightning-update-link';

	// 現存する変更が必要な古い情報の配列.
	global $lightning_old_setting_array;
	$lightning_old_setting_array = array(
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
		array(
			'data_type'     => 'meta',
			'check_version' => '0.1.0',
			'callback'      => 'lightning_pageheader_and_breadcrumb_compatible',
		),
		array(
			'data_type'     => 'meta',
			'check_version' => '0.1.1',
			'callback'      => 'lightning_g2_template_compatible',
		),
	);

	require_once dirname( __FILE__ ) . '/package/class-vk-old-options-notice.php';
}

