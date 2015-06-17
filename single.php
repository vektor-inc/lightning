<?php get_header(); ?>
<div class="section siteContent">
<div class="container">
<div class="row">

<div class="col-md-8">
<main class="site-main" id="main" role="main">
<?php if (have_posts()) : while ( have_posts() ) : the_post();?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header>
	<?php get_template_part('module_loop_post_meta');?>
	<h1 class="entry-title"><?php the_title(); ?></h1>
	</header>

	<div class="entry-body">
	<?php the_content();?>
	</div><!-- [ /.entry-body ] -->

	<?php
	$args = array(
		'before'           => '<nav class="page-link"><dl><dt>Pages :</dt><dd>',
		'after'            => '</dd></dl></nav>',
		'link_before'      => '<span class="page-numbers">',
		'link_after'       => '</span>',
		'echo'             => 1 );
	wp_link_pages( $args ); ?>

	<?php $tags_list = get_the_tag_list();
	if ( $tags_list ): ?>
	<div class="entry-tag">
	<dl>
	<dt><?php _e('Tags','lightning') ;?></dt>
	<dd><?php echo $tags_list; ?></dd>
	</dl>
	</div><!-- [ /.entry-tag ] -->
	<?php endif; ?>
	<?php comments_template( '', true ); ?>
</article>
<?php endwhile;endif; ?>

<nav>
  <ul class="pager">
    <li class="previous"><?php previous_post_link( '%link', '%title' ); ?></li>
    <li class="next"><?php next_post_link( '%link', '%title' ); ?></li>
  </ul>
</nav>
</main>
</div><!-- [ /.col-md-8 ] -->

<div class="col-md-3 col-md-offset-1 site-sub subSection">
<?php get_sidebar(get_post_type()); ?>
</div><!-- [ /.site-sub ] -->

</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>