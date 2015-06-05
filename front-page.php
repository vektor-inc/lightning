<?php get_header(); ?>
<div class="section sectionBox siteContent" id="top__blog">
    <div class="container">
    <!--
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center">Blog</h1>
            </div>
        </div>
    -->
        <div class="row sectionBox">
            <div class="col-md-12">
            <?php
$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
$post_loop = new WP_Query( array(
'post_type' => 'post',
'posts_per_page' => 10,
) ); ?>
<?php if ($post_loop->have_posts()) : while ( $post_loop->have_posts() ) : $post_loop->the_post();?>
<?php get_template_part('module_post__loop_item'); ?>
<?php endwhile;endif; ?>
            </div>
        </div>
        <div class="row ">
            <div class="col-md-12">
                <a href="/blog" class="pull-right btn btn-default">More</a>
            </div>
        </div>
    </div>
</div>
 <?php get_footer(); ?>