<?php

class LTG_Template_Redirect {

    public function __construct(){
        add_filter( 'frontpage_template', array( __CLASS__, 'redirect_index' ) );
        // add_action( 'get_template_part', array( __CLASS__, 'redirect_get_template_part' ), 10, 4 );
    }

    public static function g3_convert( $template_names ){
        $template = '';
        foreach ( (array) $template_names as $template_name ) {
            if ( ! $template_name ) {
                continue;
            }
            if ( file_exists( STYLESHEETPATH . '/' . LIG_G3_DIR . '/' . $template_name ) ) {
                $template = STYLESHEETPATH . '/' . LIG_G3_DIR . '/' . $template_name;
                break;
            } elseif ( file_exists( TEMPLATEPATH . '/' . LIG_G3_DIR . '/' . $template_name ) ) {
                $template = TEMPLATEPATH . '/' . LIG_G3_DIR . '/' . $template_name;
                break;
            }
        }
        return $template;
    }

    public static function redirect_index( $template ){
    $template_names[] = 'front-page.php';
    $template_names[] = 'index.php';
 
    $template = self::g3_convert( $template_names );

    return $template;

    }

}

new LTG_Template_Redirect();