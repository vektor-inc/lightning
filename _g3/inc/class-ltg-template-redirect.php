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
    }

    public static function redirect_test( $templates ){
        foreach ( $templates as $key => $template){
            $templates[$key] = LIG_G3_DIR . '/' . $template;
        }
        return $templates;
    }
}

new LTG_Template_Redirect();