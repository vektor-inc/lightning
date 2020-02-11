<?php

/*
  lightning_is_layout_onecolumn
/*-------------------------------------------*/

function lightning_is_layout_onecolumn() {
	$onecolumn = false;
	$options =  get_option('lightning_theme_options');
	global $wp_query;
	if (  is_front_page() ){
		if ( isset( $options['layout']['front-page'] ) && $options['layout']['front-page'] === 'col-one' ){
			$onecolumn = true;
		} else {
			$page_on_front_id = get_option( 'page_on_front' );
			if ( $page_on_front_id ) {
				$template = get_post_meta( $page_on_front_id, '_wp_page_template', true );
				if ( $template == 'page-onecolumn.php' ) {
					return true;
				}
			}
		}

	} elseif ( is_404() ) {
		if ( isset( $options['layout']['error404'] ) && $options['layout']['error404'] === 'col-one' ){
			$onecolumn = true;
		} 
	} elseif ( is_search() ) {
		if ( isset( $options['layout']['search'] ) && $options['layout']['search'] === 'col-one' ){
			$onecolumn = true;
		}
	} elseif ( is_home() && ! is_front_page() ) {
		if ( isset( $options['layout']['archive'] ) && $options['layout']['archive'] === 'col-one' ){
			$onecolumn = true;
		} 
	} elseif ( is_category() ) {
		if ( isset( $options['layout']['archive'] ) && $options['layout']['archive'] === 'col-one' ){
			$onecolumn = true;
		} 
	} elseif ( is_single() ) {
		if ( isset( $options['layout']['single'] ) && $options['layout']['single'] === 'col-one' ){
			$onecolumn = true;
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

/*
  lightning_is_subsection_display
/*-------------------------------------------*/
function lightning_is_subsection_display() {
	$return = true;
	$options =  get_option('lightning_theme_options');

	// break and hidden
	if ( is_front_page() ){
		if ( 
			isset( $options['sidebar_display']['front-page'] ) && 
			$options['sidebar_display']['front-page'] === 'hidden' 
			) {
			$return = false;
		}
		if ( ! lightning_is_layout_onecolumn() ) {
			$return = true;
		}
	} elseif ( is_404() ){
		if ( 
			isset( $options['sidebar_display']['error404'] ) && 
			$options['sidebar_display']['error404'] === 'hidden' ) {
			$return = false;
		}
	} elseif ( is_search() ){
		if ( 
			isset( $options['sidebar_display']['search']) && 
			$options['sidebar_display']['search'] === 'hidden' ) {
			$return = false;
		}
	} elseif ( is_home() && ! is_front_page() ){
		if ( 
			isset( $options['sidebar_display']['archive']) && 
			$options['sidebar_display']['archive'] === 'hidden' ) {
			$return = false;
		}
	} elseif ( is_archive() ){
		if ( 
			isset($options['sidebar_display']['archive'] ) && 
			$options['sidebar_display']['archive'] === 'hidden' ) {
			$return = false;
		}
	} elseif ( is_singular() ) {
		if ( is_single() ){
			if ( 
				isset( $options['sidebar_display']['single'] ) && 
				$options['sidebar_display']['single'] === 'hidden' ) {
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
	return apply_filters( 'lightning_is_subsection_display', $return);
}

/**
 * Page header and Breadcrumb Display or hidden
 *
 * @return boolean
 */
function lightning_is_page_header_and_breadcrumb() {
	$return = true;
	if ( is_page() ){
		global $post;

		if ( ! empty( $post->_lightning_design_setting['hidden_page_header_and_breadcrumb'] ) ) {
			$return = false;
		}
		
	}
	return apply_filters( 'lightning_is_page_header_and_breadcrumb', $return );
}