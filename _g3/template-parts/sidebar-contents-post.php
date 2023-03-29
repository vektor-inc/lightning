<?php
/**
 * Sidebar for post
 *
 * This file is sidebar fot post.
 * But, if the widget or block is placed in the sitebar widget area (post),
 * This file will not be read.
 *
 * 投稿タイプ post 用のサイドバーです。
 * しかし、サイトバーウィジェットエリア（投稿）にウィジェットかブロックが配置されている場合、
 * このファイルは読み込まれなくなります。
 *
 * @package vektor-inc/lightning
 */

$post_loop = new WP_Query(
	array(
		'post_type'              => 'post',
		'posts_per_page'         => 10,
		'no_found_rows'          => true,
		'update_post_meta_cache' => false,
		'update_post_term_cache' => false,
	)
);
?>

<?php if ( $post_loop->have_posts() ) : ?>
<aside class="widget widget_media">
<h4 class="sub-section-title"><?php echo __( 'Recent posts', 'lightning' ); ?></h4>
<div class="vk_posts">
	<?php
	while ( $post_loop->have_posts() ) :
		$post_loop->the_post();

		$options = array(
			'layout'                     => 'media', // card , card-horizontal , media
			'display_image'              => true,
			'display_image_overlay_term' => true,
			'display_excerpt'            => false,
			'display_date'               => true,
			'display_new'                => true,
			'display_btn'                => false,
			'image_default_url'          => false,
			'overlay'                    => false,
			'btn_text'                   => __( 'Read more', 'lightning' ),
			'btn_align'                  => 'text-right',
			'new_text'                   => __( 'New!!', 'lightning' ),
			'new_date'                   => 7,
			'class_outer'                => 'vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-lg-12 vk_post-col-xl-12',
			'class_title'                => '',
			'body_prepend'               => '',
			'body_append'                => '',
		);
		VK_Component_Posts::the_view( $post, $options );

endwhile;
	?>
</div>
</aside>
<?php endif; ?>
<?php wp_reset_query(); ?>

<aside class="widget widget_link_list">
<h4 class="sub-section-title"><?php _e( 'Category', 'lightning' ); ?></h4>
<ul>
	<?php wp_list_categories( 'title_li=' ); ?>
</ul>
</aside>

<aside class="widget widget_link_list">
<h4 class="sub-section-title"><?php _e( 'Archive', 'lightning' ); ?></h4>
<ul>
	<?php
	$args = array(
		'type'      => 'monthly',
		'post_type' => 'post',
	);
	wp_get_archives( $args );
	?>
</ul>
</aside>
