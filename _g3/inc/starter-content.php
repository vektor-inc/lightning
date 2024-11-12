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
						'<!-- wp:columns {"style":{"spacing":{"blockGap":{"top":"var:preset|spacing|50","left":"var:preset|spacing|50"},"margin":{"bottom":"var:preset|spacing|60"}}}} -->
						<div class="wp-block-columns" style="margin-bottom:var(--wp--preset--spacing--60)"><!-- wp:column -->
						<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
						<figure class="wp-block-image size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/sample-image-gray.png" alt=""/></figure>
						<!-- /wp:image -->
						
						<!-- wp:heading {"level":5,"style":{"spacing":{"margin":{"top":"1.5rem","bottom":"1rem"}}}} -->
						<h5 class="wp-block-heading" id="vk-blocks" style="margin-top:1.5rem;margin-bottom:1rem">Title Text</h5>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph -->
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"top":"0.5em","bottom":"0.5em","left":"1em","right":"1em"}},"border":{"radius":"0px"}},"className":"is-style-outline","fontSize":"small"} -->
						<div class="wp-block-button has-custom-font-size is-style-outline has-small-font-size"><a class="wp-block-button__link wp-element-button" href="" style="border-radius:0px;padding-top:0.5em;padding-right:1em;padding-bottom:0.5em;padding-left:1em">Read more</a></div>
						<!-- /wp:button --></div>
						<!-- /wp:buttons --></div>
						<!-- /wp:column -->
						
						<!-- wp:column -->
						<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
						<figure class="wp-block-image size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/sample-image-gray.png" alt=""/></figure>
						<!-- /wp:image -->
						
						<!-- wp:heading {"level":5,"style":{"spacing":{"margin":{"top":"1.5rem","bottom":"1rem"}}}} -->
						<h5 class="wp-block-heading" id="vk-blocks" style="margin-top:1.5rem;margin-bottom:1rem">Title Text</h5>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph -->
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"top":"0.5em","bottom":"0.5em","left":"1em","right":"1em"}},"border":{"radius":"0px"}},"className":"is-style-outline","fontSize":"small"} -->
						<div class="wp-block-button has-custom-font-size is-style-outline has-small-font-size"><a class="wp-block-button__link wp-element-button" href="" style="border-radius:0px;padding-top:0.5em;padding-right:1em;padding-bottom:0.5em;padding-left:1em">Read more</a></div>
						<!-- /wp:button --></div>
						<!-- /wp:buttons --></div>
						<!-- /wp:column -->
						
						<!-- wp:column -->
						<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
						<figure class="wp-block-image size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/sample-image-gray.png" alt=""/></figure>
						<!-- /wp:image -->
						
						<!-- wp:heading {"level":5,"style":{"spacing":{"margin":{"top":"1.5rem","bottom":"1rem"}}}} -->
						<h5 class="wp-block-heading" id="vk-blocks" style="margin-top:1.5rem;margin-bottom:1rem">Title Text</h5>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph -->
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"top":"0.5em","bottom":"0.5em","left":"1em","right":"1em"}},"border":{"radius":"0px"}},"className":"is-style-outline","fontSize":"small"} -->
						<div class="wp-block-button has-custom-font-size is-style-outline has-small-font-size"><a class="wp-block-button__link wp-element-button" href="" style="border-radius:0px;padding-top:0.5em;padding-right:1em;padding-bottom:0.5em;padding-left:1em">Read more</a></div>
						<!-- /wp:button --></div>
						<!-- /wp:buttons --></div>
						<!-- /wp:column -->
						
						<!-- wp:column -->
						<div class="wp-block-column"><!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
						<figure class="wp-block-image size-full"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/sample-image-gray.png" alt=""/></figure>
						<!-- /wp:image -->
						
						<!-- wp:heading {"level":5,"style":{"spacing":{"margin":{"top":"1.5rem","bottom":"1rem"}}}} -->
						<h5 class="wp-block-heading" id="vk-blocks" style="margin-top:1.5rem;margin-bottom:1rem">Title Text</h5>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph -->
						<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
						<!-- /wp:paragraph -->
						
						<!-- wp:buttons -->
						<div class="wp-block-buttons"><!-- wp:button {"style":{"spacing":{"padding":{"top":"0.5em","bottom":"0.5em","left":"1em","right":"1em"}},"border":{"radius":"0px"}},"className":"is-style-outline","fontSize":"small"} -->
						<div class="wp-block-button has-custom-font-size is-style-outline has-small-font-size"><a class="wp-block-button__link wp-element-button" href="" style="border-radius:0px;padding-top:0.5em;padding-right:1em;padding-bottom:0.5em;padding-left:1em">Read more</a></div>
						<!-- /wp:button --></div>
						<!-- /wp:buttons --></div>
						<!-- /wp:column --></div>
						<!-- /wp:columns -->
						
						<!-- wp:cover {"customOverlayColor":"#f3f3f3","isUserOverlayColor":true,"minHeight":250,"contentPosition":"center center","align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|60","bottom":"var:preset|spacing|60"}}},"className":"is-light"} -->
						<div class="wp-block-cover alignfull is-light" style="padding-top:var(--wp--preset--spacing--60);padding-bottom:var(--wp--preset--spacing--60);min-height:250px"><span aria-hidden="true" class="wp-block-cover__background has-background-dim-100 has-background-dim" style="background-color:#f3f3f3"></span><div class="wp-block-cover__inner-container"><!-- wp:columns {"verticalAlignment":"center","style":{"spacing":{"blockGap":{"left":"var:preset|spacing|50"}}}} -->
						<div class="wp-block-columns are-vertically-aligned-center"><!-- wp:column {"verticalAlignment":"center","width":"33.33%"} -->
						<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:33.33%"><!-- wp:image {"align":"center"} -->
						<figure class="wp-block-image aligncenter"><img src="' . esc_url( get_template_directory_uri() ) . '/assets/images/logo_Lightning.png" alt=""/></figure>
						<!-- /wp:image --></div>
						<!-- /wp:column -->
						
						<!-- wp:column {"verticalAlignment":"center","width":"66.66%"} -->
						<div class="wp-block-column is-vertically-aligned-center" style="flex-basis:66.66%"><!-- wp:heading {"level":4,"textColor":"black"} -->
						<h4 class="wp-block-heading has-black-color has-text-color">Layout Control</h4>
						<!-- /wp:heading -->
						
						<!-- wp:paragraph {"textColor":"black"} -->
						<p class="has-black-color has-text-color">Lightning has column control function that you can specific column number from "Appearance &gt; Customize &gt; Lightning Layout Setting" and specific post edit screen.</p>
						<!-- /wp:paragraph --></div>
						<!-- /wp:column --></div>
						<!-- /wp:columns --></div></div>
						<!-- /wp:cover -->
						
						<!-- wp:spacer {"height":"60px"} -->
						<div style="height:60px" aria-hidden="true" class="wp-block-spacer"></div>
						<!-- /wp:spacer -->
						
						<!-- wp:heading -->
						<h2 class="wp-block-heading">News &amp; Information</h2>
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
			'footer-before-widget'           => array(),
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
				'layout'     => array(
					'front-page' => 'col-one-no-subsection',
				),
				'theme_json' => true,
			),
			'lightning_theme_generation' => 'g3',
		),
		// Set up nav menus for each of the two areas registered in the theme.
		'nav_menus' => array(
			// Assign a menu to the "primary" location.
			'global-nav' => array(
				'name'  => __( 'Header Nav', 'lightning' ),
				'items' => array(
					'link_home', // Note that the core "home" page is actually a link in case a static front page is not used.
					'page_about',
					'page_blog',
					'page_contact',
				),
			),
			'footer-nav' => array(
				'name'  => __( 'Footer Nav', 'lightning' ),
				'items' => array(
					'page_contact',
				),
			),
		),
	);
	return $starter_content;
}
