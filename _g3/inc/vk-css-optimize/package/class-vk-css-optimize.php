<?php
/**
 * VK CSS Optimize
 *
 * @package VK CSS Optimize
 */

/*
The original of this file is located at:
https://github.com/vektor-inc/vektor-wp-libraries
If you want to change this file, please change the original file.
*/

if ( ! class_exists( 'VK_CSS_Optimize' ) ) {
	/**
	 * VK CSS Optimize
	 */
	class VK_CSS_Optimize {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
			add_filter( 'css_tree_shaking_exclude', array( __CLASS__, 'tree_shaking_exclude' ) );

			$options = self::get_css_optimize_options();

			if ( ! empty( $options['tree_shaking'] ) ) {
				add_filter( 'wp_using_themes', array( __CLASS__, 'get_html_start' ), 1, 1 );
			}

			if ( ! empty( $options['preload'] ) ) {
				add_filter( 'style_loader_tag', array( __CLASS__, 'css_preload' ), 10, 4 );
			}
		}

		/**
		 * Customize Register
		 *
		 * @param object $wp_customize : wp custommize object .
		 */
		public static function customize_register( $wp_customize ) {
			global $prefix_customize_panel;
			$wp_customize->add_section(
				'css_optimize',
				array(
					'title'    => $prefix_customize_panel . __( 'CSS Optimize ( Speed up ) Settings', 'lightning' ),
					'priority' => 450,
				)
			);

			// Tree shaking.
			$wp_customize->add_setting(
				'tree_shaking_title',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				new VK_Custom_Html_Control(
					$wp_customize,
					'tree_shaking_title',
					array(
						'label'            => __( 'Tree shaking', 'lightning' ),
						'section'          => 'css_optimize',
						'type'             => 'text',
						'custom_title_sub' => '',
					)
				)
			);

			$wp_customize->add_setting(
				'vk_css_optimize_options[tree_shaking]',
				array(
					'default'           => '',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				'vk_css_optimize_options[tree_shaking]',
				array(
					'label'       => __( 'Tree shaking activation settings', 'lightning' ),
					'section'     => 'css_optimize',
					'settings'    => 'vk_css_optimize_options[tree_shaking]',
					'type'        => 'select',
					'description' => __( 'Output only the main CSS of the page inline', 'lightning' ),
					'choices'     => array(
						''       => __( 'Nothing to do', 'lightning' ),
						'active' => __( 'Active Tree shaking (Recomend)', 'lightning' ),
					),
				)
			);

			$wp_customize->add_setting(
				'vk_css_optimize_options[tree_shaking_class_exclude]',
				array(
					'default'           => '',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				'vk_css_optimize_options[tree_shaking_class_exclude]',
				array(
					'label'       => __( 'Exclude class of Tree shaking', 'lightning' ),
					'section'     => 'css_optimize',
					'settings'    => 'vk_css_optimize_options[tree_shaking_class_exclude]',
					'type'        => 'textarea',
					'description' => __( 'If you choose "Active Tree shaking" that delete the useless css.If you using active css class that please fill in class name. Ex) btn-active,slide-active,scrolled', 'lightning' ),
				)
			);

			// Preload.
			$wp_customize->add_setting(
				'css_preload_title',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				new VK_Custom_Html_Control(
					$wp_customize,
					'css_preload_title',
					array(
						'label'            => __( 'Preload CSS', 'lightning' ),
						'section'          => 'css_optimize',
						'type'             => 'text',
						'custom_title_sub' => '',
					)
				)
			);

			$wp_customize->add_setting(
				'vk_css_optimize_options[preload]',
				array(
					'default'           => '',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				'vk_css_optimize_options[preload]',
				array(
					'label'       => __( 'Preload CSS activation settings', 'lightning' ),
					'section'     => 'css_optimize',
					'settings'    => 'vk_css_optimize_options[preload]',
					'description' => __( 'Preload css except for critical css', 'lightning' ),
					'type'        => 'select',
					'choices'     => array(
						''       => __( 'Nothing to do', 'lightning' ),
						'active' => __( 'Active Preload CSS (Recomend)', 'lightning' ),
					),
				)
			);

			$wp_customize->add_setting(
				'vk_css_optimize_options[preload_handle_exclude]',
				array(
					'default'           => '',
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => 'sanitize_text_field',
				)
			);
			$wp_customize->add_control(
				'vk_css_optimize_options[preload_handle_exclude]',
				array(
					'label'       => __( 'Exclude class of Preload CSS', 'lightning' ),
					'section'     => 'css_optimize',
					'settings'    => 'vk_css_optimize_options[preload_handle_exclude]',
					'type'        => 'textarea',
					'description' => __( 'If you choose "Active Preload CSS" that css load timing was changed.If you have any do not want to preload css file that please fill in handle(id) name. Ex) pluginname_a-style,pluginname_b-css', 'lightning' ),
				)
			);

		}

		/**
		 * CSS Optimize Default Options
		 *
		 * @return array $vk_css_optimize_options_default .
		 */
		public static function get_css_optimize_options_default() {
			$vk_css_optimize_options_default = array(
				'tree_shaking' => '',
				'preload'      => '',
			);
			return apply_filters( 'vk_css_optimize_options_default', $vk_css_optimize_options_default );
		}

		/**
		 * CSS Optimize Options
		 * 古いオプションやデフォルト値を結合して返す
		 *
		 * @return array $vk_css_optimize_options .
		 */
		public static function get_css_optimize_options() {

			$theme_textdomain = wp_get_theme()->get( 'TextDomain' );

			// CSS高速化を各テーマなどで保存していた頃の互換処理.
			if ( 'lightning' === $theme_textdomain || 'lightning-pro' === $theme_textdomain ) {
				$old_options = get_option( 'lightning_theme_options' );
			} elseif ( 'katawara' === $theme_textdomain ) {
				$old_options = get_option( 'katawara_theme_options' );
			} else {
				$old_options = get_option( 'vk_blocks_options' );
			}

			$vk_css_optimize_options         = get_option( 'vk_css_optimize_options' );
			$vk_css_optimize_options_default = self::get_css_optimize_options_default();

			// 新しい保存値に保存されていない場合.
			if ( ! isset( $vk_css_optimize_options['tree_shaking'] ) ) {
				// 古い設定がある場合（互換処理）.
				if ( isset( $old_options['optimize_css'] ) ) {
					if ( 'optomize-all-css' === $old_options['optimize_css'] || 'tree-shaking' === $old_options['optimize_css'] ) {
						$vk_css_optimize_options['tree_shaking'] = 'active';
					} else {
						$vk_css_optimize_options['tree_shaking'] = '';
					}
				}
			}

			// 除外指定がない場合.
			if ( ! isset( $vk_css_optimize_options['tree_shaking_class_exclude'] ) ) {
				// 古いoption値で除外指定が存在する場合（互換処理）.
				if ( ! empty( $old_options['tree_shaking_class_exclude'] ) ) {
					$vk_css_optimize_options['tree_shaking_class_exclude'] = esc_html( $old_options['tree_shaking_class_exclude'] );
				}
			}

			// プリロード指定がない場合.
			if ( ! isset( $vk_css_optimize_options['preload'] ) ) {
				// 古いoption値でプリロード設定が存在する場合（互換処理）.
				if ( isset( $old_options['optimize_css'] ) ) {
					if ( 'optomize-all-css' === $old_options['optimize_css'] ) {
						$vk_css_optimize_options['preload'] = 'active';
					} else {
						$vk_css_optimize_options['preload'] = '';
					}
				}
			}
			$vk_css_optimize_options = wp_parse_args( $vk_css_optimize_options, $vk_css_optimize_options_default );
			if (
				! isset( $vk_css_optimize_options['tree_shaking'] ) ||
				! isset( $vk_css_optimize_options['tree_shaking_class_exclude'] ) ||
				! isset( $vk_css_optimize_options['preload'] )
			) {
				update_option( 'vk_css_optimize_options', $vk_css_optimize_options );
			}

			return $vk_css_optimize_options;
		}

		/**
		 * Get HTML Document Start
		 *
		 * @param bool $is_use_themes .
		 * @return $is_use_themes;
		 */
		public static function get_html_start( $is_use_themes ) {
			// template_redirect が呼ばれる前でのみ実行する .
			if ( $is_use_themes && did_action( 'template_redirect' ) === 0 ) {
				// バッファ開始.
				ob_start( 'VK_CSS_Optimize::css_tree_shaking_buffer' );
			}
			return $is_use_themes;
		}

		/**
		 * Array of Apply Tree Shaking
		 * Tree Shaking にかけるCSS情報の配列
		 *
		 * @return array $vk_css_tree_shaking_array.
		 */
		public static function css_tree_shaking_array() {
			$vk_css_tree_shaking_array = array();
			$vk_css_tree_shaking_array = apply_filters( 'vk_css_tree_shaking_array', $vk_css_tree_shaking_array );
			return $vk_css_tree_shaking_array;
		}

		/**
		 * Array of Apply Simple Minify
		 * 単純なCSS最小化にかけるCSS情報の配列
		 *
		 * @return array $vk_css_simple_minify_array
		 */
		public static function css_simple_minify_array() {
			$vk_css_simple_minify_array = array();
			$vk_css_simple_minify_array = apply_filters( 'vk_css_simple_minify_array', $vk_css_simple_minify_array );
			return $vk_css_simple_minify_array;
		}

		/**
		 * Change Buffer of HTML Document
		 *
		 * @param string $buffer Gotten HTML Document
		 * @return $buffer
		 */
		public static function css_tree_shaking_buffer( $buffer ) {

			$options = self::get_css_optimize_options();

			// Lode Modules.
			// Tree shaking モジュール読み込み .
			require_once dirname( __FILE__ ) . '/class-css-tree-shaking.php';

			// Load CSS Arrays
			// 軽量化するCSSの情報配列読み込み.
			$vk_css_tree_shaking_array  = self::css_tree_shaking_array();
			$vk_css_simple_minify_array = self::css_simple_minify_array();

			// WP_Filesystem() が使えるように読み込み.
			require_once ABSPATH . 'wp-admin/includes/file.php';

			// CSS Tree Shaking //////////////////////////////////////////// .

			foreach ( $vk_css_tree_shaking_array as $vk_css_array ) {

				// 読み込むCSSファイルのパス.
				$path_name = $vk_css_array['path'];
				if ( WP_Filesystem() ) {
					global $wp_filesystem;
					// CSSのファイルの中身を取得.
					$css = $wp_filesystem->get_contents( $path_name );
				}

				// ree shaking を実行して再格納 .
				$css = celtislab\v2_1\CSS_tree_shaking::extended_minify( celtislab\v2_1\CSS_tree_shaking::simple_minify( $css ), $buffer );

				// ファイルで読み込んでいるCSSを直接出力に置換（バージョンパラメーターあり）.
				$buffer = str_replace(
					'<link rel=\'stylesheet\' id=\'' . $vk_css_array['id'] . '-css\'  href=\'' . $vk_css_array['url'] . '?ver=' . $vk_css_array['version'] . '\' type=\'text/css\' media=\'all\' />',
					'<style id=\'' . $vk_css_array['id'] . '-css\' type=\'text/css\'>' . $css . '</style>',
					$buffer
				);

				// ファイルで読み込んでいるCSSを直接出力に置換（バージョンパラメーターなし）.
				$buffer = str_replace(
					'<link rel=\'stylesheet\' id=\'' . $vk_css_array['id'] . '-css\'  href=\'' . $vk_css_array['url'] . '\' type=\'text/css\' media=\'all\' />',
					'<style id=\'' . $vk_css_array['id'] . '-css\' type=\'text/css\'>' . $css . '</style>',
					$buffer
				);

			}

			// CSS Simply Minify //////////////////////////////////////////// .

			foreach ( $vk_css_simple_minify_array as $vk_css_array ) {

				$path_name = $vk_css_array['path'];
				if ( WP_Filesystem() ) {
					global $wp_filesystem;
					$css = $wp_filesystem->get_contents( $path_name );
				}

				$css = celtislab\v2_1\CSS_tree_shaking::simple_minify( $css );

				// ファイルで読み込んでいるCSSを直接出力に置換（バージョンパラメーターあり）.
				$buffer = str_replace(
					'<link rel=\'stylesheet\' id=\'' . $vk_css_array['id'] . '-css\'  href=\'' . $vk_css_array['url'] . '?ver=' . $vk_css_array['version'] . '\' type=\'text/css\' media=\'all\' />',
					'<style id=\'' . $vk_css_array['id'] . '-css\' type=\'text/css\'>' . $css . '</style>',
					$buffer
				);
				// ファイルで読み込んでいるCSSを直接出力に置換（バージョンパラメーターなし）.
				$buffer = str_replace(
					'<link rel=\'stylesheet\' id=\'' . $vk_css_array['id'] . '-css\'  href=\'' . $vk_css_array['url'] . '\' type=\'text/css\' media=\'all\' />',
					'<style id=\'' . $vk_css_array['id'] . '-css\' type=\'text/css\'>' . $css . '</style>',
					$buffer
				);

			}

			return $buffer;
		}

		/**
		 * Undocumented function
		 *
		 * @param string $tag .
		 * @param string $handle .
		 * @param string $href .
		 * @param string $media .
		 * @return string $tag .
		 */
		public static function css_preload( $tag, $handle, $href, $media ) {

			$vk_css_tree_shaking_array  = self::css_tree_shaking_array();
			$vk_css_simple_minify_array = self::css_simple_minify_array();

			$exclude_handles = array( 'woocommerce-layout', 'woocommerce-smallscreen', 'woocommerce-general' );

			$options = self::get_css_optimize_options();

			// tree shaking がかかっているものはpreloadから除外する
			// でないと表示時に一瞬崩れて結局実用性に問題があるため.
			foreach ( $vk_css_tree_shaking_array as $vk_css_array ) {
				$exclude_handles[] = $vk_css_array['id'];
			}

			// Simple Minify がかかっているものはpreloadから除外する
			// でないと表示時に一瞬崩れて結局実用性に問題があるため.
			foreach ( $vk_css_simple_minify_array as $vk_css_array ) {
				$exclude_handles[] = $vk_css_array['id'];
			}

			// プリロードから除外するCSSハンドルが option で保存されている場合.
			if ( ! empty( $options['preload_handle_exclude'] ) ) {
				// 保存されているされている除外するCSSハンドルを配列に変換.
				$exclude_array = explode( ',', $options['preload_handle_exclude'] );
				// 除外するCSSハンドルの配列をマージ.
				$exclude_handles = array_merge( $exclude_array, $exclude_handles );
			}

			$exclude_handles = apply_filters( 'vk_css_preload_exclude_handles', $exclude_handles );

			// クリティカルじゃないCSS（tree shakingにかけているもの以外）をpreload .
			if ( ! in_array( $handle, $exclude_handles ) ) {
				// preload を追加する.
				$tag  = "<link rel='preload' id='" . $handle . "-css' href='" . $href . "' as='style' onload=\"this.onload=null;this.rel='stylesheet'\"/>\n";
				$tag .= "<link rel='stylesheet' id='" . $handle . "-css' href='" . $href . "' media='print' onload=\"this.media='all'; this.onload=null;\">\n";
			}

			return $tag;
		}


		/**
		 * Exclude CSS.
		 *
		 * @param string $inidata exclude css class.
		 */
		public static function tree_shaking_exclude( $inidata ) {
			$options = self::get_css_optimize_options();

			$exclude_classes_array = array();

			if ( ! empty( $options['tree_shaking_class_exclude'] ) ) {

				// delete before after space.
				$exclude_clssses = trim( $options['tree_shaking_class_exclude'] );

				// convert tab and br to space.
				$exclude_clssses = preg_replace( '/[\n\r\t]/', '', $exclude_clssses );

				// Change multiple spaces to single space.
				$exclude_clssses       = preg_replace( '/\s/', '', $exclude_clssses );
				$exclude_clssses       = str_replace( '，', ',', $exclude_clssses );
				$exclude_clssses       = str_replace( '、', ',', $exclude_clssses );
				$exclude_classes_array = explode( ',', $exclude_clssses );

			}

			$inidata['class'] = array_merge( $inidata['class'], $exclude_classes_array );

			return $inidata;
		}

	}
	new VK_CSS_Optimize();
}
