<?php
/*
-------------------------------------------*/
/*
  Print head
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_print_css_origin', 5 );
function lightning_print_css_origin() {
	$options = get_option( 'lightning_theme_options' );

	$colors 		= lightning_get_colors();
	$color_key		= $colors['color_key'];
	$color_key_dark = $colors['color_key_dark'];

	// Text Color ///////////////////
	$dynamic_css = '.media .media-body .media-heading a:hover { color:' . $color_key . ';  }';

	// Global Menu //////////////////
	$dynamic_css .= '
	@media (min-width: 768px){
		.gMenu > li:before,
		.gMenu > li.menu-item-has-children::after { border-bottom-color:' . $color_key_dark . ' }
		.gMenu li li { background-color:' . $color_key_dark . ' }
		.gMenu li li a:hover { background-color:' . $color_key . '; }
	} /* @media (min-width: 768px) */';
	if ( ! empty( $options['color_header_bg'] ) ) {
		$color_header_bg = esc_html( $options['color_header_bg'] );
		if ( lightning_check_color_mode( $color_header_bg ) == 'dark' ) {
			// Dark Color ///////////////////
			$dynamic_css .= '
		@media (min-width: 768px){
			ul.gMenu > li > a:after { border-bottom-color: rgba(255,255,255,0.9 );}
		}';
		} else {
			// Light Color ///////////////////
		}
	}// if ( ! empty( $options['color_header_bg'] ) ) {

	// When pro version if this .page-header exist that VK Page Header Can't over write.
	// @since 11.3.4
	if ( ! class_exists('Vk_Page_Header') ){
		$dynamic_css .= '.page-header { background-color:' . $color_key . '; }';
	}

	$dynamic_css .= '
h2,
.mainSection-title { border-top-color:' . $color_key . '; }
h3:after,
.subSection-title:after { border-bottom-color:' . $color_key . ';  }
ul.page-numbers li span.page-numbers.current,
.page-link dl .post-page-numbers.current { background-color:' . $color_key . '; }
.pager li > a { border-color:' . $color_key . ';color:' . $color_key . ';}
.pager li > a:hover { background-color:' . $color_key . ';color:#fff;}
.siteFooter { border-top-color:' . $color_key . '; }
dt { border-left-color:' . $color_key . '; }
:root {
	--g_nav_main_acc_icon_open_url:url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-acc-icon-open-black.svg);
	--g_nav_main_acc_icon_close_url: url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-close-black.svg);
	--g_nav_sub_acc_icon_open_url: url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-acc-icon-open-white.svg);
	--g_nav_sub_acc_icon_close_url: url(' . get_template_directory_uri() . '/inc/vk-mobile-nav/package/images/vk-menu-close-white.svg);
}
';

	// delete before after space
	$dynamic_css = trim( $dynamic_css );
	// convert tab and br to space
	$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
	// Change multiple spaces to single space
	$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
	wp_add_inline_style( 'lightning-design-style', $dynamic_css );
}

/*
-------------------------------------------*/
/*
  Your design skin Specific functions
/*-------------------------------------------*/

add_filter( 'lightning_localize_options', 'lightning_origin2_add_js_option', 10, 1 );
function lightning_origin2_add_js_option( $options ) {
	$options['header_scrool'] = true;
	return $options;
}


// lightning headfix disabel
add_filter( 'lightning_headfix_enable', 'lightning_origin2_headfix_disabel' );
function lightning_origin2_headfix_disabel() {
	return false;
}

// lightning header height changer disabel

add_filter( 'lightning_header_height_changer_enable', 'lightning_origin2_header_height_changer_disabel' );
function lightning_origin2_header_height_changer_disabel() {
	return false;
}
