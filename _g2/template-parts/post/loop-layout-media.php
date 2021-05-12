<article class="media">
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
	<div class="media-left postList_thumbnail">
		<a href="<?php the_permalink(); ?>">
		<?php
		$attr = array( 'class' => 'media-object' );
		the_post_thumbnail( 'thumbnail', $attr );
		?>
		</a>
	</div>
	<?php endif; ?>
	<div class="media-body">
		<?php get_template_part( 'template-parts/post/meta' ); ?>
		<h1 class="media-heading entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
		<a href="<?php the_permalink(); ?>" class="media-body_excerpt"><?php the_excerpt(); ?></a>
	</div>
</div>
</article>
