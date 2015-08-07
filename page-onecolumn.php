<?php
/*
 * Template Name: No sidebar
 */
get_header(); ?>

<?php get_template_part('module_pageTit'); ?>
<?php get_template_part('module_panList'); ?>

<div class="section siteContent">
<div class="container">
<div class="row">

<div class="col-md-12">
<main class="site-main" id="main" role="main">
    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
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
		'echo'             => 1 );
	wp_link_pages( $args ); ?>
    </div><!-- [ /#post-<?php the_ID(); ?> ] -->

</main>
<?php endwhile; ?>
</div>
</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>