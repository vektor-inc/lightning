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
		$dynamic_css    = '
a { color:' . $color_key_dark . ' ; }
a:hover { color:' . $color_key . ' ; }
ul.gMenu a:hover { color:' . $color_key . '; }
.page-header { background-color:' . $color_key . '; }
h1.entry-title:first-letter,
.single h1.entry-title:first-letter { color:' . $color_key . '; }
h2,
.mainSection-title { border-top-color:' . $color_key . '; }
h3:after,
.subSection-title:after { border-bottom-color:' . $color_key . ';  }
.media .media-body .media-heading a:hover { color:' . $color_key . ';  }
ul.page-numbers li span.page-numbers.current { background-color:' . $color_key . '; }
.pager li > a { border-color:' . $color_key . ';color:' . $color_key . ';}
.pager li > a:hover { background-color:' . $color_key . ';color:#fff;}
footer { border-top-color:' . $color_key . '; }
dt { border-left-color:' . $color_key . '; }
@media (min-width: 768px){
  ul.gMenu > li > a:hover:after,
  ul.gMenu > li.current-post-ancestor > a:after,
  ul.gMenu > li.current-menu-item > a:after,
  ul.gMenu > li.current-menu-parent > a:after,
  ul.gMenu > li.current-menu-ancestor > a:after,
  ul.gMenu > li.current_page_parent > a:after,
  ul.gMenu > li.current_page_ancestor > a:after { border-bottom-color: ' . $color_key . ' ; }
  ul.gMenu > li > a:hover .gMenu_description { color: ' . $color_key . ' ; }
} /* @media (min-width: 768px) */';
		// delete before after space
		$dynamic_css = trim( $dynamic_css );
		// convert tab and br to space
		$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
		// Change multiple spaces to single space
		$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
		wp_add_inline_style( 'lightning-design-style', $dynamic_css );
	} // if ( isset($options['color_key'] && isset($options['color_key_dark'] ) {
}
