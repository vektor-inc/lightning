<?php

function lightning_get_class_names( $position = '' ) {

	$class_names = array(
		'site-header'  => array(
			'site-header',
			'site-header--layout--nav-float',
		),
		'global-nav'   => array(
			'global-nav',
			'global-nav--layout--float-right',
		),
		'site-body'    => array( 'site-body' ),
		'main-section' => array( 'main-section' ),
		'sub-section'  => array( 'sub-section' ),
	);

	if ( empty( $class_names[ $position ] ) ) {
		$class_names[ $position ][] = $position;
	}

	/**
	 * カラムでの配列操作
	 * （本来はカラムコントローラーからフックで処理するのが望ましい）
	 */
	if ( lightning_is_layout_onecolumn() && lightning_is_subsection() ) {

		$class_names['main-section'][] = 'main-section--margin-bottom--on';

	} elseif ( ! lightning_is_layout_onecolumn() ) {

		$class_names['main-section'][] = 'main-section--col--two';
		$class_names['sub-section'][]  = 'sub-section--col--two';
		// 2 column
		$options = get_option( 'lightning_theme_options' );
		// sidebar-position
		if ( isset( $options['sidebar_position'] ) && $options['sidebar_position'] === 'left' ) {
			$class_names['main-section'][] = 'main-section--pos--right';
			$class_names['sub-section'][]  = 'sub-section--pos--left';
		}
	}

	if ( lightning_is_site_body_padding_off() ) {
		$class_names['site-body'][]    = 'site-body--padding-vertical--off';
		$class_names['main-section'][] = 'main-section--margin-vertical--off';
	}

	$class_names = apply_filters( 'lightning_get_class_names', $class_names );
	return $class_names;
}

function lightning_get_the_class_name( $position = '' ) {
	$class_names = lightning_get_class_names( $position );

	// すべてのクラス名から単一のクラスを代入
	if ( $position && empty( $class_names[ $position ] ) ) {
		$class_name = $position;
	} else {
		$class_name = implode( ' ', $class_names[ $position ] );
	}

	// 元の配列（lightning_get_class_names）はフック操作が少し難しいので単純に書き換えたい人用
	$class_name = esc_attr( apply_filters( 'lightning_get_the_class_name_' . $position, $class_name ) );

	return esc_attr( apply_filters( 'lightning_get_the_class_name', $class_name, $position ) );
}

function lightning_the_class_name( $position = '' ) {
	echo lightning_get_the_class_name( $position );
}

/*
  Theme default options
/*-------------------------------------------*/
function lightning_get_theme_options_default() {
	$theme_options_default = array(
		'layout' => array(
			'front-page' => 'col-one-no-subsection',
		),
	);
	return $theme_options_default;
}

/*
  lightning_get_theme_options()
/*-------------------------------------------*/
function lightning_get_theme_options() {
	$lightning_theme_options_default = lightning_get_theme_options_default();
	$lightning_theme_options         = get_option( 'lightning_theme_options', $lightning_theme_options_default );
	$lightning_theme_options         = wp_parse_args( $lightning_theme_options, $lightning_theme_options_default );
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
  Archive title
/*-------------------------------------------*/
add_filter( 'get_the_archive_title', 'lightning_get_the_archive_title' );
function lightning_get_the_archive_title() {
	$title = '';
	if ( is_category() ) {
		$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
		$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
		$title = get_the_author();
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
		// Get post top page by setting display page.
		$post_top_id = get_option( 'page_for_posts' );
		if ( $post_top_id ) {
			$title = get_the_title( $post_top_id );
		}
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

function lightning_the_footer_copyight() {

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

function lightning_get_entry_meta( $options = array() ) {

	$defaults = array(
		'published'    => true,
		'updated'      => true,
		'author_name'  => true,
		'author_image' => true,
		'class_outer'  => 'entry-meta',
	);

	$option = apply_filters( 'lightning_get_entry_meta_options', wp_parse_args( $options, $defaults ) );

	if ( $option['published'] || $option['updated'] || $option['author_name'] || $option['author_image'] ) {
		$html = '<div class="' . $option['class_outer'] . '">';

		if ( $option['published'] ) {
			$html .= '<span class="entry-meta-item entry-meta-item-date">
			<i class="far fa-calendar-alt"></i>
			<span class="published">' . esc_html( get_the_date() ) . '</span>
			</span>';
		}

		if ( $option['updated'] ) {
			$html .= '<span class="entry-meta-item entry-meta-item-updated">
			<i class="fas fa-history"></i>
			<span class="screen-reader-text">' . __( 'Last updated', 'lightning' ) . ' : </span>
			<span class="updated">' . get_the_modified_date( '' ) . '</span>
			</span>';
		}

		if ( $option['author_name'] || $option['author_image'] ) {
			// Post author
			// For post type where author does not exist
			$author = get_the_author();
			if ( $author ) {
				$meta_hidden_author = ( ! empty( $options['postAuthor_hidden'] ) ) ? ' entry-meta_hidden' : '';

				$html .= '<span class="entry-meta-item entry-meta-item-author' . $meta_hidden_author . '">
				<span class="vcard author" itemprop="author">';

				if ( $option['author_image'] ) {
					$html .= '<span class="entry-meta-item-author-image">';

					// VK Post Author Display の画像を取得
					$profile_image_id = get_the_author_meta( 'user_profile_image' );
					if ( $profile_image_id ) {
						$profile_image_src = wp_get_attachment_image_src( $profile_image_id, 'thumbnail' );
						$html             .= '<img src="' . $profile_image_src[0] . '" alt="' . esc_attr( $author ) . '" />';
					} else {
						$html .= get_avatar( get_the_author_meta( 'email' ), 30 );
					}

					$html .= '</span>';
				}

				if ( $option['author_name'] ) {
					$html .= '<span class="fn" itemprop="name">' . esc_html( $author ) . '</span>';
				}

				$html .= '</span></span>';

			} // if ( $author ) {
		}

		$html .= '</div>';
	}

	return apply_filters( 'lightning_get_entry_meta', $html );
}

function lightning_the_entry_meta( $options = array() ) {
	echo wp_kses_post( lightning_get_entry_meta() );
}


function lightning_is_woo_page() {
	if ( ! class_exists( 'woocommerce' ) ) {
		return false;
	}
	$post_type_info = VK_Helpers::get_post_type_info();
	if ( isset( $post_type_info['slug'] ) && 'product' === $post_type_info['slug'] ) {
		return true;
	}
	return false;
}
