<?php get_header(); ?>
<div class="section sectionBox siteContent" id="top__blog">
    <div class="container">

        <div class="row">
            <div class="col-md-12 postList">

<?php if (have_posts()) : ?>

    <div class="postList">

        <?php while ( have_posts() ) : the_post();?>

            <?php get_template_part('module_post__loop_item'); ?>

        <?php endwhile;?>

    </div>

    <?php the_posts_pagination(array (
                            'mid_size'  => 1,
                            'prev_text' => __ ( '&laquo;', 'bvII' ),
                            'next_text' => __ ( '&raquo;', 'bvII' ),
                            'type'      => 'list',
                            'before_page_number' => '<span class="meta-nav screen-reader-text">' . __ ( 'Page', 'bvII' ) . ' </span>'
                        ) ); ?>

<?php else: ?>

    <div class="well"><p><?php _e('No posts.','bvII');?></p></div>

<?php endif; // have_post() ?>

            </div>
        </div>
    </div>
</div>
 <?php get_footer(); ?>