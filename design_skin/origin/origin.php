<?php
/*-------------------------------------------*/
/*	Print head
/*-------------------------------------------*/
add_action( 'wp_head','lightning_print_css_origin', 160);
function lightning_print_css_origin(){
	$options = get_option('lightning_theme_options');
	if ( isset($options['color_key']) && isset($options['color_key_dark']) ) {
	$color_key = esc_html($options['color_key']);
	$color_key_dark = esc_html($options['color_key_dark']);
	?>
<!-- [ Ligthning Origin ] -->
<style type="text/css">
ul.gMenu a:hover { color:<?php echo $color_key;?>; }
.page-header { background-color:<?php echo $color_key;?>; }
h1.entry-title:first-letter,
.single h1.entry-title:first-letter { color:<?php echo $color_key;?>; }
h2,
.mainSection-title { border-top-color:<?php echo $color_key;?> }
h3:after,
.subSection-title:after { border-bottom-color:<?php echo $color_key; ?>; }
.media .media-body .media-heading a:hover { color:<?php echo $color_key; ?>; }
ul.page-numbers li span.page-numbers.current { background-color:<?php echo $color_key;?>; }
.pager li > a { border-color:<?php echo $color_key;?>;color:<?php echo $color_key;?>;}
.pager li > a:hover { background-color:<?php echo $color_key;?>;color:#fff;}
footer { border-top-color:<?php echo $color_key	;?> }
dt { border-left-color:<?php echo $color_key  ;?>; }
@media (min-width: 768px){
  ul.gMenu > li > a:hover:after,
  ul.gMenu > li.current-post-ancestor > a:after,
  ul.gMenu > li.current-menu-item > a:after,
  ul.gMenu > li.current-menu-parent > a:after,
  ul.gMenu > li.current-menu-ancestor > a:after,
  ul.gMenu > li.current_page_parent > a:after,
  ul.gMenu > li.current_page_ancestor > a:after { border-bottom-color: <?php echo $color_key ;?> }
  ul.gMenu > li > a:hover .gMenu_description { color: <?php echo $color_key ;?>; }
} /* @media (min-width: 768px) */
</style>
<!-- [ / Ligthning Origin ] -->
<?php } // if ( isset($options['color_key'] && isset($options['color_key_dark'] ) {
}
