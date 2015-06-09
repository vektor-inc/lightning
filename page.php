<?php get_header(); ?>
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
    </div><!-- [ /#post-<?php the_ID(); ?> ] -->
    <?php wp_link_pages( array( 'before' => '<div class="page-link">' . 'Pages:', 'after' => '</div>' ) ); ?>
</main>
<?php endwhile; ?>
</div>
</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>