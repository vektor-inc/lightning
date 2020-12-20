<?php

define( 'LIG_G3_DIR_PATH', get_parent_theme_file_path( '_g3/' ) );
define( 'LIG_G3_DIR', '_g3' );

require get_parent_theme_file_path( LIG_G3_DIR . '/inc/class-vk-helpers.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/class-ltg-template-redirect.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/template-tags.php' );

require get_parent_theme_file_path( LIG_G3_DIR . '/inc/layout-controller/layout-controller.php' );
require get_parent_theme_file_path( LIG_G3_DIR . '/inc/vk-breadcrumb/config.php' );