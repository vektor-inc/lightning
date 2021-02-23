<?php

/*
  Load tga(Plugin install)
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/tgm-plugin-activation/tgm-config.php' );

/*
  Load modules
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/package-manager.php' );

require get_parent_theme_file_path( '/inc/vk-old-options-notice/vk-old-options-notice-config.php' );

/*
  Year Artchive list 'year' and count insert to inner </a>
/*-------------------------------------------*/
function lightning_archives_link( $html ) {
	return preg_replace( '@</a>(.+?)</li>@', '\1</a></li>', $html );
}
add_filter( 'get_archives_link', 'lightning_archives_link' );

/*
  Category list count insert to inner </a>
/*-------------------------------------------*/
function lightning_list_categories( $output, $args ) {
	$output = preg_replace( '/<\/a>\s*\((\d+)\)/', ' ($1)</a>', $output );
	return $output;
}
add_filter( 'wp_list_categories', 'lightning_list_categories', 10, 2 );


/*
  Tag Cloud _ Change font size
/*-------------------------------------------*/
function lightning_tag_cloud_filter( $args ) {
	$args['smallest'] = 10;
	$args['largest']  = 10;
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'lightning_tag_cloud_filter' );