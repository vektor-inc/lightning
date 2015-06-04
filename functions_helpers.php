<?php
/*-------------------------------------------*/
/*	Head logo
/*-------------------------------------------*/
/*	Chack use post top page
/*-------------------------------------------*/
/*	Chack post type info
/*-------------------------------------------*/
/*	Archive title
/*-------------------------------------------*/
/*	Pagination
/*-------------------------------------------*/

/*-------------------------------------------*/
/*	Head logo
/*-------------------------------------------*/
function bvII_print_headlogo() {
	$options = get_option('bvII_theme_options');
	if (isset($options['head_logo']) && $options['head_logo']){
		print '<img src="'.$options['head_logo'].'" alt="'.get_bloginfo('name').'" />';
	} else {
		echo bloginfo('name');
	}
}

/*-------------------------------------------*/
/*	Chack use post top page
/*-------------------------------------------*/
function bvII_get_page_for_posts(){
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
function bvII_get_post_type(){
	$page_for_posts = bvII_get_page_for_posts();

	// Get post type slug
	/*-------------------------------------------*/
	$postType['slug'] = get_post_type();
	if ( !$postType['slug'] ) {
	  global $wp_query;
	  if ($wp_query->query_vars['post_type']) {
	      $postType['slug'] = $wp_query->query_vars['post_type'];
	  } elseif(is_tax()) {
	  	// Case of tax archive and no posts
		$taxonomy = get_queried_object()->taxonomy;
		$postType['slug'] = get_taxonomy( $taxonomy )->object_type[0];	  	
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
		$postType['url'] = home_url().'/?post_type='.$postType['slug'];
	}

	$postType = apply_filters('bvII_postType_custom',$postType);
	return $postType;
}


/*-------------------------------------------*/
/*	Archive title
/*-------------------------------------------*/

function bvII_get_the_archive_title(){
   if ( is_category() ) {
        $title = single_cat_title( '', false );
    } elseif ( is_tag() ) {
        $title = single_tag_title( '', false );
    } elseif ( is_author() ) {
        $title = sprintf( __( 'Author: %s' ), '<span class="vcard">' . get_the_author() . '</span>' );
    } elseif ( is_year() ) {
        $title = get_the_date( _x( 'Y', 'yearly archives date format', 'bvII' ) );
    } elseif ( is_month() ) {
        $title = get_the_date( _x( 'F Y', 'monthly archives date format', 'bvII' ) );
    } elseif ( is_day() ) {
        $title = get_the_date( _x( 'F j, Y', 'daily archives date format', 'bvII' ) );
    } elseif ( is_tax( 'post_format' ) ) {
        if ( is_tax( 'post_format', 'post-format-aside' ) ) {
            $title = _x( 'Asides', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) {
            $title = _x( 'Galleries', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-image' ) ) {
            $title = _x( 'Images', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-video' ) ) {
            $title = _x( 'Videos', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-quote' ) ) {
            $title = _x( 'Quotes', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-link' ) ) {
            $title = _x( 'Links', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-status' ) ) {
            $title = _x( 'Statuses', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-audio' ) ) {
            $title = _x( 'Audio', 'post format archive title' );
        } elseif ( is_tax( 'post_format', 'post-format-chat' ) ) {
            $title = _x( 'Chats', 'post format archive title' );
        }
    } elseif ( is_post_type_archive() ) {
        $title = post_type_archive_title( '', false );
    } elseif ( is_tax() ) {
        $title = single_term_title( '', false );
    } elseif ( is_home() && !is_front_page() ){
    	$bvII_page_for_posts = bvII_get_page_for_posts();
    	$title = $bvII_page_for_posts['post_top_name'];
    } else {
        global $wp_query;
		// get post type
		$postType = $wp_query->query_vars['post_type'];
		if ( $postType ) {
			$pageTitle = get_post_type_object($postType)->labels->name;
		} else {
			$title = __( 'Archives' );
		}
    }

    return apply_filters( 'bvII_get_the_archive_title', $title );
}


/*-------------------------------------------*/
/*	Pagination
/*-------------------------------------------*/

function bvII_pagination($max_num_pages = '', $range = 1) {
	$showitems = ($range * 2)+1;

	global $paged;
	if(empty($paged)) $paged = 1;

	if($max_num_pages == '') {
		global $wp_query;
		// 最後のページ
		$max_num_pages = $wp_query->max_num_pages;
		if(!$max_num_pages) {
			 $max_num_pages = 1;
		}
	}

	if(1 != $max_num_pages) {
		echo '<nav><ul class="pagination">'."\n";

		// Prevリンク
		// 現在のページが２ページ目以降の場合
		if ($paged > 1) echo '<li><a href="'.get_pagenum_link($paged - 1).'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>'."\n";

		// 今のページからレンジを引いて2以上ある場合 && 最大表示アイテム数より最第ページ数が大きい場合
		// （レンジ数のすぐ次の場合は表示する）
		// 1...３４５
		if ( $paged-$range >= 2 && $max_num_pages > $showitems ) 
			echo '<li><a href="'.get_pagenum_link(1).'">1</a></li>'."\n";
		// 今のページからレンジを引いて3以上ある場合 && 最大表示アイテム数より最第ページ数が大きい場合
		if ( $paged-$range >= 3 && $max_num_pages > $showitems ) 
			echo '<li class="disabled"><span><span aria-hidden="true">&hellip;</span></span></li>'."\n";

		// レンジより前に追加する数
		$addPrevCount = $paged+$range-$max_num_pages;
		// レンジより後に追加する数
		$addNextCount = -($paged-1-$range); // 今のページ数を遡ってカウントするために-1
		// アイテムループ
		for ($i=1; $i <= $max_num_pages; $i++) {
			// 表示するアイテム
			if ($paged == $i) {
				$pageItem = '<li class="active"><span>'.$i.'<span class="sr-only">(current)</span></span></li>'."\n";
			} else {
				$pageItem = '<li><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>'."\n";
			}

			// 今のページからレンジを引いた数～今のページからレンジを足した数まで || 最大ページ数が最大表示アイテム数以下の場合
			if ( ( $paged-$range <= $i && $i<= $paged+$range ) || $max_num_pages <= $showitems ) {
				echo $pageItem;
				// 今のページからレンジを引くと負数になる場合 && 今のページ+レンジ+負数をレンジに加算した数まで
			} else if ( $paged-1-$range < 0 && $paged+$range+$addNextCount >= $i ) {
				echo $pageItem;
			// 今のページからレンジを足すと　最後のページよりも大きくなる場合 && 今のページ+レンジ+負数をレンジに加算した数まで
			} else if ( $paged+$range > $max_num_pages && $paged-$range-$addPrevCount <= $i ) {
				echo $pageItem;
			}
		}

		// 現在のページにレンジを足しても最後のページ数より２以上小さい時 && 最大表示アイテム数より最第ページ数が大きい場合
		if ( $paged+$range <= $max_num_pages-2 && $max_num_pages > $showitems ) 
			echo '<li class="disabled"><span><span aria-hidden="true">&hellip;</span></span></li>'."\n";
		if ( $paged+$range <= $max_num_pages-1 && $max_num_pages > $showitems ) 
			echo '<li><a href="'.get_pagenum_link($max_num_pages).'">'.$max_num_pages.'</a></li>'."\n";
		// Nextリンク
		if ($paged < $max_num_pages) echo '<li><a href="'.get_pagenum_link($paged + 1).'" aria-label="Next"><span aria-hidden="true">&raquo;</a></span></li>'."\n";

		echo "</ul></nav>\n";
	 }
}


