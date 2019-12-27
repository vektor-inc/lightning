<?php
/*-------------------------------------------*/
/*	Print head
/*-------------------------------------------*/
add_action( 'wp_head', 'lightning_print_css_origin', 3 );
function lightning_print_css_origin() {
	$options = get_option( 'lightning_theme_options' );
	if ( isset( $options['color_key'] ) && isset( $options['color_key_dark'] ) ) {
		$color_key      = esc_html( $options['color_key'] );
		$color_key_dark = esc_html( $options['color_key_dark'] );

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

		$dynamic_css .= '
.page-header { background-color:' . $color_key . '; }
h2,
.mainSection-title { border-top-color:' . $color_key . '; }
h3:after,
.subSection-title:after { border-bottom-color:' . $color_key . ';  }
ul.page-numbers li span.page-numbers.current { background-color:' . $color_key . '; }
.pager li > a { border-color:' . $color_key . ';color:' . $color_key . ';}
.pager li > a:hover { background-color:' . $color_key . ';color:#fff;}
.siteFooter { border-top-color:' . $color_key . '; }
dt { border-left-color:' . $color_key . '; }';

		// delete before after space
		$dynamic_css = trim( $dynamic_css );
		// convert tab and br to space
		$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
		// Change multiple spaces to single space
		$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
		wp_add_inline_style( 'lightning-design-style', $dynamic_css );
	} // if ( isset($options['color_key'] && isset($options['color_key_dark'] ) {
}

/*-------------------------------------------*/
/*	Your design skin Specific functions
/*-------------------------------------------*/

function lightning_origin2_header_scrolled_scripts() {
	if ( function_exists( 'wp_add_inline_script' ) ) {
		$script = "
		;(function($,document,window){
		$(document).ready(function($){
			/* Add scroll recognition class */
			$(window).scroll(function () {
				var scroll = $(this).scrollTop();
				if ($(this).scrollTop() > 160) {
					$('body').addClass('header_scrolled');
				} else {
					$('body').removeClass('header_scrolled');
				}
			});
		});
		})(jQuery,document,window);
		";
		// delete br
		$script = str_replace( PHP_EOL, '', $script );
		// delete tab
		$script = preg_replace( '/[\n\r\t]/', '', $script );
		wp_add_inline_script( 'jquery-core', $script, 'after' );
	}
}
add_action( 'wp_enqueue_scripts', 'lightning_origin2_header_scrolled_scripts' );


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
