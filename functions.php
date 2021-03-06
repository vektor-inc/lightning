<?php

define( 'LIG_G3_DIR', '_g3' );
define( 'LIG_G2_DIR', '_g2' );

function lightning_is_g3(){
	$options = get_option( 'lightning_theme_options' );
	if ( isset($options['generation']) && 'g3' === $options['generation'] ){
		return true;
	}
}

if ( ! class_exists( 'LTG_Template_Redirect' ) ){
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
				add_filter( "{$type}_template_hierarchy", array( __CLASS__, 'template_hierarchy_redirect' ) );
			}
	
			// get_template_directory_uri()
			add_filter( 'template_directory_uri', array( __CLASS__, 'template_directory_uri' ) );

			// get_parent_theme_file_path()
			add_filter( 'parent_theme_file_path', array( __CLASS__, 'parent_theme_file_path' ), 10, 2 );
	
			// add_filter( 'template_directory', array( __CLASS__, 'template_directory' ), 10, 3 );
			add_filter( 'comments_template', array( __CLASS__, 'comments_template' ) );

			add_action( 'get_template_part', array( __CLASS__, 'get_template_part_fallback' ), 10, 3 );
	
			// woocommerce.php redirect
			add_filter( 'woocommerce_template_loader_files', array( __CLASS__, 'woocommerce_redirect' ), 10, 2 );
	
		}
	
		public static function woocommerce_redirect( $default_file ){
			if ( lightning_is_g3() ){
				$dir = LIG_G3_DIR;
			} else {
				$dir = LIG_G2_DIR;
			}
			/**
			 * 子テーマg階層のwoocommerce.phpを優先したいが子テーマ直下が優先されてしまう
			 * ※ 順番変えても効かないが、子テーマ内でどちらかの階層だけにあれば良い（両方に置くほうが悪い）のでOK
			 */
			$templates = $default_file;
			$templates[]   = 'woocommerce.php';
			$templates[]   = $dir . '/woocommerce.php';
			
			return $templates;
		}
	
		public static function theme_directory(){
			if ( lightning_is_g3() ){
				$dir = LIG_G3_DIR;
			} else {
				$dir = LIG_G2_DIR;
			}
			return $dir;
		}
	
		public static function template_hierarchy_redirect( $templates ){
			/**
			 * page-{slug}.php などのように - つきのファイルについては、
			 * 子テーマ内で g階層 がなくても親テーマより優先されなくてはならない。
			 */  
	
			/**
			*     
			* [] => child/_g2/page-service.php
			* [] => child/page-service.php
			* [] => parent/_g2/page-service.php
	
			* [] => child/_g2/page-9.php
			* [] => child/page-9.php
			* [] => parent/_g2/page-9.php
	
			* [] => child/_g2/page.php
			* [] => child/page.php
			* [] => parent/_g2/page.php
			*/
	
			$templates_new = array();
			foreach ( $templates as $key => $template){
				$templates_new[] = self::theme_directory() . '/' . $template;
				$templates_new[] = $template;
			}
			return $templates_new;
		}
	
		public static function template_directory_uri( $template_dir_uri  ){
			return $template_dir_uri . '/' . self::theme_directory();
		}
	
		public static function comments_template( $theme_template  ){
			$theme_template = get_stylesheet_directory() . '/' . self::theme_directory() . '/comments.php';
			return $theme_template;
		}

		// public static function template_directory( $template_dir, $template, $theme_root  ){
		// 	$template_dir = "$theme_root/$template";
		//     return $parent_theme_file_path . '/' . self::theme_directory();
		// }

		/**
		 * 
		 */
		public static function parent_theme_file_path( $path, $file  ){
			$file = ltrim( $file, '/' );
			if ( empty( $file ) ) {
				$path = get_template_directory() . '/' . self::theme_directory();
			} else {
				$path = get_template_directory() . '/' . self::theme_directory(). '/' . $file;
			}
		    return $path;
		}
	
		public static function get_template_part_fallback( $slug, $name, $template_names ){
	
			/*
			 $slug や $name は受け取れるが、$template_names が既に $slugの$nameを結合した配列を渡してくれているので、
			 $template_names を処理すれば良い
			 */
	
			/*
			主に子テーマで get_template_part() で親ファイルを呼び出しているケースにおいて
			親ファイルのパスが変更になっているので対応するための処理
	
			1.	引数にg階層が含まれない時に実行される
			2.	子テーマ内に該当ファイルがあればそのまま処理しされる。
			3.	g階層のファイルがない場合は子テーマでg階層のパスを追加したファイルがあれば処理
			4.	ただしこの時子テーマにg階層でないファイル(2.)がある場合は読み込んでしまうのは避けられないため、
				lightning_get_template_part() を使うか、子テーマ内では g階層の中にファイルを入れる。
			4.	なければ親階層にg階層をつけたファイルがあれば読み込む
			*/
	
			$g_dir = self::theme_directory();
	
			// 引数でうけとったパスが _g2 _ g3 を含んでいるか
			// preg_match( '/^'.LIG_G2_DIR.'/', $slug ,$matches );
	
			if ( preg_match( '/^' . $g_dir . '/', $slug ) ){
				// 含んでいるならそのまま標準処理で良いので return
				return;
			// 含んでいなかったら {
			} else {
	
				// 子テーマの場合のみ処理する
				
				/**
				 *  標準階層のファイル読み込みを先に処理している理由
				 * 	
				 * 	標準はファイルがあれば出力されるので、g階層のファイルとの二重出力となってしまうため
				 */
	
				if ( get_stylesheet() !== get_template()  ){
	
					// 子テーマ直下に引数のファイルがあるか確認
					// 親テーマの header.php など参照しないように子テーマの階層
	
					foreach( (array) $template_names as $template_name ){
						if ( ! $template_name ) {
							continue;
						}
						if ( file_exists( get_stylesheet_directory() . '/' . $template_name ) ) {
							// あれば標準処理で良いので何もせずに return
							return;
						}
					}
	
					/**
					 * 独自階層を付与した階層にファイルがあるか確認
					 * 
					 * わかる人ならg階層をつけて get_template_part() 書きそうだが、
					 * このリダイレクトが成功するなら本体も g階層つけずに get_template_part() 書けるので、
					 * それを真似してg階層無しで書いてくる人用の処理 
					 */
	
					foreach( (array) $template_names as $template_name ){
						if ( ! $template_name ) {
							continue;
						}
						$file_path = get_stylesheet_directory() . '/' . $g_dir . '/' . $template_name;
						if ( file_exists( $file_path ) ) {
							// 階層を追加したファイルが存在する場合は、標準処理では見つからないので読み込み実行する
							$require_once = false;
							load_template(  $file_path, $require_once );
							// 後続処理しないようにリターン
							return;
						}
					}
				}
	
				// 親テーマに独自階層を付与した階層にファイルがあるか確認
				foreach( (array) $template_names as $template_name ){
					if ( ! $template_name ) {
						continue;
					}
					$file_path = get_template_directory() . '/' . $g_dir . '/' . $template_name;
					if ( file_exists( $file_path ) ) {
						// 階層を追加したファイルが存在する場合は、標準処理では見つからないので読み込み実行する
						$require_once = false;
						load_template(  $file_path, $require_once );
						// 後続処理しないようにリターン
						return;
					}
				}
			}
		}
	}
	
	new LTG_Template_Redirect();
}

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