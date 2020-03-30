<?php

/*
  lightning_get_the_class_name
  Sanitize
  Theme default options
  lightning_get_theme_options()
  Head logo
  Chack use post top page
  Chack post type info
  lightning_is_mobile
  lightning_top_slide_count
  lightning_is_slide_outer_link
  lightning_slide_cover_style
  Archive title
  lightning_is_layout_onecolumn
  lightning_check_color_mode
/*-------------------------------------------*/


/*
  lightning_get_the_class_name
/*-------------------------------------------*/
function lightning_get_the_class_name( $position = '' ) {
	$skin_info = Lightning_Design_Manager::get_current_skin();

	if ( empty( $skin_info['bootstrap'] ) ) {
		$class_names = array(
			'header'          => 'navbar siteHeader',
			'header_logo'     => 'navbar-brand siteHeader_logo',
			'nav_menu_header' => 'nav gMenu',
			'siteContent'     => 'section siteContent',
			'mainSection'     => 'col-md-8 mainSection',
			'sideSection'     => 'col-md-3 col-md-offset-1 subSection sideSection',
		);
		if ( lightning_is_layout_onecolumn() ) {
			$class_names['mainSection'] = 'col-md-12 mainSection';
			$class_names['sideSection'] = 'col-md-12 sideSection';
		}
	} elseif ( $skin_info['bootstrap'] === 'bs4' ) {

		$class_names = array(
			'header'          => 'siteHeader',
			'header_logo'     => 'navbar-brand siteHeader_logo',
			'nav_menu_header' => 'gMenu vk-menu-acc',
			'siteContent'     => 'section siteContent',
			'mainSection'     => 'col mainSection mainSection-col-two baseSection',
			'sideSection'     => 'col subSection sideSection sideSection-col-two baseSection',
		);
		if ( lightning_is_layout_onecolumn() ) {
			$class_names['mainSection'] = 'col mainSection mainSection-col-one';
			$class_names['sideSection'] = 'col subSection sideSection sideSection-col-one';
			if ( lightning_is_subsection_display() ) {
				$class_names['mainSection'] .= ' mainSection-marginBottom-on';
			}
		} else {
			// 2 column
			$options = get_option( 'lightning_theme_options' );
			// sidebar-position
			if ( isset( $options['sidebar_position'] ) && $options['sidebar_position'] === 'left' ) {
				$class_names['mainSection'] = 'col mainSection mainSection-col-two mainSection-pos-right';
				$class_names['sideSection'] = 'col subSection sideSection sideSection-col-two sideSection-pos-left';
			}
		}
		if ( lightning_is_siteContent_padding_off() ) {
			$class_names['siteContent'] .= ' siteContent-paddingVertical-off';
			$class_names['mainSection'] .= ' mainSection-marginVertical-off';
		}
	}

	if ( empty( $class_names[ $position ] ) ) {
		$class_names[ $position ] = esc_attr( $position );
	}

	$class_names = apply_filters( 'lightning_get_the_class_names', $class_names, $position );

	$return = $class_names[ $position ];

	// *** Warning ***
	// The 'lightning_get_the_class_name' will be discontinued soon.
	// Please instead to 'lightning_get_the_class_names'.
	return esc_attr( apply_filters( 'lightning_get_the_class_name', $return ) );
}

function lightning_the_class_name( $position = '', $extend = array() ) {
	echo lightning_get_the_class_name( $position, $extend );
}

/*
  Sanitize
/*-------------------------------------------*/

/*
	Add sanitize checkbox
/*-------------------------------------------*/
function lightning_sanitize_checkbox( $input ) {
	if ( $input == true ) {
		return true;
	} else {
		return false;
	}
}

function lightning_sanitize_number( $input ) {
	$input = mb_convert_kana( $input, 'a' );
	if ( is_numeric( $input ) ) {
		return $input;
	} else {
		return 0;
	}
}

function lightning_sanitize_number_percentage( $input ) {
	$input = lightning_sanitize_number( $input );
	if ( 0 <= $input && $input <= 100 ) {
		return $input;
	} else {
		return 0;
	}
}

function lightning_sanitize_radio( $input ) {
	return esc_attr( $input );
}

function lightning_sanitize_textarea( $input ) {
	$allowed_html = array(
		'a'      => array(
			'id'    => array(),
			'href'  => array(),
			'title' => array(),
			'class' => array(),
			'role'  => array(),
		),
		'br'     => array(),
		'em'     => array(),
		'strong' => array(),
		'i'      => array(
			'class' => array(),
		),
	);
	return wp_kses( $input, $allowed_html );
}

/*
  Theme default options
/*-------------------------------------------*/
function lightning_get_theme_options_default() {
	$theme_options_default = array(
		'front_pr_display'              => true,
		'top_slide_time'                => 4000,
		'top_slide_image_1'             => get_template_directory_uri() . '/assets/images/top_image_1.jpg',
		'top_slide_url_1'               => __( 'https://lightning.nagoya/', 'lightning' ),
		'top_slide_text_title_1'        => __( 'Simple and Customize easy <br>WordPress theme.', 'lightning' ),
		'top_slide_text_caption_1'      => __( '100% GPL Lisence  and adopting the bootstrap', 'lightning' ),
		'top_slide_text_btn_1'          => __( 'READ MORE', 'lightning' ),
		'top_slide_text_align_1'        => 'left',
		'top_slide_text_color_1'        => '#000',
		'top_slide_text_shadow_use_1'   => true,
		'top_slide_text_shadow_color_1' => '#fff',
		'top_slide_image_2'             => get_template_directory_uri() . '/assets/images/top_image_2.jpg',
		'top_slide_url_2'               => esc_url( home_url() ),
		'top_slide_text_title_2'        => __( 'Johnijirou On Snow', 'lightning' ),
		'top_slide_text_caption_2'      => __( 'Growing up everyday', 'lightning' ),
		'top_slide_text_btn_2'          => __( 'READ MORE', 'lightning' ),
		'top_slide_text_align_2'        => 'left',
		'top_slide_text_color_2'        => '#000',
		'top_slide_text_shadow_use_2'   => true,
		'top_slide_text_shadow_color_2' => '#fff',
	);
	return $theme_options_default;
}

// Old function name
function lightning_theme_options_default() {
	return lightning_get_theme_options_default();
}

/*
  lightning_get_theme_options()
/*-------------------------------------------*/
function lightning_get_theme_options() {
	$lightning_theme_options_default = lightning_get_theme_options_default();
	$lightning_theme_options         = get_option( 'lightning_theme_options', $lightning_theme_options_default );
	// It use then display default text to old user ... orz
	// $lightning_theme_options         = wp_parse_args( $lightning_theme_options, $lightning_theme_options_default );
	return $lightning_theme_options;
}

/*
  Head logo
/*-------------------------------------------*/
function lightning_get_print_headlogo() {
	$options = get_option( 'lightning_theme_options' );
	if ( ! empty( $options['head_logo'] ) ) {
		$head_logo = apply_filters( 'lightning_head_logo_image_url', $options['head_logo'] );
		if ( $head_logo ) {
			return '<img src="' . esc_url( $head_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" />';
		}
	}
	return get_bloginfo( 'name' );
}
function lightning_print_headlogo() {
	echo lightning_get_print_headlogo();
}

/*
  Chack use post top page
/*-------------------------------------------*/
function lightning_get_page_for_posts() {
	// Get post top page by setting display page.
	$page_for_posts['post_top_id'] = get_option( 'page_for_posts' );

	// Set use post top page flag.
	$page_for_posts['post_top_use'] = ( $page_for_posts['post_top_id'] ) ? true : false;

	// When use post top page that get post top page name.
	$page_for_posts['post_top_name'] = ( $page_for_posts['post_top_use'] ) ? get_the_title( $page_for_posts['post_top_id'] ) : '';

	return $page_for_posts;
}


/*
  Chack post type info
/*-------------------------------------------*/
function lightning_get_post_type() {
	// Check use post top page
	$page_for_posts = lightning_get_page_for_posts();

	$woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id' );

	// Get post type slug
	/*
	-------------------------------------------*/
	// When WooCommerce taxonomy archive page , get_post_type() is does not work properly
	// $postType['slug'] = get_post_type();

	global $wp_query;
	if ( ! empty( $wp_query->query_vars['post_type'] ) ) {

		$postType['slug'] = $wp_query->query_vars['post_type'];
		// Maybe $wp_query->query_vars['post_type'] is usually an array...
		if ( is_array( $postType['slug'] ) ) {
			$postType['slug'] = current( $postType['slug'] );
		}
	} elseif ( is_tax() ) {
		// Case of tax archive and no posts
		$taxonomy         = get_queried_object()->taxonomy;
		$postType['slug'] = get_taxonomy( $taxonomy )->object_type[0];
	} else {
		// This is necessary that when no posts.
		$postType['slug'] = 'post';
	}

	// Get custom post type name
	/*-------------------------------------------*/
	$post_type_object = get_post_type_object( $postType['slug'] );
	if ( $post_type_object ) {
		$allowed_html = array(
			'span' => array( 'class' => array() ),
			'b'    => array(),
		);
		if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ) {
			$postType['name'] = wp_kses( get_the_title( $page_for_posts['post_top_id'] ), $allowed_html );
		} elseif ( $woocommerce_shop_page_id && $postType['slug'] == 'product' ) {
			$postType['name'] = wp_kses( get_the_title( $woocommerce_shop_page_id ), $allowed_html );
		} else {
			$postType['name'] = esc_html( $post_type_object->labels->name );
		}
	}

	// Get custom post type archive url
	/*-------------------------------------------*/
	if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ) {
		$postType['url'] = esc_url( get_the_permalink( $page_for_posts['post_top_id'] ) );
	} elseif ( $woocommerce_shop_page_id && $postType['slug'] == 'product' ) {
		$postType['url'] = esc_url( get_the_permalink( $woocommerce_shop_page_id ) );
	} else {
		$postType['url'] = esc_url( get_post_type_archive_link( $postType['slug'] ) );
	}

	$postType = apply_filters( 'lightning_postType_custom', $postType );
	return $postType;
}


/*
  lightning_is_mobile
/*-------------------------------------------*/
function lightning_is_mobile() {
	$useragents = array(
		'iPhone', // iPhone
		'iPod', // iPod touch
		'Android.*Mobile', // 1.5+ Android *** Only mobile
		'Windows.*Phone', // *** Windows Phone
		'dream', // Pre 1.5 Android
		'CUPCAKE', // 1.5+ Android
		'blackberry9500', // Storm
		'blackberry9530', // Storm
		'blackberry9520', // Storm v2
		'blackberry9550', // Storm v2
		'blackberry9800', // Torch
		'webOS', // Palm Pre Experimental
		'incognito', // Other iPhone browser
		'webmate', // Other iPhone browser
	);
	$pattern    = '/' . implode( '|', $useragents ) . '/i';
	if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ) {
		$is_mobile = preg_match( $pattern, $_SERVER['HTTP_USER_AGENT'] );
	} else {
		$is_mobile = false;
	}
	return apply_filters( 'lightning_is_mobile', $is_mobile );
}

/*
  lightning_top_slide_count
/*-------------------------------------------*/
function lightning_top_slide_count_max() {
	$top_slide_count_max = 5;
	$top_slide_count_max = apply_filters( 'lightning_top_slide_count_max', $top_slide_count_max );
	return $top_slide_count_max;
}
function lightning_top_slide_count( $lightning_theme_options ) {
	$top_slide_count     = 0;
	$top_slide_count_max = lightning_top_slide_count_max();
	for ( $i = 1; $i <= $top_slide_count_max; ) {
		if ( ! empty( $lightning_theme_options[ 'top_slide_image_' . $i ] ) ) {
				$top_slide_count ++;
		}
		$i++;
	}
	return $top_slide_count;
}
/*
  lightning_is_slide_outer_link
/*
  link url exist but btn txt exixt? or not
/*-------------------------------------------*/
function lightning_is_slide_outer_link( $lightning_theme_options, $i ) {
	if ( ! empty( $lightning_theme_options[ 'top_slide_url_' . $i ] ) && empty( $lightning_theme_options[ 'top_slide_text_btn_' . $i ] ) ) {
		return true;
	} else {
		return false;
	}
}

/*
  lightning_slide_cover_style
/*-------------------------------------------*/
function lightning_slide_cover_style( $lightning_theme_options, $i ) {
	$cover_style = '';

	if (
		! empty( $lightning_theme_options[ 'top_slide_cover_color_' . $i ] ) &&
		! empty( $lightning_theme_options[ 'top_slide_cover_opacity_' . $i ] )
	) {

		// bgcolor
		$cover_style = 'background-color:' . $lightning_theme_options[ 'top_slide_cover_color_' . $i ] . ';';

		// opacity
		$opacity      = lightning_sanitize_number_percentage( $lightning_theme_options[ 'top_slide_cover_opacity_' . $i ] ) / 100;
		$cover_style .= 'opacity:' . $opacity;

	}
	return $cover_style;
}

/*
  Archive title
/*-------------------------------------------*/
add_filter( 'get_the_archive_title', 'lightning_get_the_archive_title' );
function lightning_get_the_archive_title() {
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = sprintf( __( 'Author: %s', 'lightning' ), '<span class="vcard">' . get_the_author() . '</span>' );
	} elseif ( is_year() ) {
		$title = get_the_date( _x( 'Y', 'yearly archives date format', 'lightning' ) );
	} elseif ( is_month() ) {
		$title = get_the_date( _x( 'F Y', 'monthly archives date format', 'lightning' ) );
	} elseif ( is_day() ) {
		$title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'lightning' ) );
	} elseif ( is_post_type_archive() ) {
		$title = post_type_archive_title( '', false );
	} elseif ( is_tax() ) {
		$title = single_term_title( '', false );
	} elseif ( is_home() && ! is_front_page() ) {
		$lightning_page_for_posts = lightning_get_page_for_posts();
		$title                    = $lightning_page_for_posts['post_top_name'];
	} else {
		global $wp_query;
		// get post type
		$postType = $wp_query->query_vars['post_type'];
		if ( $postType ) {
			$title = get_post_type_object( $postType )->labels->name;
		} else {
			$title = __( 'Archives', 'lightning' );
		}
	}
	return apply_filters( 'lightning_get_the_archive_title', $title );
}

/*
  CopyRight
/*-------------------------------------------*/
function lightning_the_footerCopyRight() {

	// copyright
	/*------------------*/
	$lightning_footerCopyRight = '<p>' . sprintf( __( 'Copyright &copy; %s All Rights Reserved.', 'lightning' ), get_bloginfo( 'name' ) ) . '</p>';
	echo apply_filters( 'lightning_footerCopyRightCustom', $lightning_footerCopyRight );

	// Powered
	/*------------------*/
	$lightning_footerPowered = __( '<p>Powered by <a href="https://wordpress.org/">WordPress</a> &amp; <a href="https://lightning.nagoya" target="_blank" title="Free WordPress Theme Lightning"> Lightning Theme</a> by Vektor,Inc. technology.</p>', 'lightning' );
	echo apply_filters( 'lightning_footerPoweredCustom', $lightning_footerPowered );

}

function lightning_get_theme_name() {
	return apply_filters( 'lightning_theme_name', 'Lightning' );
}
function lightning_get_theme_name_short() {
	return apply_filters( 'lightning_get_theme_name_short', 'LTG' );
}
function lightning_get_prefix() {
	$prefix = apply_filters( 'lightning_get_prefix', 'LTG' );
	if ( $prefix ) {
		$prefix .= ' ';
	}
	return $prefix;
}
function lightning_get_prefix_customize_panel() {
	$prefix_customize_panel = apply_filters( 'lightning_get_prefix_customize_panel', 'Lightning' );
	if ( $prefix_customize_panel ) {
		$prefix_customize_panel .= ' ';
	}
	return $prefix_customize_panel;
}

/*
  lightning_check_color_mode
/*-------------------------------------------*/
/**
 * [lightning_check_color_mode description]
 *
 * @param  string  $input         input color code
 * @param  boolean $return_detail If false that return 'mode' only
 * @return string                 If $return_detail == false that return light ot dark
 */
function lightning_check_color_mode( $input = '#ffffff', $return_detail = false ) {
	$color['input'] = $input;
	// delete #
	$color['input'] = preg_replace( '/#/', '', $color['input'] );

	$color_len = strlen( $color['input'] );

	// Only 3 character
	if ( $color_len === 3 ) {
		$color_red   = substr( $color['input'], 0, 1 ) . substr( $color['input'], 0, 1 );
		$color_green = substr( $color['input'], 1, 1 ) . substr( $color['input'], 1, 1 );
		$color_blue  = substr( $color['input'], 2, 1 ) . substr( $color['input'], 2, 1 );
	} elseif ( $color_len === 6 ) {
		$color_red   = substr( $color['input'], 0, 2 );
		$color_green = substr( $color['input'], 2, 2 );
		$color_blue  = substr( $color['input'], 4, 2 );
	} else {
		$color_red   = 'ff';
		$color_green = 'ff';
		$color_blue  = 'ff';
	}

	// change 16 to 10 number
	$color_red           = hexdec( $color_red );
	$color_green         = hexdec( $color_green );
	$color_blue          = hexdec( $color_blue );
	$color['number_sum'] = $color_red + $color_green + $color_blue;

	$color_change_point = 765 / 2;

	if ( $color['number_sum'] > $color_change_point ) {
		$color['mode'] = 'light';
	} else {
		$color['mode'] = 'dark';
	}

	if ( $return_detail ) {
		return $color;
	} else {
		return $color['mode'];
	}
}
