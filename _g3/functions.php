<?php

define( 'LIG_G3_DIR_PATH', get_parent_theme_file_path( '_g3/' ) );
define( 'LIG_G3_DIR', '_g3' );

class LTG_Template_Redirect {

    public function __construct(){
        add_filter( 'frontpage_template', array( __CLASS__, 'redirect_index' ) );
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



// add_filter( 'frontpage_template', function( $templates ){
//     print '<pre style="text-align:left">';print_r( $templates );print '</pre>';

// } );

// add_action( 'template_redirect', 'bvsl_doc_change_salary_archive' );
// function bvsl_doc_change_salary_archive() {
// 	global $wp_query;
// 	if ( function_exists( 'bill_get_post_type' ) ) {
// 		$post_type = bill_get_post_type();
// 		$post_type = $post_type['slug'];
// 	} else {
// 		$post_type = get_post_type();
// 	}
// 	if ( $post_type == 'salary' && is_tax() ) {
// 		require_once( 'template-parts/doc/frame-salary-archive.php' );
// 		die();
// 	}
// }

// add_action( 'get_header', 'lightning_g3_redirect_header' );
// function lightning_g3_redirect_header( $name = null, $args = array() ){

//     $templates = array();
//     $name      = (string) $name;

//     if ( '' !== $name ) {
//         $templates[] = LIG_G3_DIR_PATH . "header-{$name}.php";
//     }
 
//     $templates[] = LIG_G3_DIR_PATH . 'header.php';
 
//     if ( ! locate_template( $templates, true, true, $args ) ) {
//         return false;
//     }
//     return;
// }