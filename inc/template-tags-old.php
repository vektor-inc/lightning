<?php

function lightning_get_theme_name_customize_panel() {
	return lightning_get_prefix_customize_panel();
}

/**
 * lightning_is_frontpage_onecolumn() is already not used
 *
 * @return void
 */
function lightning_is_frontpage_onecolumn() {
	// global $lightning_theme_options;
	// $options          = $lightning_theme_options;
	// $options          = get_option( 'lightning_theme_options' );
	// $page_on_front_id = get_option( 'page_on_front' );

	// if ( isset( $options['top_sidebar_hidden'] ) && $options['top_sidebar_hidden'] ) {
	// 	return true;
	// }
	// if ( $page_on_front_id ) {
	// 	$template = get_post_meta( $page_on_front_id, '_wp_page_template', true );
	// 	if ( $template == 'page-onecolumn.php' ) {
	// 		return true;
	// 	}
	// }
	// return false;
}

/*
  Sanitize
/*-------------------------------------------*/

/*
	Add sanitize checkbox
/*-------------------------------------------*/
function lightning_sanitize_checkbox( $input ) {
	if ( class_exists( 'VK_Helpers' ) ) {
		return VK_Helpers::sanitize_checkbox( $input );
	}
}

function lightning_sanitize_number( $input ) {
	if ( class_exists( 'VK_Helpers' ) ) {
		return VK_Helpers::sanitize_number( $input );
	}
}

function lightning_sanitize_number_percentage( $input ) {
	if ( class_exists( 'VK_Helpers' ) ) {
		return VK_Helpers::sanitize_number_percentage( $input );
	}
}

function lightning_sanitize_radio( $input ) {
	if ( class_exists( 'VK_Helpers' ) ) {
		return VK_Helpers::sanitize_choice( $input );
	}
}

function lightning_sanitize_textarea( $input ) {
	if ( class_exists( 'VK_Helpers' ) ) {
		return VK_Helpers::sanitize_textarea( $input );
	}
}

function lightning_deactivate_plugin( $plugin_path ){
	if ( class_exists( 'VK_Helpers' ) ){
		VK_Helpers::deactivate_plugin( $plugin_path  );
	}
}

/**
 * Page header and Breadcrumb Display or hidden
 *
 * @since Lightning 9.0.0
 * @repeal Lightning 13.0.0
 * @return boolean
 */
function lightning_is_page_header_and_breadcrumb() {
	$return = true;
	if ( is_singular() ) {
		global $post;

		if ( ! empty( $post->_lightning_design_setting['hidden_page_header_and_breadcrumb'] ) ) {
			$return = false;
		}

		if ( 
		! empty( $post->_lightning_design_setting['hidden_page_header'] ) && 
		! empty( $post->_lightning_design_setting['hidden_breadcrumb'] ) 
		) {
			$return = false;
		}
	}
	return apply_filters( 'lightning_is_page_header_and_breadcrumb', $return );
}

function lightning_is_siteContent_padding_off() {
	$return = false;
	if ( is_singular() ) {
		global $post;
		if ( ! empty( $post->_lightning_design_setting['siteContent_padding'] ) ) {
			$return = true;
		}
	}
	return apply_filters( 'lightning_is_siteContent_padding_off', $return );
}
