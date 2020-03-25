<?php
/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

if ( ! class_exists( 'Vk_Mobile_Nav' ) ) {
	class Vk_Mobile_Nav {

		public static $version = '0.0.1';

		public function __construct() {
			/* Can not call get_called_class() on PHP5.2 */
			if ( function_exists( 'get_called_class' ) ) {
				add_action( 'after_setup_theme', array( get_called_class(), 'setup_menu' ) );
				add_action( 'widgets_init', array( get_called_class(), 'setup_widget' ) );
				add_action( 'wp_footer', array( get_called_class(), 'menu_set_html' ) );
				add_action( 'wp_enqueue_scripts', array( get_called_class(), 'add_script' ) );
			}
			add_filter( 'body_class', array( $this, 'add_body_class_mobile_device' ) );
		}
		public static function init() {
			// add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		}

		/**
		 * body class 端末識別クラス追加
		 *
		 * @return [type] [description]
		 */
		function add_body_class_mobile_device( $class ) {
			if ( wp_is_mobile() ) {
				$class[] = 'device-mobile';
			} else {
				$class[] = 'device-pc';
			}
			return $class;
		}

		/**
		 * モバイル用メニュー追加
		 *
		 * @return [type] [description]
		 */
		public static function setup_menu() {
				register_nav_menus( array( 'vk-mobile-nav' => 'Mobile Navigation' ) );
		}

		/**
		 * モバイルメニュー用ウィジェットエリア追加
		 *
		 * @return [type] [description]
		 */
		static function setup_widget() {

			register_sidebar(
				array(
					'name'          => __( 'Mobile Nav Upper', 'lightning' ),
					'id'            => 'vk-mobile-nav-upper',
					'before_widget' => '<aside class="widget vk-mobile-nav-widget %2$s" id="%1$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h1 class="widget-title subSection-title">',
					'after_title'   => '</h1>',
				)
			);
			register_sidebar(
				array(
					'name'          => __( 'Mobile Nav Bottom', 'lightning' ),
					'id'            => 'vk-mobile-nav-bottom',
					'before_widget' => '<aside class="widget vk-mobile-nav-widget %2$s" id="%1$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h1 class="widget-title subSection-title">',
					'after_title'   => '</h1>',
				)
			);
		}

		public static function menu_set_html() {

			if ( class_exists( 'Vk_Mobile_Fix_Nav' ) ) {
				$options = Vk_Mobile_Fix_Nav::get_options();
				// fixナビにメニュー展開ボタンを表示しない || fixナビ自体を表示しない
				if ( ! $options['add_menu_btn'] || $options['hidden'] ) {
					echo '<div class="vk-mobile-nav-menu-btn">MENU</div>';
				}
			} else {
				echo '<div class="vk-mobile-nav-menu-btn">MENU</div>';
			}

			echo '<div class="vk-mobile-nav" id="vk-mobile-nav">';
			if ( is_active_sidebar( 'vk-mobile-nav-upper' ) ) {
				dynamic_sidebar( 'vk-mobile-nav-upper' );
			} else {
				if ( current_user_can( 'edit_theme_options' ) ) {
					echo '<div class="veu_adminEdit alert alert-info">';
					echo '<p>' . sprintf( __( 'This is the widget area.<br>You can set widget item from [ <a href="%s">Appearance > Customize</a> ] Page -> "Widgets" panel -> "Mobile Nav Upper" Panel.', 'lightning' ), admin_url( 'customize.php' ) ) . '</p>';
					echo '<p>' . __( '* This message is displayed only to users with editing authority.', 'lightning' ) . '</p>';
					echo '</div>';
				}
			}

			$menu_vk_mobile = wp_nav_menu(
				array(
					'theme_location' => 'vk-mobile-nav',
					'container'      => '',
					'items_wrap'     => '<nav class="global-nav" role="navigation"><ul id="%1$s" class="vk-menu-acc  %2$s">%3$s</ul></nav>',
					'fallback_cb'    => '',
					'echo'           => false,
					// 'depth'          => 1,
				)
			);
			global $default_nav;
			$menu_theme_default = wp_nav_menu(
				array(
					'theme_location' => $default_nav,
					'container'      => '',
					'items_wrap'     => '<nav class="global-nav"><ul id="%1$s" class="vk-menu-acc  %2$s">%3$s</ul></nav>',
					'fallback_cb'    => '',
					'echo'           => false,
					// 'depth'          => 1,
				)
			);
			if ( $menu_vk_mobile ) {
				echo $menu_vk_mobile;
			} elseif ( $menu_theme_default ) {
				echo $menu_theme_default;
			} else {
				if ( current_user_can( 'edit_theme_options' ) ) {
					echo '<div class="veu_adminEdit alert alert-danger">';
					echo '<p>' . sprintf( __( 'Menu is not set.<br>Please set menu from [ <a href="%s">Appearance > Customize</a> ] Page -> "Menus" panel -> Menu Locations "Mobile Navigation".', 'lightning' ), admin_url( 'customize.php' ) ) . '</p>';
					echo '<p>' . __( '* This message is displayed only to users with editing authority.', 'lightning' ) . '</p>';
					echo '</div>';
				}
			}

			if ( is_active_sidebar( 'vk-mobile-nav-bottom' ) ) {
				dynamic_sidebar( 'vk-mobile-nav-bottom' );
			} else {
				if ( current_user_can( 'edit_theme_options' ) ) {
					echo '<div class="veu_adminEdit alert alert-info">';
					echo '<p>' . sprintf( __( 'This is the widget area.<br>You can set widget item from [ <a href="%s">Appearance > Customize</a> ] Page -> "Widgets" panel -> "Mobile Nav Bottom" Panel.', 'lightning' ), admin_url( 'customize.php' ) ) . '</p>';
					echo '<p>' . __( '* This message is displayed only to users with editing authority.', 'lightning' ) . '</p>';
					echo '</div>';
				}
			}

			echo '</div>';
		}

		/*
		  Load js & CSS
		/*-------------------------------------------*/

		public static function add_script() {
			global $library_url;
			wp_register_script( 'vk-mobile-nav-js', $library_url . 'js/vk-mobile-nav.min.js', array( 'jquery' ), self::$version );
			wp_enqueue_script( 'vk-mobile-nav-js' );
			wp_enqueue_style( 'vk-mobile-nav-css', $library_url . 'css/vk-mobile-nav-bright.css', array(), self::$version, 'all' );
		}

	} // class Vk_Mobile_Nav

	// Store in global variable so that hook in class can be removed
	global $vk_mobile_nav;
	$vk_mobile_nav = new Vk_Mobile_Nav();

}
