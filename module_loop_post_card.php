<?php
print '<pre style="text-align:left">';
print_r( $options );
print '</pre>';

	?>
<div class="card">
	<a href="<?php the_permalink(); ?>">

		<?php if ( $options['display']['overlay'] ) { ?>

		<div class="card-img-overlay">
		<?php echo esc_kses_post( $options['display']['overlay'] ); ?>
		</div>

	<?php } ?>

	<?php
	if ( $options['display']['image'] ) {
		$attr = array( 'class' => 'card-img-top' );
		the_post_thumbnail( 'medium', $attr );
	}
	?>

	<div class="card-body">
	<h6 class="card-title"><?php the_title(); ?></h6>

	<?php if ( $options['display']['excerpt'] ) { ?>
		<p class="card-text">
			<?php the_excerpt(); ?>
		</p>
	<?php } ?>

	<?php if ( $options['display']['date'] ) { ?>
	<p class="card-text">
		<span class="published entry-meta_items"><?php echo esc_html( get_the_date() ); ?></span>
	</p>
	<?php } ?>

	<?php if ( $options['display']['link_button'] ) { ?>
		 <a href="<?php the_permalink(); ?>" class="btn btn-primary"><?php $options['display']['link_text']; ?></a>
	<?php } ?>

	</div>
	</a>
</div>
