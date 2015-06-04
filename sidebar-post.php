<?php
if ( is_active_sidebar( 'common-side-top-widget-area' ) )
  dynamic_sidebar( 'common-side-top-widget-area' );

// Display post type widget area
$widdget_area_name = 'post-side-widget-area';
if ( is_active_sidebar( $widdget_area_name ) ){
  dynamic_sidebar( $widdget_area_name );
} else { ?>

<?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$post_loop = new WP_Query( array(
    'post_type' => 'post',
    'posts_per_page' => 5,
) ); ?>
<?php if ($post_loop->have_posts()) : ?>
<div class="widget">
<h4 class="subSection-title"><?php echo __('Recent posts', 'bvII');?></h4>
<?php while ( $post_loop->have_posts() ) : $post_loop->the_post();?>
<?php get_template_part('module_post__subLoop_item'); ?>
<?php endwhile;?>
</div>
<?php endif; ?>

<div class="widget">
<nav class="localNav">
<h4 class="subSection-title">カテゴリー</h4>
<ul class="nav nav-pills nav-stacked">
  <?php wp_list_categories('title_li='); ?> 
</ul>
</nav>
</div>

<div class="widget">
<nav class="localNav">
<h4 class="subSection-title">アーカイブ</h4>
<ul class="nav nav-pills nav-stacked">
  <?php
  $args = array(
    'type' => 'monthly',
    'post_type' => 'post',
    );
  wp_get_archives($args); ?>
</ul>
</nav>
</div>

<?php 
}

if ( is_active_sidebar( 'common-side-bottom-widget-area' ) )
  dynamic_sidebar( 'common-side-bottom-widget-area' );