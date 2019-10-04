<?php /* customize sample */ ?>
<div class="card">
	<a href="<?php the_permalink(); ?>">

		<!-- <div class="card-img-overlay"></div> -->

	<?php
		$attr = array( 'class' => 'card-img-top' );
		the_post_thumbnail( 'medium', $attr );
	?>

	<div class="card-body">
	<h6 class="card-title"><?php the_title(); ?></h6>

		<!-- <p class="card-text">
			<?php the_excerpt(); ?>
		</p> -->

	<p class="card-text">
		<span class="published entry-meta_items"><?php echo esc_html( get_the_date() ); ?></span>
	</p>

	 <!-- <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php $options['display']['link_text']; ?></a> -->

	</div>
	</a>
</div>
