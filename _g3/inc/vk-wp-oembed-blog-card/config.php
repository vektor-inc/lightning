<?php
/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'VK_WP_Oembed_Blog_Card' ) ) {
    global $vk_embed_dir_uri;
    $vk_embed_dir_uri = get_template_directory_uri() . '/inc/vk-wp-oembed-blog-card/package/';
  require dirname( __FILE__ ) . '/package/class-vk-wp-oembed-blog-card.php';
}
