<?php get_header(); ?>
<div class="section siteContent">
    <div class="container">
        <div class="row">

            <?php
            $bvII_theme_options = get_option('bvII_theme_options');
            if ( isset($bvII_theme_options['top_sidebar_hidden']) && !$bvII_theme_options['top_sidebar_hidden'] ){
                $main_col = 'col-md-8';
            } else {
                $main_col = 'col-md-12';
            } ?>

            <div class="<?php echo $main_col; ?>">

            <?php if (have_posts()) : ?>

                <?php if ( 'page' == get_option('show_on_front') ) : ?>

                    <?php while ( have_posts() ) : the_post();?>

                        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                        <div class="entry-body">
                            <?php the_content(); ?>
                        </div>
                        <?php wp_link_pages( array( 'before' => '<div class="page-link">' . 'Pages:', 'after' => '</div>' ) ); ?>
                         </article><!-- [ /#post-<?php the_ID(); ?> ] -->

                    <?php endwhile;?>

                <?php else : ?>

                    <div class="postList">

                        <?php while ( have_posts() ) : the_post();?>

                            <?php get_template_part('module_post__loop_item'); ?>

                        <?php endwhile;?>

                        <?php the_posts_pagination(array (
                                                'mid_size'  => 1,
                                                'prev_text' => __ ( '&laquo;', 'bvII' ),
                                                'next_text' => __ ( '&raquo;', 'bvII' ),
                                                'type'      => 'list',
                                                'before_page_number' => '<span class="meta-nav screen-reader-text">' . __ ( 'Page', 'bvII' ) . ' </span>'
                                            ) ); ?>

                    </div><!-- [ /.postList ] -->

                <?php endif; // if ( 'page' == get_option('show_on_front') ) : ?>

            <?php else: ?>

                <div class="well"><p><?php _e('No posts.','bvII');?></p></div>

            <?php endif; // have_post() ?>

            </div><!-- [ /.col-md-8 of .col-md-12 ] -->

            <?php if ( !isset($bvII_theme_options['top_sidebar_hidden']) || !$bvII_theme_options['top_sidebar_hidden'] ) :?>
                <div class="col-md-3 col-md-offset-1 site-sub subSection">
                    <?php get_sidebar(); ?>
                </div><!-- [ /.site-sub ] -->
            <?php endif; ?>

        </div><!-- [ /.row ] -->
    </div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
 <?php get_footer(); ?>