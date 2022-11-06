<?php //phpcs:ignore
/**
 * VK_Font_Awesome_Versions
 *
 * @package vektor-inc/font-awesome-versions
 * @license GPL-2.0+
 *
 * @version 0.4.1
 */

namespace VektorInc\VK_Font_Awesome_Versions;

/**
 * VkFontAwesomeVersions
 */
class VkFontAwesomeVersions {

	private static $version_default = '6_WebFonts_CSS';

	/**
	 * 直接 new VkFontAwesomeVersions() している場所がありえるので fallback
	 */
	function __construct() {
		self::init();
	}

	/**
	 * Initialise
	 *
	 * @return void
	 */
	public static function init() {
		/**
		 * テキストドメイン
		 */
		if ( did_action( 'init' ) ) {
			self::load_text_domain();
		} else {
			add_action( 'init', array( __CLASS__, 'load_text_domain' ) );
		}

		/**
		 * Reason of Using through the after_setup_theme is
		 * to be able to change the action hook point of css load from theme..
		 */
		add_action( 'after_setup_theme', array( __CLASS__, 'load_css_action' ) );
		add_action( 'admin_notices', array( __CLASS__, 'old_notice' ) );

		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );

		/* admin init だと use_block_editor_for_post が効かない */
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'load_admin_font_awesome' ) );

		add_action( 'enqueue_block_editor_assets', array( __CLASS__, 'load_gutenberg_font_awesome' ) );
		add_action( 'wp_head', array( __CLASS__, 'dynamic_css' ), 3 );
		add_filter( 'body_class', array( __CLASS__, 'add_body_class_fa_version' ) );
	}

	public static function load_text_domain() {
		// We're not using load_plugin_textdomain() or its siblings because figuring out where
		// the library is located (plugin, mu-plugin, theme, custom wp-content paths) is messy.
		$domain = 'font-awesome-versions';
		$locale = apply_filters(
			'plugin_locale',
			( is_admin() && function_exists( 'get_user_locale' ) ) ? get_user_locale() : get_locale(),
			$domain
		);

		$mo_file = $domain . '-' . $locale . '.mo';
		$path    = realpath( dirname( __FILE__ ) . '/languages' );
		if ( $path && file_exists( $path ) ) {
			load_textdomain( $domain, $path . '/' . $mo_file );
		}
	}

	/**
	 * Undocumented function
	 *
	 * @since 0.3.0
	 * @param string $path : PHPUnit テスト用
	 * @return string $uri
	 */
	public static function get_directory_uri( $path = '' ) {

		$uri = '';

		if ( ! $path ) {
			// このファイルのパス.
			$path = wp_normalize_path( dirname( __FILE__ ) );
		}

		// ファイルのパスの wp-content より前の部分を site_url() に置換する
		// ABSPATH の部分を site_url() に置換したいところだが、ABSPATHは WordPress.com で /wordpress/core/5.9.3/ のような返し方をされて、一般的なサーバーのパスとは異なるので、置換などには使用しない.
		preg_match( '/(.*)(wp-content.*)/', $path, $matches, PREG_OFFSET_CAPTURE );
		if ( ! empty( $matches[2][0] ) ) {
			$uri = site_url( '/' ) . $matches[2][0] . '/';
		}

		return $uri;
	}

	/**
	 * アイコンの class 名だけ保存されている場合も i タグに変換して出力する
	 *
	 * @param string $option : saved value.
	 * @param string $additional_class : i タグに追加する Font Awesome 以外のクラス名.
	 *
	 * @return string $icon_html : icon tag
	 */
	public static function get_icon_tag( $option = '', $additional_class = '' ) {
		if ( empty( $option ) ) {
			return;
		}
		if (
			false !== strpos( $option, '<i' ) &&
			false !== strpos( $option, '</i>' )
		) {
			$icon_html = $option;
			if ( $additional_class ) {
				preg_match( '/(<i class=\")(.*)(\"><\/i>)/', $option, $matches );
				if ( ! empty( $matches[2] ) ) {
					$icon_html = '<i class="' . esc_attr( $matches[2] ) . ' ' . esc_attr( $additional_class ) . '"></i>';
				}
			}
		} else {

			// 4.7 fall back.
			$print_fa = '';
			$print_fa = self::print_fa();

			$class = $print_fa . $option;

			// Font Awesome 以外のクラス名がある場合.
			if ( $additional_class ) {
				$class .= ' ' . $additional_class;
			}

			$icon_html = '<i class="' . esc_attr( $class ) . '"></i>';
		}
		return $icon_html;
	}

	/**
	 * Load Font Awesome Action
	 *
	 * @return void
	 */
	public static function load_css_action() {
		$hook_point = apply_filters( 'vkfa_enqueue_point', 'wp_enqueue_scripts' );
		add_action( $hook_point, array( __CLASS__, 'load_font_awesome' ) );
	}

	/**
	 * Version Array
	 *
	 * @return array $versions
	 */
	public static function versions() {

		$font_awesome_directory_uri = self::get_directory_uri();

		$versions = array(
			'6_SVG_JS'       => array(
				'label'   => '6 SVG with JS ( ' . __( 'Not recommended', 'font-awesome-versions' ) . ' )',
				'version' => '6.1.0',
				'type'    => 'svg-with-js',
				/* [ Notice ] use editor css*/
				'url_css' => $font_awesome_directory_uri . 'versions/6/css/all.min.css',
				'url_js'  => $font_awesome_directory_uri . 'versions/6/js/all.min.js',
			),
			'6_WebFonts_CSS' => array(
				'label'   => '6 Web Fonts with CSS',
				'version' => '6.1.0',
				'type'    => 'web-fonts-with-css',
				'url_css' => $font_awesome_directory_uri . 'versions/6/css/all.min.css',
				'url_js'  => '',
			),
			'5_SVG_JS'       => array(
				'label'   => '5 SVG with JS ( ' . __( 'Not recommended', 'font-awesome-versions' ) . ' )',
				'version' => '5.15.4',
				'type'    => 'svg-with-js',
				/* [ Notice ] use editor css*/
				'url_css' => $font_awesome_directory_uri . 'versions/5/css/all.min.css',
				'url_js'  => $font_awesome_directory_uri . 'versions/5/js/all.min.js',
			),
			'5_WebFonts_CSS' => array(
				'label'   => '5 Web Fonts with CSS',
				'version' => '5.15.4',
				'type'    => 'web-fonts-with-css',
				'url_css' => $font_awesome_directory_uri . 'versions/5/css/all.min.css',
				'url_js'  => '',
			),
			'4.7'            => array(
				'label'   => '4.7 ( ' . __( 'Not recommended', 'font-awesome-versions' ) . ' )',
				'version' => '4.7',
				'type'    => 'web-fonts-with-css',
				'url_css' => $font_awesome_directory_uri . 'versions/4.7.0/css/font-awesome.min.css',
				'url_js'  => '',
			),
		);
		return $versions;
	}

	/**
	 * Get Font Awesome Option
	 * 5系の時の保存名が適切でなかったために補正している
	 *
	 * @return string $current : current version slug
	 */
	public static function get_option_fa() {
		$current = get_option( 'vk_font_awesome_version', self::$version_default );
		if ( '5.0_WebFonts_CSS' === $current ) {
			update_option( 'vk_font_awesome_version', '5_WebFonts_CSS' );
			$current = '5_WebFonts_CSS';
		} elseif ( '5.0_SVG_JS' === $current ) {
			update_option( 'vk_font_awesome_version', '5_SVG_JS' );
			$current = '5_SVG_JS';
		}
		return $current;
	}

	public static function current_info() {
		$versions       = self::versions();
		$current_option = self::get_option_fa();
		$current_info   = $versions[ $current_option ];
		return $current_info;
	}

	/**
	 * Display icon list link
	 *
	 * @param string $type = 'class' : クラス名のみ / $type = 'html' : i タグ表示.
	 * @param string $example_class_array 例として表示するクラス名のバージョンごとの配列.
	 * @return string $ex_and_link
	 */
	public static function ex_and_link( $type = '', $example_class_array = array() ) {
		$current_option = self::get_option_fa();
		if ( '6_WebFonts_CSS' === $current_option || '6_SVG_JS' === $current_option ) {
			$version = '6';
			$link    = 'https://fontawesome.com/icons?d=gallery&m=free';
			if ( ! empty( $example_class_array ['v6'] ) ) {
				$icon_class = esc_attr( $example_class_array['v6'] );
			} else {
				$icon_class = 'fa-regular fa-file-lines';
			}
		} elseif ( '5_WebFonts_CSS' === $current_option || '5_SVG_JS' === $current_option ) {
			$version = '5';
			$link    = 'https://fontawesome.com/v5/search?m=free';
			if ( ! empty( $example_class_array ['v5'] ) ) {
				$icon_class = esc_attr( $example_class_array['v5'] );
			} else {
				$icon_class = 'far fa-file-alt';
			}
		} else {
			$version = '4.7';
			$link    = 'https://fontawesome.com/v4/icons/';
			if ( ! empty( $example_class_array ['v4.7'] ) ) {
				$icon_class = esc_attr( $example_class_array['v4.7'] );
			} else {
				$icon_class = 'fa-file-text-o';
			}
		}

		$ex_and_link  = '<div style="margin-top:5px"><strong>Font Awesome ' . $version . '</strong></div>';
		$ex_and_link .= __( 'Ex ) ', 'font-awesome-versions' );
		if ( 'class' === $type ) {
			$ex_and_link .= $icon_class;
		} else {
			$ex_and_link .= esc_html( '<i class="' . $icon_class . '"></i>' );
		}
		$ex_and_link .= '<br>[ -> <a href="' . $link . '" target="_blank">' . __( 'Font Awesome Icon list', 'font-awesome-versions' ) . '</a> ]';

		return wp_kses_post( $ex_and_link );
	}

	/**
	 * When use Font Awesome 4,7 then print 'fa '.
	 *
	 * @var strings;
	 */
	public static function print_fa() {
		$fa             = '';
		$current_option = self::get_option_fa();
		if ( '4.7' === $current_option ) {
			$fa = 'fa ';
		}
		return $fa;
	}

	static function load_font_awesome() {
		$current = self::current_info();
		if ( 'svg-with-js' === $current['type'] ) {
			wp_enqueue_script( 'vk-font-awesome-js', $current['url_js'], array(), $current['version'] );
			// [ Danger ] This script now causes important errors
			// wp_add_inline_script( 'font-awesome-js', 'FontAwesomeConfig = { searchPseudoElements: true };', 'before' );
		} else {
			wp_enqueue_style( 'vk-font-awesome', $current['url_css'], array(), $current['version'] );
		}
	}

	static function load_admin_font_awesome( $post ) {
		$current = self::current_info();
		// ブロックエディタでこれがあるとコンソールでエラー吐かれるのでclassicエディタのときだけ読み込み.
		if ( ! function_exists( 'use_block_editor_for_post' ) || ! use_block_editor_for_post( $post ) ) {
			add_editor_style( $current['url_css'] );
		}
	}

	static function load_gutenberg_font_awesome() {
		$current_info = self::current_info();
		wp_enqueue_style( 'gutenberg-font-awesome', $current_info['url_css'], array(), $current_info['version'] );
	}

	/**
	 * Add body class
	 *
	 * @return string font awesome version slug
	 */
	public static function add_body_class_fa_version( $class ) {
		$current_option = self::get_option_fa();
		if ( '4.7' === $current_option ) {
			$class[] = 'fa_v4';
		} elseif ( '5_WebFonts_CSS' === $current_option ) {
			$class[] = 'fa_v5_css';
		} elseif ( '5_SVG_JS' === $current_option ) {
			$class[] = 'fa_v5_svg';
		} elseif ( '6_WebFonts_CSS' === $current_option ) {
			$class[] = 'fa_v6_css';
		} elseif ( '6_SVG_JS' === $current_option ) {
			$class[] = 'fa_v6_svg';
		}
		return $class;
	}

	/**
	 * Output dynbamic css according to Font Awesome versions
	 *
	 * @return void
	 */
	public static function dynamic_css() {
		$current     = self::get_option_fa();
		$dynamic_css = '';
		if ( '4.7' === $current ) {
			$dynamic_css = '.tagcloud a:before { font-family:FontAwesome;content:"\f02b"; }';
		} elseif ( '5_WebFonts_CSS' === $current ) {
			$dynamic_css = '.tagcloud a:before { font-family: "Font Awesome 5 Free";content: "\f02b";font-weight: bold; }';
		} elseif ( '5_SVG_JS' === $current ) {
			$dynamic_css = '.tagcloud a:before { content:"" }';
		} elseif ( '6_WebFonts_CSS' === $current ) {
			$dynamic_css = '.tagcloud a:before { font-family: "Font Awesome 5 Free";content: "\f02b";font-weight: bold; }';
		} elseif ( '6_SVG_JS' === $current ) {
			$dynamic_css = '.tagcloud a:before { content:"" }';
		}
		// delete before after space.
		$dynamic_css = trim( $dynamic_css );
		// convert tab and br to space.
		$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
		// Change multiple spaces to single space.
		$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );

		global $vkfav_set_enqueue_handle_style;
		wp_add_inline_style( $vkfav_set_enqueue_handle_style, $dynamic_css );
	}

	/**
	 * 同じ絵柄のアイコンをバージョンによって出し分ける場合に切り替える
	 *
	 * @param string $class_v4 : v4 の場合のアイコン
	 * @param string $class_v5 : v5 の場合のアイコン
	 * @param string $class_v6 : v6 の場合のアイコン
	 * @return void
	 */
	public static function class_switch( $class_v4 = '', $class_v5 = '', $class_v6 = '' ) {
		$current_option = self::get_option_fa();
		if ( '6_WebFonts_CSS' === $current_option || '6_SVG_JS' === $current_option ) {
			return $class_v6;
		} elseif ( '5_WebFonts_CSS' === $current_option || '5_SVG_JS' === $current_option ) {
			return $class_v5;
		} else {
			return $class_v4;
		}
	}

	public static function old_notice() {
		$old_notice     = '';
		$current_option = self::get_option_fa();
		if ( '4.7' === $current_option ) {
			$old_notice .= '<div class="error">';
			$old_notice .= '<p>' . __( 'An older version of Font Awesome is selected. This version will be removed by August 2022.', 'font-awesome-versions' ) . '</p>';
			$old_notice .= '<p>' . __( 'Please change the version of FontAwesome on the Appearance > Customize screen.', 'font-awesome-versions' ) . '</p>';
			$old_notice .= '<p>' . __( '* It is necessary to reset the icon font in the place where Font Awesome is used.', 'font-awesome-versions' ) . '</p>';
			$old_notice .= '</div>';
		}
		echo wp_kses_post( $old_notice );
	}

	/**
	 * Customize_register
	 *
	 * @param object $wp_customize : customize object.
	 * @return void
	 */
	public static function customize_register( $wp_customize ) {

		global $vkfav_customize_panel_prefix;
		global $vkfav_customize_panel_priority;
		if ( ! $vkfav_customize_panel_priority ) {
			$vkfav_customize_panel_priority = 450;
		}

		$wp_customize->add_section(
			'VK Font Awesome',
			array(
				'title'    => $vkfav_customize_panel_prefix . __( 'Font Awesome', 'font-awesome-versions' ),
				'priority' => $vkfav_customize_panel_priority,
			)
		);

		$wp_customize->add_setting(
			'vk_font_awesome_version',
			array(
				'default'           => '5_WebFonts_CSS',
				'type'              => 'option',
				'capability'        => 'edit_theme_options',
				'sanitize_callback' => 'sanitize_text_field',
			)
		);

		$versions = self::versions();
		foreach ( $versions as $key => $value ) {
			$choices[ $key ] = $value['label'];
		}

		$wp_customize->add_control(
			'vk_font_awesome_version',
			array(
				'label'       => __( 'Font Awesome Version', 'font-awesome-versions' ),
				'section'     => 'VK Font Awesome',
				'settings'    => 'vk_font_awesome_version',
				'description' => __( '4.7 will be abolished in the near future.', 'font-awesome-versions' ),
				'type'        => 'select',
				'priority'    => '',
				'choices'     => $choices,
			)
		);
	}

}
