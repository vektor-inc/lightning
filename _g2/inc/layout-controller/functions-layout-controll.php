<?php
/**
 * Layout Controller of Lightning.
 *
 * @package Lightning
 */

/**
 * Array of Layout Taeget
 */
function lightning_layout_target_array() {
	$array = array(
		'error404'       => array(
			'function' => 'is_404',
		),
		'search'         => array(
			'function' => 'is_search',
		),
		'archive-author' => array(
			'function' => 'is_author',
		),
	);
	return $array;
}

/**
 * lightning_layout_by_single
 *
 *  @since Lightning 14.3.3
 *  @return false / col-two / col-one / col-one-no-subsection
 */
function lightning_layout_by_single() {
	$layout = false;
	if ( is_singular() ) {
		global $post;
		if ( is_page() ) {
			$template           = get_post_meta( $post->ID, '_wp_page_template', true );
			$template_onecolumn = array(
				'page-onecolumn.php',
				'page-lp.php',
			);
			if ( in_array( $template, $template_onecolumn, true ) ) {
				$layout = 'col-one';
			}
		}
		if ( isset( $post->_lightning_design_setting['layout'] ) ) {
			if ( 'col-two' === $post->_lightning_design_setting['layout'] ) {
				$layout = 'col-two';
			} elseif ( 'col-one-no-subsection' === $post->_lightning_design_setting['layout'] ) {
				$layout = 'col-one-no-subsection';
			} elseif ( 'col-one' === $post->_lightning_design_setting['layout'] ) {
				$layout = 'col-one';
			}
		}
	}
	return $layout;
}

/**
 * Lightning Is Layout One Column
 *
 * @since Lightning 9.0.0
 * @return boolean
 */
function lightning_is_layout_onecolumn() {
	$onecolumn = false;
	$options   = get_option( 'lightning_theme_options' );
	global $wp_query;

	$array = lightning_layout_target_array();

	$additional_post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'names'
	);

	foreach ( $array as $key => $value ) {
		if ( call_user_func( $value['function'] ) ) {
			if ( isset( $options['layout'][ $key ] ) ) {
				if ( 'col-one' === $options['layout'][ $key ] || 'col-one-no-subsection' === $options['layout'][ $key ] ) {
					$onecolumn = true;
				}
			}
		}
	}

	// show_on_front 'page' case
	if ( is_front_page() && ! is_home() ) {
		if ( isset( $options['layout']['front-page'] ) ) {
			if ( 'col-one' === $options['layout']['front-page'] || 'col-one-no-subsection' === $options['layout']['front-page'] ) {
				$onecolumn = true;
			}
		} else {
			$page_on_front_id = get_option( 'page_on_front' );
			if ( $page_on_front_id ) {
				$template = get_post_meta( $page_on_front_id, '_wp_page_template', true );
				if ( 'page-onecolumn.php' === $template || 'page-lp.php' === $template ) {
					$onecolumn = true;
				}
			}
		}
		if ( 'col-one' === lightning_layout_by_single() || 'col-one-no-subsection' === lightning_layout_by_single() ) {
			$onecolumn = true;
		}
		// show_on_front 'posts' case
	} elseif ( is_front_page() && is_home() ) {
		if ( isset( $options['layout']['front-page'] ) ) {
			if ( 'col-one' === $options['layout']['front-page'] || 'col-one-no-subsection' === $options['layout']['front-page'] ) {
				$onecolumn = true;
			} elseif ( isset( $options['layout']['archive-post'] ) ) {
				if ( 'col-one' === $options['layout']['archive-post'] || 'col-one-no-subsection' === $options['layout']['archive-post'] ) {
					$onecolumn = true;
				}
			}
		}
	} elseif ( ! is_front_page() && is_home() ) {
		if ( isset( $options['layout']['archive-post'] ) ) {
			if ( 'col-one' === $options['layout']['archive-post'] || 'col-one-no-subsection' === $options['layout']['archive-post'] ) {
				$onecolumn = true;
			}
		}
	} elseif ( is_archive() && ! is_search() && ! is_author() ) {
		$current_post_type_info = lightning_get_post_type();
		$archive_post_types     = array( 'post' ) + $additional_post_types;
		foreach ( $archive_post_types as $archive_post_type ) {
			if ( isset( $options['layout'][ 'archive-' . $archive_post_type ] ) && $current_post_type_info['slug'] === $archive_post_type ) {
				if ( 'col-one' === $options['layout'][ 'archive-' . $archive_post_type ] || 'col-one-no-subsection' === $options['layout'][ 'archive-' . $archive_post_type ] ) {
					$onecolumn = true;
				}
			}
		}
	} elseif ( is_singular() ) {
		$single_post_types = array( 'post', 'page' ) + $additional_post_types;
		foreach ( $single_post_types as $single_post_type ) {
			if ( isset( $options['layout'][ 'single-' . $single_post_type ] ) && get_post_type() === $single_post_type ) {
				if ( 'col-one' === $options['layout'][ 'single-' . $single_post_type ] || 'col-one-no-subsection' === $options['layout'][ 'single-' . $single_post_type ] ) {
					$onecolumn = true;
				}
			}
		}

		$lightning_layout_by_single = lightning_layout_by_single();
		if ( $lightning_layout_by_single ) {
			if ( 'col-one' === $lightning_layout_by_single || 'col-one-no-subsection' === $lightning_layout_by_single ) {
				$onecolumn = true;
			} else {
				$onecolumn = false;
			}
		}
	}
	return apply_filters( 'lightning_is_layout_onecolumn', $onecolumn );
}

/**
 * Lightning Is Subsection Display
 *
 * @since Lightning 9.0.0
 * @return boolean
 */
function lightning_is_subsection_display() {
	global $post;
	$return  = true;
	$options = lightning_get_theme_options();

	$array = lightning_layout_target_array();

	foreach ( $array as $key => $value ) {
		if ( call_user_func( $value['function'] ) ) {
			if ( isset( $options['layout'][ $key ] ) ) {
				if ( 'col-one-no-subsection' === $options['layout'][ $key ] ) {
					$return = false;
				}
			}
		}
	}

	$additional_post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'names'
	);
	// break and hidden.
	if ( is_front_page() && ! is_home() ) {

		if ( isset( $options['layout']['front-page'] ) && 'col-one-no-subsection' === $options['layout']['front-page'] ) {
			$return = false;
		}
		if ( is_page() ) {
			$template           = get_post_meta( $post->ID, '_wp_page_template', true );
			$template_onecolumn = array(
				'page-onecolumn.php',
				'page-lp.php',
			);
			if ( in_array( $template, $template_onecolumn, true ) ) {
				$return = false;
			}
			if ( isset( $post->_lightning_design_setting['layout'] ) ) {

				if ( 'col-one-no-subsection' === $post->_lightning_design_setting['layout'] ) {
					$return = false;
				} elseif ( 'col-two' === $post->_lightning_design_setting['layout'] ) {
					$return = true;
				} elseif ( 'col-one' === $post->_lightning_design_setting['layout'] ) {
					/* 1 column but subsection is exist */
					$return = true;
				}
			}
		}
	} elseif ( is_front_page() && is_home() ) {
		if ( isset( $options['layout']['front-page'] ) && 'col-one-no-subsection' === $options['layout']['front-page'] ) {
			$return = false;
		} elseif ( isset( $options['layout']['archive-post'] ) && 'col-one-no-subsection' === $options['layout']['archive-post'] ) {
			$return = false;
		}
	} elseif ( ! is_front_page() && is_home() ) {
		if ( isset( $options['layout']['archive-post'] ) && 'col-one-no-subsection' === $options['layout']['archive-post'] ) {
			$return = false;
		}
	} elseif ( is_archive() && ! is_search() && ! is_author() ) {
		$current_post_type_info = lightning_get_post_type();
		$archive_post_types     = array( 'post' ) + $additional_post_types;
		foreach ( $archive_post_types as $archive_post_type ) {

			if ( isset( $options['layout'][ 'archive-' . $archive_post_type ] ) && $current_post_type_info['slug'] === $archive_post_type ) {
				if ( 'col-one-no-subsection' === $options['layout'][ 'archive-' . $archive_post_type ] ) {
					$return = false;
				}
			}
		}
	} elseif ( is_singular() ) {
		$single_post_types = array( 'post', 'page' ) + $additional_post_types;
		foreach ( $single_post_types as $single_post_type ) {
			if ( isset( $options['layout'][ 'single-' . $single_post_type ] ) && get_post_type() === $single_post_type ) {
				if ( 'col-one-no-subsection' === $options['layout'][ 'single-' . $single_post_type ] ) {
					$return = false;
				}
			}
		}
		if ( is_page() ) {
			$template           = get_post_meta( $post->ID, '_wp_page_template', true );
			$template_onecolumn = array(
				'page-onecolumn.php',
				'page-lp.php',
			);
			if ( in_array( $template, $template_onecolumn, true ) ) {
				$return = false;
			}
		}
		if ( isset( $post->_lightning_design_setting['layout'] ) ) {
			if ( 'col-one-no-subsection' === $post->_lightning_design_setting['layout'] ) {
				$return = false;
			} elseif ( 'col-two' === $post->_lightning_design_setting['layout'] ) {
				$return = true;
			} elseif ( 'col-one' === $post->_lightning_design_setting['layout'] ) {
				/* 1 column but subsection is exist */
				$return = true;
			}
		}
	}
	return apply_filters( 'lightning_is_subsection_display', $return );
}

/**
 * Page header Display or hidden
 *
 * The purpose of preparing a unique function is to enable batch control using a filter hook.
 *
 * @since Lightning 13.0.0
 * @return boolean
 */
function lightning_is_page_header() {
	$return = true;
	if ( is_singular() ) {

		global $post;

		if ( ! empty( $post->_lightning_design_setting['hidden_page_header'] ) ) {
			$return = false;
		}
	}
	return apply_filters( 'lightning_is_page_header', $return );
}

/**
 * Breadcrumb Display or hidden
 *
 * The purpose of preparing a unique function is to enable batch control using a filter hook.
 *
 * @since Lightning 13.0.0
 * @return boolean
 */
function lightning_is_breadcrumb() {
	$return = true;
	if ( is_singular() ) {

		global $post;

		if ( ! empty( $post->_lightning_design_setting['hidden_breadcrumb'] ) ) {
			$return = false;
		}
	}
	return apply_filters( 'lightning_is_breadcrumb', $return );
}
