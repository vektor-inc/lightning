<?php

if ( ! class_exists( 'VK_Breadcrumb' ) ) {

    global $breadcrumb_options;
    $breadcrumb_options = array(
        'id_outer' => 'breadcrumb',
        'class_outer' => 'breadcrumb',
        'class_inner' => 'container',
        'class_list' => 'breadcrumb__list',
        'class_list_item' => 'breadcrumb__list__item',
    );

    require get_parent_theme_file_path( LIG_G3_DIR . '/inc/vk-breadcrumb/package/class-vk-breadcrumb.php' );

}

