<div class="media">
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( has_post_thumbnail()) :?>
	<div class="media-left postList__thumbnail">
		<a href="<?php the_permalink(); ?>">
		<?php
		$attr = array('class'	=> "media-object");
		the_post_thumbnail('thumbnail',$attr); ?>
		</a>
	</div>
	<?php endif; ?>
	<div class="media-body">
		<?php get_template_part('module_post__loop_item_postMeta');?>
		<h4 class="media-heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
		<?php the_excerpt(); ?>
		<!--
		<div><a href="<?php the_permalink(); ?>" class="btn btn-default btn-sm"><?php _e('Read more', 'bvII'); ?></a></div>
		-->   
	</div>
</div>
</div>