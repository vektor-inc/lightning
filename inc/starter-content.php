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
						'<!-- wp:columns -->
						<div class="wp-block-columns"><!-- wp:column -->
						<div class="wp-block-column">
						<!-- wp:image {"className":"border"} -->
						<figure class="wp-block-image border"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/logo_Lightning.png" alt=""/></figure>
						<!-- /wp:image -->
						
						<!-- wp:heading {"level":5} -->
						<h5>About Lightning</h5>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph -->
						<p>Lightning is a WordPress theme designed to facilitate the creation of a website without specialized knowledge of web production.</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"borderRadius":0,"className":"is-style-outline"} -->
						<div class="wp-block-button is-style-outline"><a class="wp-block-button__link no-border-radius" href="https://wordpress.org">Read more</a></div>
						<!-- /wp:button --></div>
						<!-- /wp:buttons -->
						
						<!-- wp:spacer {"height":20} -->
						<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
						<!-- /wp:spacer --></div>
						<!-- /wp:column -->
						
						<!-- wp:column -->
						<div class="wp-block-column">
						<!-- wp:image {"className":"border"} -->
						<figure class="wp-block-image border"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/logo_ExUnit.png" alt=""/></figure>
						<!-- /wp:image -->
						
						<!-- wp:heading {"level":5} -->
						<h5>ExUnit</h5>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph -->
						<p>By using the multi-function plug-in "VK All in One Expansion Unit (free)", you can use the various useful functions and rich widgets.</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"borderRadius":0,"className":"is-style-outline"} -->
						<div class="wp-block-button is-style-outline"><a class="wp-block-button__link no-border-radius" href="https://wordpress.org">Read more</a></div>
						<!-- /wp:button --></div>
						<!-- /wp:buttons -->
						
						<!-- wp:spacer {"height":20} -->
						<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
						<!-- /wp:spacer --></div>
						<!-- /wp:column -->
						
						<!-- wp:column -->
						<div class="wp-block-column">
						<!-- wp:image {"className":"border"} -->
						<figure class="wp-block-image border"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/logo_VK_Blocks.png" alt=""/></figure>
						<!-- /wp:image -->
						
						<!-- wp:heading {"level":5} -->
						<h5>VK Blocks</h5>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph -->
						<p>Plugin VK Blocks is a block library that adds various blocks and styles and functions that are useful for building your business websites.</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"borderRadius":0,"className":"is-style-outline"} -->
						<div class="wp-block-button is-style-outline"><a class="wp-block-button__link no-border-radius" href="https://wordpress.org">Read more</a></div>
						<!-- /wp:button --></div>
						<!-- /wp:buttons -->
						
						<!-- wp:spacer {"height":20} -->
						<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
						<!-- /wp:spacer --></div>
						<!-- /wp:column -->
						
						<!-- wp:column -->
						<div class="wp-block-column">
						<!-- wp:image {"className":"border"} -->
						<figure class="wp-block-image border"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/logo_FilterSearch.png" alt=""/></figure>
						<!-- /wp:image -->
						
						<!-- wp:heading {"level":5} -->
						<h5>VK Filter Search</h5>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph -->
						<p>Plugin VK Filter Search (free) can create the Filter Serach Block on your edit screen. It enable to filter search on post type and terms.</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"borderRadius":0,"className":"is-style-outline"} -->
						<div class="wp-block-button is-style-outline"><a class="wp-block-button__link no-border-radius" href="https://wordpress.org">Read more</a></div>
						<!-- /wp:button --></div>
						<!-- /wp:buttons -->
						
						<!-- wp:spacer {"height":20} -->
						<div style="height:20px" aria-hidden="true" class="wp-block-spacer"></div>
						<!-- /wp:spacer --></div>
						<!-- /wp:column --></div>
						<!-- /wp:columns -->
						
						<!-- wp:cover {"customOverlayColor":"#f3f3f3","minHeight":250,"contentPosition":"center center","align":"full"} -->
						<div class="wp-block-cover alignfull has-background-dim" style="background-color:#f3f3f3;min-height:250px"><div class="wp-block-cover__inner-container"><!-- wp:spacer {"height":40} -->
						<div style="height:40px" aria-hidden="true" class="wp-block-spacer"></div>
						<!-- /wp:spacer -->
						
						<!-- wp:columns {"verticalAlignment":"center"} -->
						<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"33.33%"} -->
						<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:33.33%"><!-- wp:image {"align":"center"} -->
						<div class="wp-block-image"><figure class="aligncenter size-large"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/logo_Lightning.png" alt="" /></figure></div>
						<!-- /wp:image --></div>
						<!-- /wp:column -->
						
						<!-- wp:column {"verticalAlignment":"center","width":"66.66%"} -->
						<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.66%"><!-- wp:heading {"level":4,"textColor":"black"} -->
						<h4 class="has-black-color has-text-color">Layout Control</h4>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph {"textColor":"black"} -->
						<p class="has-black-color has-text-color">Lightning has column control function that you can secific column number from "Appearance &gt; Customize &gt; Lightning Design Setting" and specific post edit screen.</p>
						<!-- /wp:paragraph --></div>
						<!-- /wp:column --></div>
						<!-- /wp:columns --></div></div>
						<!-- /wp:cover -->

						<!-- wp:spacer {"height":30} -->
						<div style="height:30px" aria-hidden="true" class="wp-block-spacer"></div>
						<!-- /wp:spacer -->

						<!-- wp:heading -->
						<h2>News &amp; Information</h2>
						<!-- /wp:heading -->
						<!-- wp:latest-posts {"displayPostContent":true,"displayPostDate":true,"displayFeaturedImage":true,"featuredImageAlign":"left","addLinkToFeaturedImage":true} /-->',
					)
				),
			),
			'about',
			'contact',
			'blog',
			'sitemap' => array(
				'post_type'		=> 'page',
				'post_title'	=> __( 'Site Map', 'lightning' ),
				'post_name'		=> 'sitemap',
				'post_content'	=> join(
					'',
					array(
						'<!-- wp:vk-blocks/sitemap /-->',
					)
				),
			),
			'renew-release' => array(
				'post_type'    => 'post',
				'post_title'   => 'The website was renewed.',
				'thumbnail'    => '{{image-opening}}',
				'post_content' => join(
					'',
					array(
						'<!-- wp:paragraph -->',
						'<p>We have renewed our website.</p>',
						'<!-- /wp:paragraph -->',
						'<!-- wp:paragraph -->',
						'<p>With this renewal, we have redesigned the website so that it is easier to see and convey information to everyone.</p>',
						'<!-- /wp:paragraph -->',
						'<!-- wp:paragraph -->',
						'<p>Thank you for your continued patronage.</p>',
						'<!-- /wp:paragraph -->',
					)
				),
			),
			'hellow-lightning' => array(
				'post_type'    => 'post',
				'post_title'   => 'Hellow Lightning',
				'thumbnail'    => '{{image-opening}}',
				'post_content' => join(
					'',
					array(
						'<!-- wp:paragraph -->',
						'<p>Lightning is includes to a variety of ideas for making it easier to business site. Please experience the ease of use of the Lightning.</p>',
						'<!-- /wp:paragraph -->',
					)
				),
			),
		),
		'widgets' => array(
			'vk-mobile-nav-upper' => array(
				'search_custom' => array( 'search', array(
					'title' => '',
				) ),
			),

			'common-side-bottom-widget-area' => array(
				'search_custom' => array( 'search', array(
					'title' => '',
				) ),
				'text_custom' => [
					'text',
					[
						'title' => 'Layout Control',
						'text' => 'Lightning has column control function that you can secific column number from "Appearance > Customize > Lightning Design Setting" and specific post edit page.'
					]
				],
			),
			'footer-widget-1' => array(
				'text_custom' => array( 'text', array(
						'title' => get_bloginfo( 'name' ),
						'text' => '<b>Address</b><br>
						123 Main Street<br>
						New York, NY 10001<br>
						
						<b>Hours</b><br>
						Monday–Friday: 9:00AM–5:00PM<br>
						Saturday & Sunday: 11:00AM–3:00PM<br>
						<br>
						This area recommend replace to "VK Profile" widget. "VK Profile" widget is include in plugin "VK All in One expansion Unit".'
				) ),
			),
			'footer-widget-2' => array(
				'nav_menu' => array( 'nav_menu', array(
					'title' => 'Contents',
					'nav_menu' => -1,
				) ),
			),
			'footer-widget-3' => array(
				'recent_entries' => array( 'recent-posts', array(
					'title' => 'Recent Posts',
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
				),
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