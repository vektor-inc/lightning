<?php
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
            <aside class="widget widget_link_list">
			<h4 class="sub-section-title"><a href="<?php echo esc_url( get_permalink( $post_id ) ); ?>"><?php echo get_the_title( $post_id ); ?></a></h4>
			<ul>
			<?php echo $children; ?>
			</ul>
			</aside>
		<?php } // if ($children)
	} // if ($post_id)