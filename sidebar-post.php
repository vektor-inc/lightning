<?php
if ( is_active_sidebar( 'common-side-top-widget-area' ) )
  dynamic_sidebar( 'common-side-top-widget-area' );

// Display post type widget area
$widdget_area_name = 'post-side-widget-area';
if ( is_active_sidebar( $widdget_area_name ) ){
  dynamic_sidebar( $widdget_area_name );
} else { ?>

<?php
$post_loop = new WP_Query( array(
    'post_type' => 'post',
    'posts_per_page' => 10,
    'no_found_rows'  => true,
    'update_post_meta_cache' => false,
    'update_post_term_cache' => false,
) ); ?>

<?php if ($post_loop->have_posts()) : ?>
<aside class="widget">
<h1 class="subSection-title"><?php echo __('Recent posts', 'lightning');?></h1>
<?php while ( $post_loop->have_posts() ) : $post_loop->the_post();?>
<?php get_template_part('module_subLoop_post'); ?>
<?php endwhile;?>
</aside>
<?php endif; ?>

<aside class="widget widget_categories">
<nav class="localNav">
<h1 class="subSection-title"><?php _e('Category', 'lightning');?></h1>
<ul>
  <?php wp_list_categories('title_li='); ?> 
</ul>
</nav>
</aside>

<aside class="widget widget_archive">
<nav class="localNav">
<h1 class="subSection-title"><?php _e('Archive', 'lightning');?></h1>
<ul>
  <?php
  $args = array(
    'type' => 'monthly',
    'post_type' => 'post',
    );
  wp_get_archives($args); ?>
</ul>
</nav>
</aside>

<?php 
}

if ( is_active_sidebar( 'common-side-bottom-widget-area' ) )
  dynamic_sidebar( 'common-side-bottom-widget-area' );