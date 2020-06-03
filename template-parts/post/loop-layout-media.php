<?php
/**
 * Media Loop Template Part of Lightning
 *
 * @package Lightning
 */

$image_option = get_option( 'lightning_theme_options' );

$image_default_url = ! empty( $image_option['default_list_image'] ) ? $image_option['default_list_image'] : get_template_directory_uri() . '/assets/images/no-image.png';
?>
<article class="media">
	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

		<div class="media-left postList_thumbnail">
			<a href="<?php the_permalink(); ?>">
				<?php
				if ( has_post_thumbnail() ) {
					$attr = array( 'class' => 'media-object' );
					the_post_thumbnail( 'thumbnail', $attr );
				} else {
					echo '<img class="media-object wp-post-image" src="' . esc_url( $image_default_url ) . '">';
				}
				?>
			</a>
		</div>

		<div class="media-body">
			<?php get_template_part( 'template-parts/post/meta' ); ?>
			<h1 class="media-heading entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h1>
			<a href="<?php the_permalink(); ?>" class="media-body_excerpt"><?php the_excerpt(); ?></a>
		</div>
	</div>
</article>
