<?php
/*-------------------------------------------*/
/*	Print head color style
/*-------------------------------------------*/
// !! Please rewrite the part of the [sample] to your design skin name.
add_action( 'wp_head', 'lightning_origin2_print_css', 3 );
function lightning_origin2_print_css() {
	$options        = get_option( 'lightning_theme_options' );
	$color_key      = ( isset( $options['color_key'] ) ) ? esc_html( $options['color_key'] ) : '#337ab7';
	$color_key_dark = ( isset( $options['color_key_dark'] ) ) ? esc_html( $options['color_key_dark'] ) : '#2e6da4';
	$fort_options   = get_option( 'lightning_fort_options' );
	$color_taste    = ( isset( $fort_options['color_taste'] ) ) ? esc_html( $fort_options['color_taste'] ) : 'thick';
	$dynamic_css    = '/* Fort */
dt { border-left-color:' . $color_key . '; }
ul.page-numbers li span.page-numbers.current { background-color:' . $color_key . '; }

.mainSection h2,
.mainSection-title,
.subSection .widget .subSection-title { border-top-color:' . $color_key . '; }

.siteFooter {  border-top-color:' . $color_key . '; }

@media (min-width: 992px){
.gMenu_outer { border-top-color:' . $color_key . '; }
ul.gMenu > li:hover > a .gMenu_description,
ul.gMenu > li.current-post-ancestor > a .gMenu_description,
ul.gMenu > li.current-menu-item > a .gMenu_description,
ul.gMenu > li.current-menu-parent > a .gMenu_description,
ul.gMenu > li.current-menu-ancestor > a .gMenu_description,
ul.gMenu > li.current_page_parent > a .gMenu_description,
ul.gMenu > li.current_page_ancestor > a .gMenu_description { color: ' . $color_key . '; }
.gMenu_outer { border-top-color:' . $color_key_dark . '; }
} /* @media (min-width: 768px){ */

.btn-default { border-color:#e5e5e5; color:#535353; }
';
	// delete before after space
	$dynamic_css = trim( $dynamic_css );
	// convert tab and br to space
	$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
	// Change multiple spaces to single space
	$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
	wp_add_inline_style( 'lightning-design-style', $dynamic_css );
}

/*-------------------------------------------*/
/*	Your design skin Specific functions
/*-------------------------------------------*/

// lightning headfix disabel

/*スクロールに応じてヘッダーを一旦スクロールさせるため、標準のヘッダー固定は解除する必要がある */
add_filter( 'lightning_headfix_enable', 'lightning_origin2_headfix_disabel' );
function lightning_origin2_headfix_disabel() {
	return false;
}

// lightning header height changer disabel

add_filter( 'lightning_header_height_changer_enable', 'lightning_origin2_header_height_changer_disabel' );
function lightning_origin2_header_height_changer_disabel() {
	return false;
}
