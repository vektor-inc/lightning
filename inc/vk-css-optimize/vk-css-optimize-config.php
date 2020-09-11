<?php
/**
 * VK CSS Tree Shaking Config
 *
 * @package Lightning
 */

require_once dirname( __FILE__ ) . '/vk-css-optimize-admin.php';

/**
 * Optimize CSS.
 */
function lightning_optimize_css() {
	$options = get_option( 'lightning_theme_options' );

	if ( ! isset( $options['optimize_css'] ) ) {
		$options['optimize_css'] = 'minimal-bootstrap';
	} elseif ( 'optomize-all-css' === $options['optimize_css'] ) {
		$options['optimize_css'] = 'tree-shaking';
		update_option( 'lightning_theme_options', $options );
	}

	if ( ! empty( $options['optimize_css'] ) && ( 'optomize-all-css' === $options['optimize_css'] || 'tree-shaking' === $options['optimize_css'] ) ) {

		global $bootstrap;
		$skin_info = Lightning_Design_Manager::get_current_skin();

		$skin_css_url  = ! empty( $skin_info['css_path'] ) ? $skin_info['css_path'] : '';
		$skin_css_path = ! empty( $skin_info['css_sv_path'] ) ? $skin_info['css_sv_path'] : '';
		$skin_version  = ! empty( $skin_info['version'] ) ? $skin_info['version'] : '';

		$bs4_css_url  = ( ! empty( $skin_info['bootstrap'] ) && 'bs4' === $skin_info['bootstrap'] ) ? get_template_directory_uri() . '/library/bootstrap-4/css/bootstrap.min.css' : '';
		$bs4_css_path = ( ! empty( $skin_info['bootstrap'] ) && 'bs4' === $skin_info['bootstrap'] ) ? get_parent_theme_file_path( '/library/bootstrap-4/css/bootstrap.min.css' ) : '';
		$bs4_version  = ( ! empty( $skin_info['bootstrap'] ) && 'bs4' === $skin_info['bootstrap'] ) ? '4.5.0' : '';

		// 表示位置の配列.
		global $vk_css_tree_shaking_array;
		if ( empty( $vk_css_tree_shaking_array ) ) {
			$vk_css_tree_shaking_array = array(
				array(
					'id'      => 'lightning-common-style',
					'url'     => get_template_directory_uri() . '/assets/css/common.css',
					'path'    => get_parent_theme_file_path( '/assets/css/common.css' ),
					'version' => LIGHTNING_THEME_VERSION,
				),
			);
		} else {
			$add_array = array(
				'id'      => 'lightning-common-style',
				'url'     => get_template_directory_uri() . '/assets/css/common.css',
				'path'    => get_parent_theme_file_path( '/assets/css/common.css' ),
				'version' => LIGHTNING_THEME_VERSION,
			);
			array_push( $vk_css_tree_shaking_array, $add_array );
		}

		if ( ! empty( $bs4_css_url ) && ! empty( $bs4_version ) ) {
			$add_array = array(
				'id'      => 'bootstrap-4-style',
				'url'     => $bs4_css_url,
				'path'    => $bs4_css_path,
				'version' => $bs4_version,
			);
			array_push( $vk_css_tree_shaking_array, $add_array );
		}
		if ( ! empty( $skin_css_url ) && ! empty( $skin_version ) ) {
			$add_array = array(
				'id'      => 'lightning-design-style',
				'url'     => $skin_css_url,
				'path'    => $skin_css_path,
				'version' => $skin_version,
			);
			array_push( $vk_css_tree_shaking_array, $add_array );
		}
		$vk_css_tree_shaking_array = apply_filters( 'vk_css_tree_shaking_array', $vk_css_tree_shaking_array );
		if ( ! class_exists( 'VK_CSS_Optimize' ) ) {
			require_once dirname( __FILE__ ) . '/package/class-vk-css-optimize.php';
		}
	}
}
add_action( 'after_setup_theme', 'lightning_optimize_css' );


function lightning_css_tree_shaking_exclude( $inidata ) {
	$options               = get_option( 'lightning_theme_options' );
	$exclude_classes_array = array();
	if ( ! empty( $options['tree_shaking_class_exclude'] ) ) {
		// delete before after space
		$exclude_clssses = trim( $options['tree_shaking_class_exclude'] );
		// convert tab and br to space
		$exclude_clssses = preg_replace( '/[\n\r\t]/', '', $exclude_clssses );
		// Change multiple spaces to single space
		$exclude_clssses       = preg_replace( '/\s/', '', $exclude_clssses );
		$exclude_clssses       = str_replace( '，', ',', $exclude_clssses );
		$exclude_clssses       = str_replace( '、', ',', $exclude_clssses );
		$exclude_classes_array = explode( ',', $exclude_clssses );
	}

	$inidata['class'] = array_merge( $inidata['class'], $exclude_classes_array );

	return $inidata;
}
add_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude' );

/**
 * CSS Tree Shaking Exclude
 *
 * @param array $inidata CSS Tree Shaking Exclude Paramator.
 */
function lightning_css_tree_shaking_exclude_class( $inidata ) {
	$exclude_classes_array = array(
		'customize-partial-edit-shortcut',
		'vk_post',
		'card',
		'vk_post-col-xs-12',
		'vk_post-col-xs-6',
		'vk_post-col-xs-4',
		'vk_post-col-xs-3',
		'vk_post-col-xs-2',
		'vk_post-col-sm-12',
		'vk_post-col-sm-6',
		'vk_post-col-sm-4',
		'vk_post-col-sm-3',
		'vk_post-col-sm-2',
		'vk_post-col-lg-12',
		'vk_post-col-lg-6',
		'vk_post-col-lg-4',
		'vk_post-col-lg-3',
		'vk_post-col-lg-2',
		'vk_post-col-xl-12',
		'vk_post-col-xl-6',
		'vk_post-col-xl-4',
		'vk_post-col-xl-3',
		'vk_post-col-xl-2',
		'vk_post-btn-display',
	);
	$inidata['class']      = array_merge( $inidata['class'], $exclude_classes_array );

	return $inidata;
}
add_filter( 'css_tree_shaking_exclude', 'lightning_css_tree_shaking_exclude_class' );
