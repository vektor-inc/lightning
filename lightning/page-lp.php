<?php
/*
 * Template Name: Landing Page
 */
get_header(); ?>

<div class="section siteContent">
<div class="container">
<div class="row">

<div class="col-md-12 mainSection" id="main" role="main">

    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

    <?php
    global $post;
    $widget_id = 'lp-widget-'.$post->ID;
    if ( is_active_sidebar( $widget_id ) ) :
    	dynamic_sidebar( $widget_id );
    endif;

    $content = get_the_content();
    if ( $content ) : ?>

    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-body">
    <?php the_content(); ?>
    </div>
	<?php
	$args = array(
		'before'           => '<nav class="page-link"><dl><dt>Pages :</dt><dd>',
		'after'            => '</dd></dl></nav>',
		'link_before'      => '<span class="page-numbers">',
		'link_after'       => '</span>',
		'echo'             => 1
		);
	wp_link_pages( $args ); ?>
    </div><!-- [ /#post-<?php the_ID(); ?> ] -->

	<?php endif; ?>

	<?php endwhile; ?>

</div><!-- [ /.mainSection ] -->

</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>