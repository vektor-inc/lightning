<?php
/*-------------------------------------------*/
/*	Sanitize
/*-------------------------------------------*/
/*	Theme default options
/*-------------------------------------------*/
/*	Head logo
/*-------------------------------------------*/
/*	Chack use post top page
/*-------------------------------------------*/
/*	Chack post type info
/*-------------------------------------------*/
/*	lightning_is_mobile
/*-------------------------------------------*/
/*	lightning_top_slide_image_src
/*-------------------------------------------*/
/*	lightning_top_slide_count
/*-------------------------------------------*/
/*	lightning_is_slide_outer_link
/*-------------------------------------------*/
/*	lightning_slide_cover_style
/*-------------------------------------------*/
/*	Archive title
/*-------------------------------------------*/

/*-------------------------------------------*/
/*	Sanitize
/*-------------------------------------------*/

	/*	Add sanitize checkbox
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

/*-------------------------------------------*/
/*	Theme default options
/*-------------------------------------------*/
function lightning_theme_options_default() {
	$theme_options_default = array(
		'front_pr_display'              => true,
		'top_slide_time'                => 40000,
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
		'top_slide_image_3'             => get_template_directory_uri() . '/assets/images/top_image_3.jpg',
		'top_slide_url_3'               => esc_url( home_url() ),
		'top_slide_text_title_3'        => __( 'Johnijirou On Snow', 'lightning' ),
		'top_slide_text_caption_3'      => __( 'Growing up everyday', 'lightning' ),
		'top_slide_text_btn_3'          => __( 'READ MORE', 'lightning' ),
		'top_slide_text_align_3'        => 'left',
		'top_slide_text_color_3'        => '#000',
		'top_slide_text_shadow_use_3'   => true,
		'top_slide_text_shadow_color_3' => '#fff',
	);
	return $theme_options_default;
}


/*-------------------------------------------*/
/*	Head logo
/*-------------------------------------------*/
function lightning_print_headlogo() {
	$options = get_option( 'lightning_theme_options' );
	if ( isset( $options['head_logo'] ) && $options['head_logo'] ) {
		print '<img src="' . $options['head_logo'] . '" alt="' . get_bloginfo( 'name' ) . '" />';
	} else {
		bloginfo( 'name' );
	}
}

/*-------------------------------------------*/
/*	Chack use post top page
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


/*-------------------------------------------*/
/*	Chack post type info
/*-------------------------------------------*/
function lightning_get_post_type() {
	// Check use post top page
	$page_for_posts = lightning_get_page_for_posts();

	$woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id' );

	// Get post type slug
	/*-------------------------------------------*/
	// When WooCommerce taxonomy archive page , get_post_type() is does not work properly
	// $postType['slug'] = get_post_type();

	global $wp_query;
	if ( isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] ) {
		$postType['slug'] = $wp_query->query_vars['post_type'];
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
		if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ) {
			$postType['name'] = esc_html( get_the_title( $page_for_posts['post_top_id'] ) );
		} elseif ( $woocommerce_shop_page_id && $postType['slug'] == 'product' ) {
			$postType['name'] = esc_html( get_the_title( $woocommerce_shop_page_id ) );
		} else {
			$postType['name'] = esc_html( $post_type_object->labels->name );
		}
	}

	// Get custom post type archive url
	/*-------------------------------------------*/
	if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ) {
		$postType['url'] = esc_url( get_the_permalink( $page_for_posts['post_top_id'] ) );
	} elseif ( $woocommerce_shop_page_id ) {
		$postType['url'] = esc_url( get_the_permalink( $woocommerce_shop_page_id ) );
	} else {
		$postType['url'] = esc_url( get_post_type_archive_link( $postType['slug'] ) );
	}

	$postType = apply_filters( 'lightning_postType_custom', $postType );
	return $postType;
}


/*-------------------------------------------*/
/*	lightning_is_mobile
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

/*-------------------------------------------*/
/*	lightning_top_slide_image_src
/*-------------------------------------------*/
/* Although it is not used in the this theme, there is a possibility that it is used in Charm etc. */
function lightning_top_slide_image_src( $i ) {
	$top_slide_image_src     = '';
	$lightning_theme_options = get_option( 'lightning_theme_options' );

	// If 1st slide no set, set default image.
	if ( $i <= 3 ) {
		if ( ! isset( $lightning_theme_options[ 'top_slide_image_' . $i ] ) ) {
			$top_slide_image_src = get_template_directory_uri() . '/assets/images/top_image_' . $i . '.jpg';
		} else {
			$top_slide_image_src = $lightning_theme_options[ 'top_slide_image_' . $i ];
		}
	} else {
		if ( isset( $lightning_theme_options[ 'top_slide_image_' . $i ] ) ) {
			$top_slide_image_src = $lightning_theme_options[ 'top_slide_image_' . $i ];
		}
	}

	// Mobile image
	if ( lightning_is_mobile() && isset( $lightning_theme_options[ 'top_slide_image_mobile_' . $i ] ) ) {
		$top_slide_image_src = $lightning_theme_options[ 'top_slide_image_mobile_' . $i ];
	}
	return $top_slide_image_src;
}

/*-------------------------------------------*/
/*	lightning_top_slide_count
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
/*-------------------------------------------*/
/*	lightning_is_slide_outer_link
/*	link url exist but btn txt exixt? or not
/*-------------------------------------------*/
function lightning_is_slide_outer_link( $lightning_theme_options, $i ) {
	if ( ! empty( $lightning_theme_options[ 'top_slide_url_' . $i ] ) && empty( $lightning_theme_options[ 'top_slide_btn_text_' . $i ] ) ) {
		return true;
	} else {
		return false;
	}
}

/*-------------------------------------------*/
/*	lightning_slide_cover_style
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

/*-------------------------------------------*/
/*	Archive title
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

/*-------------------------------------------*/
/*	CopyRight
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

function lightning_is_frontpage_onecolumn() {
	// ※ global変数だとテストが効かないため
	// global $lightning_theme_options;
	// $options          = $lightning_theme_options;
	$options          = get_option( 'lightning_theme_options' );
	$page_on_front_id = get_option( 'page_on_front' );

	if ( isset( $options['top_sidebar_hidden'] ) && $options['top_sidebar_hidden'] ) {
		return true;
	}
	if ( $page_on_front_id ) {
		$template = get_post_meta( $page_on_front_id, '_wp_page_template', true );
		if ( $template == 'page-onecolumn.php' ) {
			return true;
		}
	}
	return false;
}

function lightning_get_theme_name() {
	return apply_filters( 'lightning_theme_name', 'Lightning' );
}
function lightning_get_theme_name_short() {
	return apply_filters( 'lightning_theme_name_short', 'LTG' );
}
