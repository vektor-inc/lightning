<?php

function lightning_layout_target_array() {
	$array = array(
		'error404' => array(
			'function' => 'is_404',
		),
		'search'   => array(
			'function' => 'is_search',
		),
		'archive'  => array(
			'function' => 'is_archive',
		),
		'page'     => array(
			'function' => 'is_page',
		),
		'single'   => array(
			'function' => 'is_single',
		),
	);
	return $array;
}

/**
 * lightning_is_layout_onecolumn
 *
 * @since Lightning 9.0.0
 * @return boolean
 */
function lightning_is_layout_onecolumn() {
	$onecolumn = false;
	$options   = get_option( 'lightning_theme_options' );
	global $wp_query;

	$array = lightning_layout_target_array();

	foreach ( $array as $key => $value ) {
		if ( call_user_func( $value['function'] ) ) {
			if ( isset( $options['layout'][ $key ] ) ) {
				if ( $options['layout'][ $key ] === 'col-one' || $options['layout'][ $key ] === 'col-one-no-subsection' ) {
					$onecolumn = true;
				}
			}
		}
	}

	if ( is_front_page() ) {
		if ( isset( $options['layout']['front-page'] ) ) {

			if ( $options['layout']['front-page'] === 'col-one' || $options['layout']['front-page'] === 'col-one-no-subsection' ) {
				$onecolumn = true;
			}
		} else {
			$page_on_front_id = get_option( 'page_on_front' );
			if ( $page_on_front_id ) {
				$template = get_post_meta( $page_on_front_id, '_wp_page_template', true );
				if ( $template == 'page-onecolumn.php' ) {
					return true;
				}
			}
		}
	} elseif ( is_home() && ! is_front_page() ) {
		if ( isset( $options['layout']['archive'] ) ) {
			if ( $options['layout']['archive'] === 'col-one' || $options['layout']['archive'] === 'col-one-no-subsection' ) {
				$onecolumn = true;
			}
		}
	}

	if ( is_singular() ) {
		global $post;
		if ( is_page() ) {
			$template           = get_post_meta( $post->ID, '_wp_page_template', true );
			$template_onecolumn = array(
				'page-onecolumn.php',
				'page-lp.php',
			);
			if ( in_array( $template, $template_onecolumn ) ) {
				$onecolumn = true;
			}
		}
		if ( isset( $post->_lightning_design_setting['layout'] ) ) {
			if ( $post->_lightning_design_setting['layout'] === 'col-two' ) {
				$onecolumn = false;
			} elseif ( $post->_lightning_design_setting['layout'] === 'col-one-no-subsection' ) {
				$onecolumn = true;
			} elseif ( $post->_lightning_design_setting['layout'] === 'col-one' ) {
				$onecolumn = true;
			}
		}
	}
	return apply_filters( 'lightning_is_layout_onecolumn', $onecolumn );
}

/**
 * lightning_is_subsection_display
 *
 * @since Lightning 9.0.0
 * @return boolean
 */
function lightning_is_subsection_display() {
	$return  = true;
	$options = get_option( 'lightning_theme_options' );

	$array = lightning_layout_target_array();

	foreach ( $array as $key => $value ) {
		if ( call_user_func( $value['function'] ) ) {
			if ( isset( $options['layout'][ $key ] ) ) {
				if ( $options['layout'][ $key ] === 'col-one-no-subsection' ) {
					$onecolumn = false;
				}
			}
		}
	}

	// break and hidden
	if ( is_front_page() ) {
		if ( isset( $options['layout']['front-page'] ) &&
		$options['layout']['front-page'] === 'col-one-no-subsection'
		) {
			$return = false;
		}
	} elseif ( is_home() && ! is_front_page() ) {
		
		if ( isset( $options['layout']['archive'] ) ) {
			if ( $options['layout']['archive'] === 'col-one-no-subsection' ) {
				
				$return = false;
			}
		}

	} elseif ( is_singular() ) {
		if ( is_single() ) {
			if ( isset( $options['layout']['single'] ) &&
			$options['layout']['single'] === 'col-one-no-subsection' ) {
				$return = false;
			}
		}
		global $post;
		if ( isset( $post->_lightning_design_setting['layout'] ) ) {
			if ( $post->_lightning_design_setting['layout'] === 'col-one-no-subsection' ) {
				$return = false;
			} elseif ( $post->_lightning_design_setting['layout'] === 'col-two' ) {
				$return = true;
			}
		}
	}
	return apply_filters( 'lightning_is_subsection_display', $return );
}

/**
 * Page header and Breadcrumb Display or hidden
 *
 * @since Lightning 9.0.0
 * @return boolean
 */
function lightning_is_page_header_and_breadcrumb() {
	$return = true;
	if ( is_singular() ) {
		global $post;

		if ( ! empty( $post->_lightning_design_setting['hidden_page_header_and_breadcrumb'] ) ) {
			$return = false;
		}
	}
	return apply_filters( 'lightning_is_page_header_and_breadcrumb', $return );
}

function lightning_is_siteContent_padding_off() {
	if ( is_singular() ) {
		global $post;
		$cf = $post->_lightning_design_setting;
		if ( ! empty( $cf['siteContent_padding'] ) ) {
			return true;
		}
	}
}
