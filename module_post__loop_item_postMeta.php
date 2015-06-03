
<div class="entry-meta">
<span class="published entry-meta-items"><i class="fa fa-calendar"></i><?php echo esc_html( get_the_date() ); ?></span>
<!--
<span class="updated entry-meta-items"><?php _e('Last updated','bvII'); ?> : <?php the_modified_date('') ?></span>
<span class="vcard author entry-meta-items"><i class="fa fa-pencil"></i><span class="fn"><?php echo esc_html(get_the_author_meta( 'display_name' ));?></span></span>
-->

<!--
<span class="vcard author entry-meta-items"><span class="fn"><i class="fa fa-pencil"></i><?php echo esc_html(get_the_author_meta( 'display_name' ));?></span></span>
-->

<?php

$page_for_posts = bvII_get_page_for_posts();
if ( $page_for_posts['post_top_use'] ) {
	$taxonomies = get_the_taxonomies();
	if ($taxonomies):
		// get $taxonomy name
		$taxonomy = key( $taxonomies );
		$terms  = get_the_terms( get_the_ID(),$taxonomy );
		$term_url	= esc_url(get_term_link( $terms[0]->term_id,$taxonomy));
		$term_name	= esc_html($terms[0]->name);
		echo '<span class="entry-meta-items entry-meta-items-term"><a href="'.$term_url.'" class="btn btn-xs btn-primary">'.$term_name.'</a></span>';
	endif;
} // if ( $page_for_posts['post_top_use'] ) {

?>
</div>