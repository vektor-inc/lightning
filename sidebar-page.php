<?php
if ( is_active_sidebar( 'common-side-top-widget-area' ) ) {
	dynamic_sidebar( 'common-side-top-widget-area' );
}

// Display post type widget area
$widdget_area_name = 'page-side-widget-area';
if ( is_active_sidebar( $widdget_area_name ) ) {
	dynamic_sidebar( $widdget_area_name );
} else {

	if ( $post->ancestors ) {
		foreach ( $post->ancestors as $post_anc_id ) {
			$post_id = $post_anc_id;
		} // foreach($post->ancestors as $post_anc_id){
	} else {
		$post_id = $post->ID;
	} // if($post->ancestors){

	if ( $post_id ) {
		$children = wp_list_pages( 'title_li=&child_of=' . $post_id . '&echo=0' );
		if ( $children ) { ?>
			<aside class="widget widget_child_page widget_link_list">
			<nav class="localNav">
			<h1 class="subSection-title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo get_the_title( $post_id ); ?></a></h1>
			<ul>
			<?php echo $children; ?>
			</ul>
			</nav>
			</aside>
		<?php } // if ($children)
	} // if ($post_id)

} // if ( is_active_sidebar( $widdget_area_name ) ){

if ( is_active_sidebar( 'common-side-bottom-widget-area' ) ) {
	dynamic_sidebar( 'common-side-bottom-widget-area' );
}
?>
