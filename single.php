<?php get_header(); ?>
<div class="section siteContent">
<div class="container">
<div class="row">

<div class="col-md-8">
<main class="site-main" id="main" role="main">
<?php if (have_posts()) : while ( have_posts() ) : the_post();?>
<article>
	<header>
	<?php get_template_part('module_post__loop_item_postMeta');?>
	<h1 class="entry-title"><?php the_title(); ?></h1>
	</header>
	<div class="row">
	<div class="col-md-12">
	<div class="entry-body">
	<?php the_content();?>
	</div><!-- [ /.entry-body ] -->

	<?php $tags_list = get_the_tag_list();
	if ( $tags_list ): ?>
	<div class="entry-tag">
	<dl>
	<dt><?php _e('Tags','bvII') ;?></dt>
	<dd><?php echo $tags_list; ?></dd>
	</dl>
	</div><!-- [ /.entry-tag ] -->
	<?php endif; ?>
	<?php comments_template( '', true ); ?>
	<?php wp_link_pages( array( 'before' => '<div class="page-link">' . 'Pages:', 'after' => '</div>' ) ); ?>

	</div><!-- [ /.col-md-12 ] -->
	</div><!-- [ /.row ] -->
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