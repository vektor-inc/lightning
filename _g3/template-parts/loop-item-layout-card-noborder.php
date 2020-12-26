<?php
$options = array(
	'layout'                     => 'card-noborder', // card , card-horizontal , media
	'display_image'              => true,
	'display_image_overlay_term' => true,
	'display_excerpt'            => true,
	'display_date'               => true,
	'display_new'                => true,
	'display_btn'                => true,
	'image_default_url'          => false,
	'overlay'                    => false,
	'btn_text'                   => __( 'Read more', 'lightning' ),
	'btn_align'                  => 'text-right',
	'new_text'                   => __( 'New!!', 'lightning' ),
	'new_date'                   => 7,
	'class_outer'                => 'vk_post-col-xs-12 vk_post-col-sm-6 vk_post-col-md-4 vk_post-col-lg-4 vk_post-col-xl-4 vk_post-col-xxl-4',
	'class_title'                => '',
	'body_prepend'               => '',
	'body_append'                => '',
);
wp_kses_post( VK_Component_Posts::the_view( $post, $options ) );