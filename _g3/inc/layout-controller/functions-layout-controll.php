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
		'error404' => array(
			'function' => 'is_404',
		),
		'search'   => array(
			'function' => 'is_search',
		),
		'archive-author'   => array(
			'function' => 'is_archive',
		),
	);
	return $array;
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
	}

	$additional_post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'names'
	);

	/**
	 * アーカイブページの場合
	 */
	if ( is_archive() && ! is_search() && ! is_author() ) {
		$current_post_type_info = VK_Helpers::get_post_type_info();
		$archive_post_types = array( 'post' ) + $additional_post_types;
		foreach ( $archive_post_types as $archive_post_type ) {
			if ( isset( $options['layout'][ 'archive-' . $archive_post_type ] ) && $current_post_type_info['slug'] === $archive_post_type ) {
				if ( 'col-one' === $options['layout'][ 'archive-' . $archive_post_type ] || 'col-one-no-subsection' === $options['layout'][ 'archive-' . $archive_post_type ] ) {
					$onecolumn = true;
				}
			}
		}
	}

	if ( is_singular() ) {
		global $post;
		$single_post_types = array( 'post', 'page' ) + $additional_post_types;
		foreach ( $single_post_types as $single_post_type ) {
			if ( isset( $options['layout'][ 'single-' . $single_post_type ] ) && get_post_type() === $single_post_type ) {
				if ( 'col-one' === $options['layout'][ 'single-' . $single_post_type ] || 'col-one-no-subsection' === $options['layout'][ 'single-' . $single_post_type ] ) {
					$onecolumn = true;
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
				$onecolumn = true;
			}
		}
		if ( isset( $post->_lightning_design_setting['layout'] ) ) {
			if ( 'col-two' === $post->_lightning_design_setting['layout'] ) {
				$onecolumn = false;
			} elseif ( 'col-one-no-subsection' === $post->_lightning_design_setting['layout'] ) {
				$onecolumn = true;
			} elseif ( 'col-one' === $post->_lightning_design_setting['layout'] ) {
				$onecolumn = true;
			}
		}
	}
	return apply_filters( 'lightning_is_layout_onecolumn', $onecolumn );
}

/**
 * Lightning Is Subsection Display
 *
 * @since G3
 * @return boolean
 */
function lightning_is_subsection() {
	global $post;
	$return  = true;
	$options = get_option( 'lightning_theme_options' );

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
		$current_post_type_info = VK_Helpers::get_post_type_info();
		$archive_post_types = array( 'post' ) + $additional_post_types;
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
	return apply_filters( 'lightning_is_subsection', $return );
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
	} else if ( is_front_page() ) {
		$return = false;
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
	} else if ( is_front_page() ) {
		$return = false;
	}
	return apply_filters( 'lightning_is_breadcrumb', $return );
}

function lightning_is_site_body_padding_off() {
	$return = false;
	if ( is_singular() ) {
		global $post;
		if ( ! empty( $post->_lightning_design_setting['site_body_padding'] ) ) {
			$return = true;
		}
	}
	return apply_filters( 'lightning_is_site_body_padding_off', $return );
}

/*
  add body class
/*-------------------------------------------*/
add_filter( 'body_class', 'lightning_add_body_class_sidefix' );
function lightning_add_body_class_sidefix( $class ) {
	$options = get_option( 'lightning_theme_options' );
	if ( ! lightning_is_layout_onecolumn() ) {
		if ( isset( $options['sidebar_fix'] ) ) {
			if ( $options['sidebar_fix'] == 'priority-top' ) {
				$class[] = 'sidebar-fix';
				$class[] = 'sidebar-fix-priority-top';
			} elseif ( $options['sidebar_fix'] == 'priority-bottom' ) {
				$class[] = 'sidebar-fix';
				$class[] = 'sidebar-fix-priority-bottom';
			} elseif ( $options['sidebar_fix'] === 'no-fix' || $options['sidebar_fix'] === true ) {
				return $class;
			}
		} else {
			$class[] = 'sidebar-fix';
			$class[] = 'sidebar-fix-priority-top';
		}
	}
	return $class;
}