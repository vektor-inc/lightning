<?php

class LTG_Template_Redirect {

    public function __construct(){
        $ridirect_array = array(
            'index',
            '404', 
            'archive', 
            'author', 
            'category', 
            'tag', 
            'taxonomy', 
            'date', 
            'home', 
            'frontpage', 
            'privacypolicy', 
            'page',
            'search',
            'single',
            'embed',
            'singular',
            'attachment'
        );
        foreach ( $ridirect_array as $key => $type ){
            add_filter( "{$type}_template_hierarchy", array( __CLASS__, 'redirect_test' ) );
        }
        add_filter( 'template_directory_uri', array( __CLASS__, 'template_directory_uri' ) );
        // parent_theme_file_path の書き換えは危険なので保留
        // add_filter( 'parent_theme_file_path', array( __CLASS__, 'parent_theme_file_path' ) );
    }

    public static function redirect_test( $templates ){
        foreach ( $templates as $key => $template){
            $templates[$key] = LIG_G3_DIR . '/' . $template;
        }
        return $templates;
    }

    public static function template_directory_uri( $template_dir_uri  ){
        return $template_dir_uri . '/' . LIG_G3_DIR;
    }
    // public static function parent_theme_file_path( $parent_theme_file_path  ){
    //     return $parent_theme_file_path . '/' . LIG_G3_DIR;
    // }
}

new LTG_Template_Redirect();