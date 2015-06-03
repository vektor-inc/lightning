<?php get_header(); ?>
<div class="section siteContent">
<div class="container">
<div class="row">

<div class="col-md-8">
<main class="site-main" id="main" role="main">

 <?php
/*-------------------------------------------*/
/*  Archive title
/*-------------------------------------------*/
$page_for_posts = bvII_get_page_for_posts();
// Use post top page（ Archive title wrap to div ）
if ( $page_for_posts['post_top_use'] || get_post_type() != 'post' ) {
  if ( is_year() || is_month() || is_day() || is_tag() || is_author() || is_tax() || is_category() ) {
      $archiveTitle = bvII_get_the_archive_title();
      echo '<h1 class="archive-title">'.esc_html( $archiveTitle ).'</h1>';
  }
}
?>

<?php
/*-------------------------------------------*/
/*  Archive description
/*-------------------------------------------*/
  if ( is_category() || is_tax() || is_tag() ) {
    $category_description = term_description();
    $page = get_query_var( 'paged', 0 );
    if ( ! empty( $category_description ) && $page == 0 ) {
      echo '<div class="archive-meta">' . $category_description . '</div>';
    }
  }
  ?>

<?php if (have_posts()) : ?>
<div class="postList">
<?php while ( have_posts() ) : the_post();?>
<?php get_template_part('module_post__loop_item'); ?>
<?php endwhile;?>
</div>
<?php bvII_pagination(); ?>
<?php else: ?>
<div class="well"><p><?php _e('No posts.','bvII');?></p></div>
<?php endif; // have_post() ?>
</main>
</div>

<div class="col-md-3 col-md-offset-1 subSection">
<?php get_sidebar(get_post_type()); ?>
</div><!-- [ /.subSection ] -->

</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
 <?php get_footer(); ?>