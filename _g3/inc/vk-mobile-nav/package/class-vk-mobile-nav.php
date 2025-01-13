<?php
/*
このファイルの元ファイルは
https://github.com/vektor-inc/vektor-wp-libraries
にあります。修正の際は上記リポジトリのデータを修正してください。
*/

if ( ! class_exists( 'Vk_Mobile_Nav' ) ) {
	class Vk_Mobile_Nav {

		public static $version = '0.3.3';

		public function __construct() {
			/* Can not call get_called_class() on PHP5.2 */
			if ( function_exists( 'get_called_class' ) ) {
				// 11 指定がないと Lightning G2系などテーマによってカスタマイザーで選択できない場合がある
				add_action( 'after_setup_theme', array( get_called_class(), 'setup_menu' ), 11 );
				add_action( 'widgets_init', array( get_called_class(), 'setup_widget' ) );
				$vk_mobile_nav_html_hook_point = apply_filters( 'vk_mobile_nav_html_hook_point', 'wp_footer' );
				add_action( $vk_mobile_nav_html_hook_point, array( get_called_class(), 'menu_set_html' ) );
				add_action( 'wp_enqueue_scripts', array( get_called_class(), 'add_script' ) );
				add_action( 'wp_enqueue_scripts', array( get_called_class(), 'add_inline_css' ), 30 );

				add_action( 'customize_register', array( $this, 'customize_register' ) ); // $thisじゃないとエラーになる
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

			$option               = self::get_option();
			$btn_additional_class = '';
			if ( 'right' === $option['position'] ) {
				$btn_additional_class = ' position-right';
			}

			$menu_btn_text = apply_filters( 'vk_mobile_nav_menu_btn_text', __( 'MENU', 'lightning' ) );
			$menu_btn      = '<div id="vk-mobile-nav-menu-btn" class="vk-mobile-nav-menu-btn' . $btn_additional_class . '">' . $menu_btn_text . '</div>';

			if ( class_exists( 'Vk_Mobile_Fix_Nav' ) ) {
				$fix_nav_options = Vk_Mobile_Fix_Nav::get_options();
				// fixナビ内にメニュー展開ボタンを表示しない || fixナビ自体を表示しない
				if ( ! $fix_nav_options['add_menu_btn'] || $fix_nav_options['hidden'] ) {
					echo wp_kses_post( $menu_btn );
				}
			} else {
				echo wp_kses_post( $menu_btn );
			}

			echo '<div class="vk-mobile-nav vk-mobile-nav-' . esc_attr( $option['slide_type'] ) . '" id="vk-mobile-nav">';
			if ( is_active_sidebar( 'vk-mobile-nav-upper' ) ) {
				dynamic_sidebar( 'vk-mobile-nav-upper' );
			} elseif ( current_user_can( 'edit_theme_options' ) ) {
					echo '<div class="veu_adminEdit alert alert-info">';
					echo '<p>' . sprintf( __( 'This is the widget area.<br>You can set widget item from [ <a href="%s">Appearance > Customize</a> ] Page -> "Widgets" panel -> "Mobile Nav Upper" Panel.', 'lightning' ), admin_url( 'customize.php' ) ) . '</p>';
					echo '<p>' . __( '* This message is displayed only to users with editing authority.', 'lightning' ) . '</p>';
					echo '</div>';
			}

			$menu_vk_mobile = wp_nav_menu(
				array(
					'theme_location' => 'vk-mobile-nav',
					'container'      => '',
					'items_wrap'     => '<nav class="vk-mobile-nav-menu-outer" role="navigation"><ul id="%1$s" class="vk-menu-acc %2$s">%3$s</ul></nav>',
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
					'items_wrap'     => '<nav class="vk-mobile-nav-menu-outer" role="navigation"><ul id="%1$s" class="vk-menu-acc %2$s">%3$s</ul></nav>',
					'fallback_cb'    => '',
					'echo'           => false,
					// 'depth'          => 1,
				)
			);
			if ( $menu_vk_mobile ) {
				echo $menu_vk_mobile;
			} elseif ( $menu_theme_default ) {
				echo $menu_theme_default;
			} elseif ( current_user_can( 'edit_theme_options' ) ) {
					echo '<div class="veu_adminEdit alert alert-danger">';
					echo '<p>' . sprintf( __( 'Menu is not set.<br>Please set menu from [ <a href="%s">Appearance > Customize</a> ] Page -> "Menus" panel -> Menu Locations "Mobile Navigation".', 'lightning' ), admin_url( 'customize.php' ) ) . '</p>';
					echo '<p>' . __( '* This message is displayed only to users with editing authority.', 'lightning' ) . '</p>';
					echo '</div>';
			}

			if ( is_active_sidebar( 'vk-mobile-nav-bottom' ) ) {
				dynamic_sidebar( 'vk-mobile-nav-bottom' );
			} elseif ( current_user_can( 'edit_theme_options' ) ) {
					echo '<div class="veu_adminEdit alert alert-info">';
					echo '<p>' . sprintf( __( 'This is the widget area.<br>You can set widget item from [ <a href="%s">Appearance > Customize</a> ] Page -> "Widgets" panel -> "Mobile Nav Bottom" Panel.', 'lightning' ), admin_url( 'customize.php' ) ) . '</p>';
					echo '<p>' . __( '* This message is displayed only to users with editing authority.', 'lightning' ) . '</p>';
					echo '</div>';
			}

			echo '</div>';
		}

		/*
			Load js & CSS
		/*-------------------------------------------*/

		public static function add_script() {
			global $library_url;
			wp_register_script( 'vk-mobile-nav-js', $library_url . '/js/vk-mobile-nav.min.js', array(), self::$version );
			wp_enqueue_script( 'vk-mobile-nav-js' );
			wp_enqueue_style( 'vk-mobile-nav-css', $library_url . '/css/vk-mobile-nav-bright.css', array(), self::$version, 'all' );
		}

		/**
		 * Add vk mobile nav inline css
		 *
		 * @return void
		 */
		public static function add_inline_css() {
			global $library_url;
			$dynamic_css = '/* vk-mobile-nav */
			:root {
				--vk-mobile-nav-menu-btn-bg-src: url("' . esc_url( $library_url . '/images/vk-menu-btn-black.svg' ) . '");
				--vk-mobile-nav-menu-btn-close-bg-src: url("' . esc_url( $library_url . '/images/vk-menu-close-black.svg' ) . '");
				--vk-menu-acc-icon-open-black-bg-src: url("' . esc_url( $library_url . '/images/vk-menu-acc-icon-open-black.svg' ) . '");
				--vk-menu-acc-icon-open-white-bg-src: url("' . esc_url( $library_url . '/images/vk-menu-acc-icon-open-white.svg' ) . '");
				--vk-menu-acc-icon-close-black-bg-src: url("' . esc_url( $library_url . '/images/vk-menu-close-black.svg' ) . '");
				--vk-menu-acc-icon-close-white-bg-src: url("' . esc_url( $library_url . '/images/vk-menu-close-white.svg' ) . '");
			}
			';
			// delete before after space
			$dynamic_css = trim( $dynamic_css );
			// convert tab and br to space
			$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
			// Change multiple spaces to single space
			$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );
			global $vk_mobile_nav_inline_style_handle;
			wp_add_inline_style( $vk_mobile_nav_inline_style_handle, $dynamic_css );
		}


		public static function get_option() {
			$option = get_option( 'vk_mobile_nav_options' );
			$option = wp_parse_args( $option, self::default_options() );
			return $option;
		}

		public static function default_options() {
			$default_options = array(
				'position'   => 'left',
				'slide_type' => 'drop-in',
			);
			return $default_options;
		}

		/*
			Customizer
		/*-------------------------------------------*/

		public function customize_register( $wp_customize ) {

			// セクション、テーマ設定、コントロールを追加
			global $vk_mobile_nav_prefix;
			global $vk_mobile_nav_priority;
			if ( ! $vk_mobile_nav_priority ) {
				$vk_mobile_nav_priority = 900;
			}

			$default_options = $this->default_options();

			// セクション追加
			$wp_customize->add_section(
				'vk_mobile_nav_setting',
				array(
					'title'    => $vk_mobile_nav_prefix . __( 'Mobile Nav', 'lightning' ),
					'priority' => $vk_mobile_nav_priority,
				)
			);

			// position セッティング
			$wp_customize->add_setting(
				'vk_mobile_nav_options[position]',
				array(
					'default'           => $default_options['position'],
					'type'              => 'option', // 保存先 option or theme_mod
					'capability'        => 'edit_theme_options', // サイト編集者
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// position コントロール
			$wp_customize->add_control(
				'vk_mobile_nav_options[position]',
				array(
					'label'    => __( 'Menu button position', 'lightning' ),
					'section'  => 'vk_mobile_nav_setting',
					'settings' => 'vk_mobile_nav_options[position]',
					'type'     => 'radio',
					'choices'  => array(
						'left'  => __( 'Left', 'lightning' ),
						'right' => __( 'Right', 'lightning' ),
					),
				)
			);

			// slide_type セッティング
			$wp_customize->add_setting(
				'vk_mobile_nav_options[slide_type]',
				array(
					'default'           => $default_options['slide_type'],
					'type'              => 'option', // 保存先 option or theme_mod
					'capability'        => 'edit_theme_options', // サイト編集者
					'sanitize_callback' => 'sanitize_text_field',
				)
			);

			// slide_type コントロール
			$wp_customize->add_control(
				'vk_mobile_nav_options[slide_type]',
				array(
					'label'    => __( 'Menu slide direction', 'lightning' ),
					'section'  => 'vk_mobile_nav_setting',
					'settings' => 'vk_mobile_nav_options[slide_type]',
					'type'     => 'radio',
					'choices'  => array(
						'drop-in'  => __( 'Drop', 'lightning' ),
						'left-in'  => __( 'Left -> Right', 'lightning' ),
						'right-in' => __( 'Right -> Left', 'lightning' ),
					),
				)
			);

			/*
				Add Edit Customize Link Btn
			/*-------------------------------------------*/
			$wp_customize->selective_refresh->add_partial(
				'vk_mobile_nav_options[position]',
				array(
					'selector'        => '.vk-mobile-nav-menu-btn',
					'render_callback' => '',
				)
			);
		} // function customize_register( $wp_customize ) {
	} // class Vk_Mobile_Nav

	// Store in global variable so that hook in class can be removed
	global $vk_mobile_nav;
	$vk_mobile_nav = new Vk_Mobile_Nav();

}
