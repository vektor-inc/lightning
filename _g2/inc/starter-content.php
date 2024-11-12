<?php
function lightning_add_starter_content() {
	$starter_content = array(
		'posts'     => array(
			'front'   => array(
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
						<p>' . esc_html_x( 'Lightning is a WordPress theme designed to facilitate the creation of a website without specialized knowledge of web production.', 'Theme starter content', 'lightning' ) . '</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"borderRadius":0,"className":"is-style-outline"} -->
						<div class="wp-block-button is-style-outline"><a class="wp-block-button__link no-border-radius" href="https://wordpress.org/themes/lightning/">Read more</a></div>
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
						<p>' . esc_html_x( 'By using the multi-function plug-in "VK All in One Expansion Unit (free)", you can use the various useful functions and rich widgets.', 'Theme starter content', 'lightning' ) . '</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"borderRadius":0,"className":"is-style-outline"} -->
						<div class="wp-block-button is-style-outline"><a class="wp-block-button__link no-border-radius" href="https://wordpress.org/plugins/vk-all-in-one-expansion-unit/">Read more</a></div>
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
						<p>' . esc_html_x( 'Plugin VK Blocks is a block library that adds various blocks and styles and functions that are useful for building your business websites.', 'Theme starter content', 'lightning' ) . '</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"borderRadius":0,"className":"is-style-outline"} -->
						<div class="wp-block-button is-style-outline"><a class="wp-block-button__link no-border-radius" href="https://wordpress.org/plugins/vk-blocks/">Read more</a></div>
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
						<p>' . esc_html_x( 'Plugin VK Filter Search (free) can create the Filter Serach Block on your edit screen. It enable to filter search on post type and terms.', 'Theme starter content', 'lightning' ) . '</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"borderRadius":0,"className":"is-style-outline"} -->
						<div class="wp-block-button is-style-outline"><a class="wp-block-button__link no-border-radius" href="https://wordpress.org/plugins/vk-filter-search">Read more</a></div>
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
						<p class="has-black-color has-text-color">' . esc_html_x( 'Lightning has column control function that you can specific column number from "Appearance &gt; Customize &gt; Lightning Design Setting" and specific post edit screen.', 'Theme starter content', 'lightning' ) . '</p>
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
				'post_type'    => 'page',
				'post_title'   => __( 'Site Map', 'lightning' ),
				'post_name'    => 'sitemap',
				'post_content' => join(
					'',
					array(
						'<!-- wp:vk-blocks/sitemap /-->',
					)
				),
			),
		),
		'widgets'   => array(
			'vk-mobile-nav-upper'            => array(
				'search_custom' => array(
					'search',
					array(
						'title' => '',
					),
				),
			),

			'common-side-bottom-widget-area' => array(
				'search_custom' => array(
					'search',
					array(
						'title' => '',
					),
				),
			),
			'footer-widget-1'                => array(
				'text_custom' => array(
					'text',
					array(
						'title' => get_bloginfo( 'name', 'display' ),
						'text'  => '<b>Address</b><br>
						123 Main Street<br>
						New York, NY 10001<br>
						
						<b>Hours</b><br>
						Monday–Friday: 9:00AM–5:00PM<br>
						Saturday & Sunday: 11:00AM–3:00PM<br>
						<br>
						' . esc_html_x( 'This area recommend replace to "VK Profile" widget. "VK Profile" widget is include in plugin "VK All in One expansion Unit".', 'Theme starter content', 'lightning' ),
					),
				),
			),
			'footer-widget-2'                => array(
				'nav_menu' => array(
					'nav_menu',
					array(
						'title'    => 'Contents',
						'nav_menu' => -1,
					),
				),
			),
			'footer-widget-3'                => array(
				'recent_entries' => array(
					'recent-posts',
					array(
						'title' => 'Recent Posts',
					),
				),
			),
		),
		'options'   => array(
			'show_on_front'              => 'page',
			'page_on_front'              => '{{front}}',
			'page_for_posts'             => '{{blog}}',
			'lightning_theme_options'    => array(
				'front_pr_display' => false,
				'layout'           => array(
					'front-page' => 'col-one-no-subsection',
				),
				'theme_json'       => true,
			),
			'lightning_theme_generation' => 'g2',
		),
		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus' => array(
			// Assign a menu to the "primary" location.
			'Header' => array(
				'name'  => __( 'Header Nav', 'lightning' ),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				),
			),
			'Footer' => array(
				'name'  => __( 'Footer Nav', 'lightning' ),
				'items' => array(
					'page_contact',
				),
			),
		),
	);
	return $starter_content;
}
