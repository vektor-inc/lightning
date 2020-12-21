<?php

if ( ! class_exists( 'VK_Breadcrumb' ) ) {

    global $breadcrumb_options;
    $breadcrumb_options = array(
        'id_outer' => 'breadrumb',
        'class_outer' => 'breadrumb',
        'class_inner' => 'container',
        'class_list' => 'breadrumb__list',
        'class_list_item' => 'breadrumb__list__item',
    );

    require get_parent_theme_file_path( LIG_G3_DIR . '/inc/vk-breadcrumb/package/class-vk-breadcrumb.php' );

}

