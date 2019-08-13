<?php get_header(); ?>

<?php get_template_part( 'module_pageTit' ); ?>
<?php get_template_part( 'module_panList' ); ?>

<div class="section siteContent">
<div class="container">
<div class="row">

<div class="<?php lightning_the_class_name( 'mainSection' ); ?>" id="main" role="main">

<?php
if ( apply_filters( 'is_lightning_extend_single', false ) ) :
	do_action( 'lightning_extend_single' );
else :
	if ( have_posts() ) :
		while ( have_posts() ) :
			the_post();
		?>
			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
		<header>
		<?php get_template_part( 'module_loop_post_meta' ); ?>
		<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>

		<div class="entry-body">
		<?php the_content(); ?>
		</div><!-- [ /.entry-body ] -->

		<div class="entry-footer">
		<?php
		$args = array(
			'before'      => '<nav class="page-link"><dl><dt>Pages :</dt><dd>',
			'after'       => '</dd></dl></nav>',
			'link_before' => '<span class="page-numbers">',
			'link_after'  => '</span>',
			'echo'        => 1,
		);
			wp_link_pages( $args );
			?>

				<?php
				/*-------------------------------------------*/
				/*  Category and tax data
				/*-------------------------------------------*/
				$args          = array(
					'template'      => __( '<dl><dt>%s</dt><dd>%l</dd></dl>', 'lightning' ),
					'term_template' => '<a href="%1$s">%2$s</a>',
				);
				$taxonomies    = get_the_taxonomies( $post->ID, $args );
				$taxnomiesHtml = '';
				if ( $taxonomies ) {
					foreach ( $taxonomies as $key => $value ) {
						if ( $key != 'post_tag' ) {
							$taxnomiesHtml .= '<div class="entry-meta-dataList">' . $value . '</div>';
						}
					} // foreach
				} // if ($taxonomies)
				$taxnomiesHtml = apply_filters( 'lightning_taxnomiesHtml', $taxnomiesHtml );
				echo $taxnomiesHtml;
			?>

			<?php
			$tags_list = get_the_tag_list();
			if ( $tags_list ) :
			?>
			<div class="entry-meta-dataList entry-tag">
			<dl>
			<dt><?php _e( 'Tags', 'lightning' ); ?></dt>
	<dd class="tagcloud"><?php echo $tags_list; ?></dd>
	</dl>
	</div><!-- [ /.entry-tag ] -->
	<?php endif; ?>
		</div><!-- [ /.entry-footer ] -->

		<?php comments_template( '', true ); ?>
	</article>


	<?php if ( $bootstrap == '3' ) { ?>
		<nav>
		  <ul class="pager">
			<li class="previous"><?php previous_post_link( '%link', '%title' ); ?></li>
			<li class="next"><?php next_post_link( '%link', '%title' ); ?></li>
		  </ul>
		</nav>
	<?php
} else {
?>
	<div class="row postNextPrev">
	  <div class="col-sm-6">

			<?php
			$post = get_previous_post();
			if ( $post ) {
			?>
				<h5 class="postNextPrev_title"><?php _e( 'Previous article', 'lightning' ); ?></h5>
				<?php
				$options = array(
					'layout'  => 'media',
					'display' => array(
						'image'       => true,
						'excerpt'     => false,
						'date'        => true,
						'link_button' => false,
						// 'link_text'   => __( 'Read more', 'lightning' ),
						'overlay'     => '',
					),
					'class'   => array(
						'outer' => 'card-sm',
					),
				);
				VK_Component_Posts::the_view( $post, $options );
				// get_template_part( 'module_loop_post_card' );
			}
			wp_reset_postdata();
			?>
	  </div>
	  <div class="col-sm-6">
			<?php
			$post = get_next_post();
			if ( $post ) {
			?>
				<h5 class="postNextPrev_title postNextPrev_title-next"><?php _e( 'Next article', 'lightning' ); ?></h5>
				<?php
				$options = VK_Components_Posts::the_view( $post, $options );
			}
			wp_reset_postdata();
			?>
	  </div>
	</div>
	<?php } ?>


	<?php
	endwhile;
endif;
endif;
?>

</div><!-- [ /.mainSection ] -->

<div class="<?php lightning_the_class_name( 'sideSection' ); ?>">
<?php get_sidebar( get_post_type() ); ?>
</div><!-- [ /.subSection ] -->

</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>
