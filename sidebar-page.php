<?php
/**
 * Sidebar for Page Template of Lightning
 *
 * @package Lightning
 */

if ( is_active_sidebar( 'common-side-top-widget-area' ) ) {
	dynamic_sidebar( 'common-side-top-widget-area' );
}

// Display post type widget area.
$widdget_area_name = 'page-side-widget-area';
if ( is_active_sidebar( $widdget_area_name ) ) {
	dynamic_sidebar( $widdget_area_name );
} else {
	if ( $post->ancestors ) {
		foreach ( $post->ancestors as $post_anc_id ) {
			$page_id = $post_anc_id;
		}
	} else {
		$page_id = $post->ID;
	}

	if ( $page_id ) {
		$children = wp_list_pages( 'title_li=&child_of=' . $page_id . '&echo=0' );
		if ( $children ) { ?>
			<aside class="widget widget_child_page widget_link_list">
				<nav class="localNav">
					<h1 class="subSection-title"><?php echo esc_html( get_the_title( $page_id ) ); ?></h1>
					<ul>
					<?php echo wp_kses_post( $children ); ?>
					</ul>
				</nav>
			</aside>
			<?php
		}
	}
}

if ( is_active_sidebar( 'common-side-bottom-widget-area' ) ) {
	dynamic_sidebar( 'common-side-bottom-widget-area' );
}
