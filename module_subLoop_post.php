<div class="media">

	<?php if ( has_post_thumbnail()) :?>

		<div class="media-left postList_thumbnail">
			<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail('thumbnail'); ?>
			</a>
		</div>

	<?php endif; ?>

	<div class="media-body">
	<h4 class="media-heading"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
	<div><i class="fa fa-calendar"></i>&nbsp;<?php echo get_the_date(); ?></div>          
	</div>
</div>