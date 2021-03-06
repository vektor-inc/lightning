<?php

define( 'LIG_G3_DIR', '_g3' );
define( 'LIG_G2_DIR', '_g2' );

function lightning_is_g3(){
	if ( 'g3' === get_option( 'lightning_theme_generation' ) ){
		return true;
	}
}

require dirname( __FILE__ ) . '/inc/class-ltg-template-redirect.php';

/**
 * 最終的に各Gディレクトリに移動
 */
if ( ! function_exists( 'lightning_get_template_part' ) ){
	function lightning_get_template_part( $slug, $name = null, $args = array() ) {

		if ( lightning_is_g3() ){
			$g_dir = '_g3';
		} else {
			$g_dir = '_g2';
		}

		/**
		 * 読み込み優先度
		 * 
		 * 1.child g階層 nameあり
		 * 2.child 直下 nameあり
		 * 3.parent g階層 nameあり
		 * 
		 * 4.child g階層 nameなし
		 * 5.child 直下 nameなし
		 * 6.parent g階層 nameなし
		 * 
		 */

		/* Almost the same as the core */
		$template_path_array = array();
		$name      = (string) $name;

		// Child theme G directory
		if ( preg_match( '/^' . $g_dir . '/', $slug ) ){
			// 1. g階層がもともと含まれている場合
			if ( '' !== $name ) {
				$template_path_array[] = get_stylesheet_directory() . "/{$slug}-{$name}.php";
			}
		} else {
			// g階層が含まれていない場合
			
			// 1. g階層付きのファイルパス
			if ( '' !== $name ) {
				$template_path_array[] = get_stylesheet_directory() . '/' . $g_dir . "/{$slug}-{$name}.php";
			}
			// 2. 直下のファイルパス
			if ( '' !== $name ) {
				$template_path_array[] = get_stylesheet_directory() . "/{$slug}-{$name}.php";
			}
		}

		if ( preg_match( '/^' . $g_dir . '/', $slug ) ){
			// 3. g階層がもともと含まれている場合
			if ( '' !== $name ) {
				$template_path_array[] = get_template_directory() . "/{$slug}-{$name}.php";
			}
		} else {
			// 3. g階層がもともと含まれていない場合
			if ( '' !== $name ) {
				$template_path_array[] = get_template_directory() . '/' . $g_dir . "/{$slug}-{$name}.php";
			}
		}


		// Child theme G directory
		if ( preg_match( '/^' . $g_dir . '/', $slug ) ){
			// 4. g階層がもともと含まれている場合
			$template_path_array[] = get_stylesheet_directory() . "/{$slug}.php";
		} else {
			// g階層が含まれていない場合
			// 4. g階層付きのファイルパス
			$template_path_array[] = get_stylesheet_directory() . '/' . $g_dir . "/{$slug}.php";
			// 5. 直下のファイルパス
			$template_path_array[] = get_stylesheet_directory() . "/{$slug}.php";
		}

		if ( preg_match( '/^' . $g_dir . '/', $slug ) ){
			// g階層がもともと含まれている場合
			// 6. 親のg階層
			$template_path_array[] = get_template_directory() . "/{$slug}.php";
		} else {
			// 6. 親のg階層
			$template_path_array[] = get_template_directory() . '/' . $g_dir . "/{$slug}.php";
		}

		foreach( (array) $template_path_array as $template_path ){
			if ( file_exists( $template_path ) ){
				$require_once = false;
				load_template( $template_path, $require_once );
				break;
			}
		}

	}
}

if ( lightning_is_g3() ){
	require dirname( __FILE__ ) . '/' . LIG_G3_DIR . '/functions.php';
} else {
	require dirname( __FILE__ ) . '/' . LIG_G2_DIR . '/functions.php';
}

require dirname( __FILE__ ) . '/inc/customize-basic.php';
require dirname( __FILE__ ) . '/inc/tgm-plugin-activation/tgm-config.php';

/**
 * 世代切り替えした時に同時にスキンも変更する処理
 *  
 * 世代は lightning_theme_options['generation'] で管理している。
 * 
 * 世代変更前のスキンを保存しておく処理
 * 
 * 		generetionに変更がある場合
 * 			保存前のフィルター pre_update_option で今の世代でのスキン名を配列に格納しておく
 * 
 */

// add_filter( 'pre_update_option_lightning_theme_options', 'lightning_save_previous_skin', 10, 3 );
// function lightning_save_previous_skin( $value, $old_value, $option ){
// 	if (  )
// }

/**
 * 
 * 世代切り替えの再に自動的にスキンを変更する処理
 * 
 * 		アクションフック update_option_lightning_theme_options で実行
 * 		変更先の世代が g3 の場合
 * 		前のスキンが保存されていない場合
 * 			origin3 にする
  * 
 */

// function lightning_change_generation( $old_value, $value, $option ){
// 	if ( empty( $old_value['generation'] ) ){
// 		if ( $value['generation'] !== 'g3' ){
// 			$old_skin = get_option( 'lightning_design_skin' );
// 			update_option( 'lightning_design_skin', 'origin3' );
// 		} elseif ( $) )
	
// 		}
// 	} 
// }
// add_action( 'update_option_lightning_theme_options', 'lightning_change_generation', 10, 3 );