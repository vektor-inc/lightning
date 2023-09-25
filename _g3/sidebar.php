<div class="<?php lightning_the_class_name( 'sub-section' ); ?>">
<?php do_action( 'lightning_sub_section_prepend' ); ?>
<?php
if ( is_active_sidebar( 'common-side-top-widget-area' ) ) {
	dynamic_sidebar( 'common-side-top-widget-area' );
}

if ( is_front_page() ) {
	if ( is_active_sidebar( 'front-side-top-widget-area' ) ) {
		dynamic_sidebar( 'front-side-top-widget-area' );
	}
} else if ( is_search() ) {
	if ( is_active_sidebar( 'search-side-widget-area' ) ) {
		dynamic_sidebar( 'search-side-widget-area' );
	}
} else {
	// Display post type widget area.
	$post_type_info    = VK_Helpers::get_post_type_info();
	$widdget_area_name = $post_type_info['slug'] . '-side-widget-area';
	if ( is_active_sidebar( $widdget_area_name ) ) {
		dynamic_sidebar( $widdget_area_name );
	} else {
		lightning_get_template_part( 'template-parts/sidebar-contents', $post_type_info['slug'] );
	}
}

if ( is_active_sidebar( 'common-side-bottom-widget-area' ) ) {
	dynamic_sidebar( 'common-side-bottom-widget-area' );
}
?>
 <?php do_action( 'lightning_sub_section_append' ); ?>
</div><!-- [ /.sub-section ] -->
