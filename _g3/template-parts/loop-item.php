<?php
/**
 * For Archive page loop post item template
 *
 * @package lightning
 */

use VektorInc\VK_Component\VK_Component_Posts;


$options = array(
	// card, card-noborder, card-intext, card-horizontal , media, postListText.
	'layout'                     => 'media',
	'display_image'              => true,
	'display_image_overlay_term' => true,
	'display_excerpt'            => true,
	'display_date'               => true,
	'display_new'                => true,
	'display_taxonomies'         => false,
	'display_btn'                => true,
	'image_default_url'          => false,
	'overlay'                    => false,
	'btn_text'                   => __( 'Read more', 'lightning' ),
	'btn_align'                  => 'text-right',
	'new_text'                   => __( 'New!!', 'lightning' ),
	'new_date'                   => 7,
	'class_outer'                => 'vk_post-col-xs-12 vk_post-col-sm-12 vk_post-col-lg-12',
	'class_title'                => '',
	'body_prepend'               => '',
	'body_append'                => '',
);
VK_Component_Posts::the_view( $post, $options );
