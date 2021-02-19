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
						'<!-- wp:vk-blocks/pr-blocks {"url1":"/service/","icon1":"far fa-file-alt","color1":"#1e73be","bgType1":"1","url2":"/company/","icon2":"far fa-building","color2":"#1e73be","bgType2":"1","url3":"/company/recruit/","icon3":"fas fa-user-tie","color3":"#1e73be","bgType3":"1"} -->
						<div class="wp-block-vk-blocks-pr-blocks vk_prBlocks row"><div class="vk_prBlocks_item col-sm-4"><a href="/service/" class="vk_prBlocks_item_link" target="_self" rel="noopener noreferrer"><div class="vk_prBlocks_item_icon_outer" style="background-color:transparent;border:1px solid #1e73be"><i style="color:#1e73be" class="far vk_prBlocks_item_icon fa-file-alt"></i></div><h3 class="vk_prBlocks_item_title vk_prBlocks_item_title-1">サービス案内</h3><p class="vk_prBlocks_item_summary vk_prBlocks_item_summary-1">弊社が提供するサービス＆ソリューションについてご紹介しています。経験豊富なスタッフがこだわりを持って取り組んでいます。</p></a></div><div class="vk_prBlocks_item col-sm-4"><a href="/company/" class="vk_prBlocks_item_link" target="_self" rel="noopener noreferrer"><div class="vk_prBlocks_item_icon_outer" style="background-color:transparent;border:1px solid #1e73be"><i style="color:#1e73be" class="far vk_prBlocks_item_icon fa-building"></i></div><h3 class="vk_prBlocks_item_title vk_prBlocks_item_title-2">会社案内</h3><p class="vk_prBlocks_item_summary vk_prBlocks_item_summary-2">弊社代表挨拶や会社の基本情報について記載しています。また、弊社の歴史なども紹介していますので是非ご覧ください。</p></a></div><div class="vk_prBlocks_item col-sm-4"><a href="/company/recruit/" class="vk_prBlocks_item_link" target="_self" rel="noopener noreferrer"><div class="vk_prBlocks_item_icon_outer" style="background-color:transparent;border:1px solid #1e73be"><i style="color:#1e73be" class="fas vk_prBlocks_item_icon fa-user-tie"></i></div><h3 class="vk_prBlocks_item_title vk_prBlocks_item_title-3">採用情報</h3><p class="vk_prBlocks_item_summary vk_prBlocks_item_summary-3">株式会社サンプルでは一緒に働く仲間を募集しています。自分で考えていろいろな事にチャレンジできるやりがいのある仕事です。</p></a></div></div>
						<!-- /wp:vk-blocks/pr-blocks -->',
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
					'page_page-name',
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