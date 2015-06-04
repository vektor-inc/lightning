<?php
// $page_for_posts = bvII_get_page_for_posts();
// if ( $page_for_posts['post_top_use'] ) {
// 	$taxonomies = get_the_taxonomies();
// 	if ($taxonomies):
// 		// get $taxonomy name
// 		$taxonomy = key( $taxonomies );
// 		$terms  = get_the_terms( get_the_ID(),$taxonomy );
// 		// $term_url	= esc_url(get_term_link( $terms[0]->term_id,$taxonomy));
// 		$term_name	= esc_html($terms[0]->name);
// 	endif;
// } // if ( $page_for_posts['post_top_use'] ) {
?>

<div class="media">
	<div class="media-left postList__thumbnail">
		<a href="<?php the_permalink(); ?>">
		<!--
		<button type="button" class="btn btn-primary btn-xs postList__cateLabel"><?php echo $term_name; ?></button>
		-->
		<?php
		$attr = array('class'	=> "media-object");
		the_post_thumbnail('thumbnail',$attr); ?>
		</a>
	</div>
	<div class="media-body">
		<?php get_template_part('module_post__loop_item_postMeta');?>
		<h4 class="media-heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		<?php the_excerpt(); ?>
		<!--
		<div><a href="<?php the_permalink(); ?>" class="btn btn-default btn-sm"><?php _e('Read more', 'bvII'); ?></a></div>
		-->   
	</div>
</div>