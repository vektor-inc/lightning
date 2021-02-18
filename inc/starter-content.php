<?php
update_option( 'fresh_site', 1 );
add_action( 'after_setup_theme', 'lightning_add_starter_content' );
if ( is_customize_preview() ) {
    add_theme_support( 'starter-content', lightning_add_starter_content() );
}
function lightning_add_starter_content(){
	$starter_content = array(
		'posts'       => array(
			'front' => array(
				'post_type'    => 'page',
				'post_title'   => 'HOME',
				'thumbnail'    => '{{image-opening}}',
				'post_content' => join(
					'',
					array(
						'<!-- wp:heading -->',
						'<h2>News &amp; Information</h2>',
						'<!-- /wp:heading -->',
						'<!-- wp:latest-posts {"displayPostContent":true,"displayPostDate":true,"displayFeaturedImage":true,"featuredImageAlign":"left","addLinkToFeaturedImage":true} /-->',
					)
				),
			),
			'about',
			'contact',
			'blog',
			'sitemap' => array(
				'post_type'    => 'page',
				'post_title'   => __( 'Site Map', 'lightning' ),
				'post_content' => join(
					'',
					array(
						'<!-- wp:vk-blocks/sitemap /-->',
					)
				),
			),
			'post1' => array(
				'post_type'    => 'post',
				'post_title'   => '投稿1です',
				'thumbnail'    => '{{image-opening}}',
				'post_content' => join(
					'',
					array(
						'<!-- wp:paragraph -->',
						'<p>投稿サンプルです。</p>',
						'<!-- /wp:paragraph -->',
					)
				),
			),
			'post2' => array(
				'post_type'    => 'post',
				'post_title'   => '重要なお知らせ',
				'thumbnail'    => '{{image-opening}}',
				'post_content' => join(
					'',
					array(
						'<!-- wp:paragraph -->',
						'<p>投稿サンプルです。</p>',
						'<!-- /wp:paragraph -->',
					)
				),
			),
			'post3' => array(
				'post_type'    => 'post',
				'post_title'   => '制作実績を更新しました',
				'thumbnail'    => '{{image-opening}}',
				'post_content' => join(
					'',
					array(
						'<!-- wp:paragraph -->',
						'<p>投稿サンプルです。</p>',
						'<!-- /wp:paragraph -->',
					)
				),
			),
		),
		'widgets' => array(
			'common-side-bottom-widget-area' => array(
				'meta_custom' => array( 'meta', array(
					'title' => 'Pre-hydrated meta widget.',
				) ),
			),
		),
		'options'     => array(
			'show_on_front'  => 'page',
			'page_on_front'  => '{{front}}',
			'page_for_posts' => '{{blog}}',
		),
		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus'   => array(
			// Assign a menu to the "primary" location.
			'Header'  => array(
				'name'  => __( 'Header Nav', 'lightning' ),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				),
			),
			'Footer'  => array(
				'name'  => __( 'Footer Nav', 'lightning' ),
				'items' => array(
					'page_contact',
					'page_sitemap',
				),
				// 'items' => array(
				// 	// 'page_privacy',
				// 	'page_sitemap',
				// ),
			),
		),
	);
    return $starter_content;
}

// Plugin widget added using filters
function myprefix_starter_content_add_widget( $content, $config ) {
    if ( isset( $content['widgets']['sidebar-1'] ) ) {
        $content['widgets']['sidebar-1']['a_custom_widget'] = array(
            'my_custom_widget', array(
                'title' => 'A Special Plugin Widget',
            ),
        );
    }
    return $content;
}
add_filter( 'get_theme_starter_content', 'myprefix_starter_content_add_widget', 10, 2 );