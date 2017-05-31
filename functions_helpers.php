<?php
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
/*	Archive title
/*-------------------------------------------*/

/*-------------------------------------------*/
/*	Head logo
/*-------------------------------------------*/
function lightning_print_headlogo() {
	$options = get_option('lightning_theme_options');
	if (isset($options['head_logo']) && $options['head_logo']){
		print '<img src="'.$options['head_logo'].'" alt="'.get_bloginfo('name').'" />';
	} else {
		bloginfo('name');
	}
}

/*-------------------------------------------*/
/*	Chack use post top page
/*-------------------------------------------*/
function lightning_get_page_for_posts(){
	// Get post top page by setting display page.
	$page_for_posts['post_top_id'] = get_option('page_for_posts');

	// Set use post top page flag.
	$page_for_posts['post_top_use'] = ( isset($page_for_posts['post_top_id']) && $page_for_posts['post_top_id'] ) ? true : false ;

	// When use post top page that get post top page name.
	$page_for_posts['post_top_name'] = ( $page_for_posts['post_top_use'] ) ? get_the_title( $page_for_posts['post_top_id'] ) : '';

	return $page_for_posts;
}


/*-------------------------------------------*/
/*	Chack post type info
/*-------------------------------------------*/
function lightning_get_post_type(){
	// Check use post top page
	$page_for_posts = lightning_get_page_for_posts();

	// Get post type slug
	/*-------------------------------------------*/
	$postType['slug'] = get_post_type();
	if ( !$postType['slug'] ) {
	  global $wp_query;
	  if ( isset( $wp_query->query_vars['post_type'] ) && $wp_query->query_vars['post_type'] ) {
	      $postType['slug'] = $wp_query->query_vars['post_type'];
	  } elseif ( is_tax() ) {
	  	// Case of tax archive and no posts
		$taxonomy = get_queried_object()->taxonomy;
		$postType['slug'] = get_taxonomy( $taxonomy )->object_type[0];
	  // This is necessary that when no posts.
	  } else {
	  	$postType['slug'] = 'post';
	  }
	}

	// Get custom post type name
	/*-------------------------------------------*/
	$post_type_object = get_post_type_object($postType['slug']);
	if($post_type_object){
		if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ){
			$postType['name'] = esc_html( get_the_title($page_for_posts['post_top_id']) );
		} else {
			$postType['name'] = esc_html($post_type_object->labels->name);
		}
	}

	// Get custom post type archive url
	/*-------------------------------------------*/
	if ( $page_for_posts['post_top_use'] && $postType['slug'] == 'post' ){
		$postType['url'] = get_the_permalink($page_for_posts['post_top_id']);
	} else {
		$postType['url'] = home_url('/').'/?post_type='.$postType['slug'];
	}

	$postType = apply_filters('lightning_postType_custom',$postType);
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
        'webmate' // Other iPhone browser
    );
    $pattern = '/'.implode('|', $useragents).'/i';
    if ( isset( $_SERVER['HTTP_USER_AGENT'] ) ){
    	$is_mobile = preg_match($pattern, $_SERVER['HTTP_USER_AGENT']);
    } else {
    	$is_mobile = false;
    }
    return apply_filters('lightning_is_mobile',$is_mobile);
}

/*-------------------------------------------*/
/*	lightning_top_slide_image_src
/*-------------------------------------------*/
function lightning_top_slide_image_src($i){
	$top_slide_image_src = '';
	$lightning_theme_options = get_option('lightning_theme_options');
	
    // If 1st slide no set, set default image.
    if ( $i <= 3 ){
        if ( !isset( $lightning_theme_options['top_slide_image_'.$i] ) ) {
            $top_slide_image_src = get_template_directory_uri().'/images/top_image_'.$i.'.jpg';
        } else {
            $top_slide_image_src = $lightning_theme_options['top_slide_image_'.$i];
        }
    } else {
        if ( isset( $lightning_theme_options['top_slide_image_'.$i] ))
            $top_slide_image_src = $lightning_theme_options['top_slide_image_'.$i];
    }

    // Mobile image
    if ( lightning_is_mobile() && isset( $lightning_theme_options['top_slide_image_mobile_'.$i] ) ){
        $top_slide_image_src = $lightning_theme_options['top_slide_image_mobile_'.$i];
    }
    return $top_slide_image_src;
}

/*-------------------------------------------*/
/*	Archive title
/*-------------------------------------------*/
add_filter('get_the_archive_title','lightning_get_the_archive_title');
function lightning_get_the_archive_title(){
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
    } elseif ( is_home() && !is_front_page() ){
    	$lightning_page_for_posts = lightning_get_page_for_posts();
    	$title = $lightning_page_for_posts['post_top_name'];
    } else {
        global $wp_query;
		// get post type
		$postType = $wp_query->query_vars['post_type'];
		if ( $postType ) {
			$title = get_post_type_object($postType)->labels->name;
		} else {
			$title = __( 'Archives', 'lightning' );
		}
    }
    return apply_filters( 'lightning_get_the_archive_title', $title );
}

/*-------------------------------------------*/
/*	CopyRight
/*-------------------------------------------*/
function lightning_the_footerCopyRight(){

	// copyright
	/*------------------*/
	$lightning_footerCopyRight = '<p>'.sprintf( __( 'Copyright &copy; %s All Rights Reserved.', 'lightning' ), get_bloginfo('name') ).'</p>';
	echo apply_filters( 'lightning_footerCopyRightCustom', $lightning_footerCopyRight );

	// Powered
	/*------------------*/
	$lightning_footerPowered = __( '<p>Powered by <a href="https://wordpress.org/">WordPress</a> &amp; <a href="https://lightning.nagoya" target="_blank" title="Free WordPress Theme Lightning"> Lightning Theme</a> by Vektor,Inc. technology.</p>', 'lightning');
	echo apply_filters( 'lightning_footerPoweredCustom', $lightning_footerPowered );

}
