<?php
/**
 * Lightning G3 functions
 *
 * @package vektor-inc/lightning
 */

$theme_opt = wp_get_theme( get_template() );

define( 'LIGHTNING_THEME_VERSION', $theme_opt->Version ); // phpcs:ignore

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

/*********************************************
 * Set up theme
 */
function lightning_theme_setup() {

	add_theme_support( 'title-tag' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'woocommerce' );
	// theme.json deactive support //////////////////////////////////.
	add_theme_support( 'link-color' );
	add_theme_support( 'border' );

	// When this support that printed front css and it's overwrite skin table style and so on
	// add_theme_support( 'wp-block-styles' );.

	$args = array(
		'default-color' => '#ffffff',
	);
	add_theme_support( 'custom-background', $args );

	// Block Editor line height @since WordPress 5.5.
	add_theme_support( 'custom-line-height' );
	// Block Editor custom unit @since WordPress 5.5.
	add_theme_support(
		'custom-units',
		'px',
		'%',
		'vw',
		'vh',
		'em',
		'rem',
		'svw',
		'lvw',
		'dvw',
		'svh',
		'lvh',
		'dvh',
		'svi',
		'lvi',
		'dvi',
		'svb',
		'lvb',
		'dvb',
		'vmin',
		'svmin',
		'lvmin',
		'dvmin',
		'vmax',
		'svmax',
		'lvmax',
		'dvmax'
	);
	// Block Editor custom unit @since WordPress 5.8.
	add_theme_support( 'custom-spacing' );

	add_theme_support(
		'editor-font-sizes',
		array(
			array(
				'name' => esc_attr__( 'Small', 'lightning' ),
				'size' => 14,
				'slug' => 'small',
			),
			array(
				'name' => esc_attr__( 'Regular', 'lightning' ),
				'size' => 16,
				'slug' => 'regular',
			),
			array(
				'name' => esc_attr__( 'Large', 'lightning' ),
				'size' => 24,
				'slug' => 'large',
			),
			array(
				'name' => esc_attr__( 'Huge', 'lightning' ),
				'size' => 36,
				'slug' => 'huge',
			),
		)
	);

	add_theme_support(
		'editor-gradient-presets',
		array(
			array(
				'name'     => esc_attr__( 'Vivid cyan blue to vivid purple', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgba(6,147,227,1) 0%,rgb(155,81,224) 100%)',
				'slug'     => 'vivid-cyan-blue-to-vivid-purple',
			),
			array(
				'name'     => esc_attr__( 'Vivid green cyan to vivid cyan blue', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgba(0,208,132,1) 0%,rgba(6,147,227,1) 100%)',
				'slug'     => 'vivid-green-cyan-to-vivid-cyan-blue',
			),
			array(
				'name'     => esc_attr__( 'Light green cyan to vivid green cyan', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgb(122,220,180) 0%,rgb(0,208,130) 100%)',
				'slug'     => 'light-green-cyan-to-vivid-green-cyan',
			),
			array(
				'name'     => esc_attr__( 'Luminous vivid amber to luminous vivid orange', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgba(252,185,0,1) 0%,rgba(255,105,0,1) 100%)',
				'slug'     => 'luminous-vivid-amber-to-luminous-vivid-orange',
			),
			array(
				'name'     => esc_attr__( 'Luminous vivid orange to vivid red', 'lightning' ),
				'gradient' => 'linear-gradient(135deg,rgba(255,105,0,1) 0%,rgb(207,46,46) 100%)',
				'slug'     => 'luminous-vivid-orange-to-vivid-red',
			),
		)
	);

	set_post_thumbnail_size( 320, 180, true );
	add_post_type_support( 'page', 'excerpt' );

	// Custom menu.
	register_nav_menus( array( 'global-nav' => 'Header Navigation' ) );
	register_nav_menus( array( 'footer-nav' => 'Footer Navigation' ) );

	// Set content width(Auto set up to media max with.).
	global $content_width;
	if ( ! isset( $content_width ) ) {
		$content_width = 1140;
	}
	load_theme_textdomain( 'lightning', get_template_directory() . '/languages' );

	if ( is_customize_preview() ) {
		require __DIR__ . '/inc/starter-content.php';
		add_theme_support( 'starter-content', lightning_add_starter_content() );
	}
}
add_action( 'after_setup_theme', 'lightning_theme_setup' );

require __DIR__ . '/inc/vk-helpers/config.php';
require __DIR__ . '/inc/class-lightning-design-manager.php';
require __DIR__ . '/inc/class-vk-description-walker.php';
require __DIR__ . '/inc/template-tags.php';
require __DIR__ . '/inc/customize/customize-design.php';
require __DIR__ . '/inc/vk-color-palette-manager/config.php';
require __DIR__ . '/inc/layout-controller/layout-controller.php';
require __DIR__ . '/inc/vk-mobile-nav/config.php';
require __DIR__ . '/inc/widget-area.php';
require __DIR__ . '/inc/term-color/config.php';
require __DIR__ . '/inc/vk-component/config.php';
require __DIR__ . '/inc/vk-css-optimize/config.php';
require __DIR__ . '/inc/vk-swiper/config.php';
require __DIR__ . '/inc/ltg-g3-slider/config.php';
require __DIR__ . '/inc/vk-wp-oembed-blog-card/config.php';
require __DIR__ . '/inc/vk-breadcrumb/config.php'; // fall back alias.


/*********************************************
 * Load CSS
 */
function lightning_load_css_action() {
	add_action( 'wp_enqueue_scripts', 'lightning_common_style' );
	add_action( 'wp_enqueue_scripts', 'lightning_theme_style' );
}
add_action( 'after_setup_theme', 'lightning_load_css_action' );

/**
 * Load theme common style
 *
 * @return void
 */
function lightning_common_style() {
	$options = get_option( 'lightning_theme_options' );
	if ( ! $options || ( ! empty( $options['theme_json'] ) ) ) {
		// theme_json = true.
		$style = get_template_directory_uri() . '/assets/css/style-theme-json.css';
	} else {
		// theme_json = false.
		$style = get_template_directory_uri() . '/assets/css/style.css';
	}
	wp_enqueue_style( 'lightning-common-style', $style, array(), LIGHTNING_THEME_VERSION );
}

/**
 * Load theme style
 *
 * @return void
 */
function lightning_theme_style() {
	wp_enqueue_style( 'lightning-theme-style', get_stylesheet_uri(), array(), LIGHTNING_THEME_VERSION );
}

/*********************************************
 * Load Editor CSS
 */
function lightning_load_common_editor_css() {
	// Notice : Use url then if you use local environment https has error that bring to get css error and don't refrected.
	// Notice : add_editor_style() is only one args.
	// add_editor_style is for Classic Editor Only.
	global $post;
	if ( ! function_exists( 'use_block_editor_for_post' ) || ! use_block_editor_for_post( $post ) ) {
		add_editor_style( LIG_G3_DIR . '/assets/css/editor.css' );
	}
}
add_action( 'admin_enqueue_scripts', 'lightning_load_common_editor_css' );

/**
 * Already add_editor_style() is used but reload css by wp_enqueue_style() reason is use to wp_add_inline_style()
 *
 * @return void
 */
function lightning_load_common_editor_css_to_gutenberg() {
	wp_enqueue_style(
		'lightning-common-editor-gutenberg',
		// If not full path that can't load in editor screen.
		get_template_directory_uri() . '/assets/css/editor.css',
		array( 'wp-edit-blocks' ),
		LIGHTNING_THEME_VERSION
	);

	/**
	 * Add widget editor style
	 *
	 * @since 15.2.2
	 */
	// カスタマイズ画面でのみ読み込む.ウィジェットエリアが狭いので content-width を補正する
	// Read only on the customization screen.Widget area is narrow, so adjust content-width
	if ( is_customize_preview() ) {
		wp_enqueue_style(
			'lightning-common-editor-widget',
			get_template_directory_uri() . '/assets/css/editor-widget.css',
			array( 'wp-edit-blocks' ),
			LIGHTNING_THEME_VERSION
		);
	}
}
add_action( 'enqueue_block_editor_assets', 'lightning_load_common_editor_css_to_gutenberg' );

/**
 * Load JavaScript
 *
 * @return void
 */
function lightning_add_script() {
	if ( filter_input( INPUT_GET, 'legacy-widget-preview', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY ) ) {
		return;
	}
	global $pagenow;
	if ( 'widgets.php' === $pagenow ) {
		return;
	}
	if ( 'index.php' === $pagenow && false !== strpos( $_SERVER['REQUEST_URI'], 'rest_route' ) ) {
		return;
	}

	wp_register_script( 'lightning-js', get_template_directory_uri() . '/assets/js/main.js', array(), LIGHTNING_THEME_VERSION, true );
	wp_localize_script( 'lightning-js', 'lightningOpt', apply_filters( 'lightning_localize_options', array() ) );
	wp_enqueue_script( 'lightning-js' );
}
add_action( 'wp_enqueue_scripts', 'lightning_add_script' );

/**
 * Fix global menu
 *
 * @param array $options : cast to js parametor.
 * @return array $options
 */
function lightning_global_nav_fix( $options ) {
	$options['header_scrool']            = true;
	$options['add_header_offset_margin'] = true;
	return $options;
}
add_filter( 'lightning_localize_options', 'lightning_global_nav_fix', 10, 1 );

/**
 * Adjustment page link scroll position for admin bar
 * ページ内リンクの管理バー分補正
 *
 * @return void
 */
function lightning_add_scroll_adjustment_css() {
	$dynamic_css = 'html{scroll-padding-top:var(--vk-size-admin-bar);}';
	wp_add_inline_style( 'lightning-common-style', $dynamic_css );
}
add_action( 'wp_enqueue_scripts', 'lightning_add_scroll_adjustment_css', 11 );

/**
 * ページ内リンクの場合は識別クラスを出力
 * Add class to anchor link
 *
 * @since 15.10.0
 *
 * @param array  $classes : class list.
 * @param object $item : menu item.
 * @return array $classes : class list.
 */
function lightning_add_anchor_nav_class( $classes, $item ) {
	if ( strpos( $item->url, '#' ) !== false ) {
		$classes[] = 'menu-item-anchor';
	}
	return $classes;
}
add_filter( 'nav_menu_css_class', 'lightning_add_anchor_nav_class', 10, 2 );

/**
 * Enqueue comment reply
 *
 * @return void
 */
function lightning_comment_js() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'lightning_comment_js' );

/**
 * Year Artchive list 'year' and count insert to inner </a>
 *
 * @param string $html link html.
 * @return string $html added string html
 */
function lightning_archives_link( $html ) {
	return preg_replace( '@</a>(.+?)</li>@', '\1</a></li>', $html );
}
add_filter( 'get_archives_link', 'lightning_archives_link' );

/**
 * Category list count insert to inner </a>
 *
 * @param string $output : output html.
 * @param array  $args : list categories args.
 * @return string $output : return string
 */
function lightning_list_categories( $output, $args ) {
	$output = preg_replace( '/<\/a>\s*\((\d+)\)/', ' ($1)</a>', $output );
	return $output;
}
add_filter( 'wp_list_categories', 'lightning_list_categories', 10, 2 );

// ↓ 何を意図して remove_action() したのか不明なので一旦コメントアウト
// remove_action( 'embed_footer', 'print_embed_sharing_dialog' );

/**
 * Load embed card css
 *
 * @return void
 */
function lightning_embed_styles() {
	wp_enqueue_style( 'wp-oembed-embed', get_template_directory_uri() . '/assets/css/wp-embed.css', array(), LIGHTNING_THEME_VERSION );
}
add_action( 'embed_head', 'lightning_embed_styles' );

/******************************************
 * Plugin support
 */
// Load woocommerce modules.
if ( class_exists( 'woocommerce' ) ) {
	require __DIR__ . '/plugin-support/woocommerce/functions-woo.php';
}
// Load polylang modules.
require_once ABSPATH . 'wp-admin/includes/plugin.php';
if ( is_plugin_active( 'polylang/polylang.php' ) ) {
	require __DIR__ . '/plugin-support/polylang/functions-polylang.php';
}
if ( is_plugin_active( 'bbpress/bbpress.php' ) ) {
	require __DIR__ . '/plugin-support/bbpress/functions-bbpress.php';
}
// Load the-events-calendar modules.
if ( is_plugin_active( 'the-events-calendar/the-events-calendar.php' ) ) {
	// ※ If File name change to "functions-the-event-calendar.php" that fail to load.
	require __DIR__ . '/plugin-support/the-events-calendar/functions-events-calendar.php';
}
if ( is_plugin_active( 'booking-package/index.php' ) ) {
	require __DIR__ . '/plugin-support/booking-package/functions-booking-package.php';
}

/**
 * Disable_tgm_notification_except_admin
 *
 * @return void
 */
function lightning_disable_tgm_notification_except_admin() {
	if ( ! current_user_can( 'administrator' ) ) {
		$allowed_html = array(
			'style' => array( 'type' => array() ),
		);
		$text         = '<style>#setting-error-tgmpa { display:none; }</style>';
		echo wp_kses( $text, $allowed_html );
	}
}
add_action( 'admin_head', 'lightning_disable_tgm_notification_except_admin' );

/**
 * Cope with wide and full width in inner block
 * theme.json があってもインナーブロックで幅広か全幅が使えるようにするための処理
 * また、これがないと編集画面でブロック要素の左右に margin:auto !important をつけられてしまう
 */
add_filter(
	'block_editor_settings_all',
	function ( $editor_settings ) {
		$editor_settings['supportsLayout'] = false;
		return $editor_settings;
	}
);

/**
 * Get Descriptions
 * 同じ説明分を複数箇所で使うので関数化
 *
 * @since 15.18.0
 * @param string $target : target name.
 * @return array $descriptions
 */
function Lightning_get_descriptions( $target = '' ) {

	$descriptions = array(
		'post-side-widget-area'       => __( 'This widget area appears on the Posts page only. If you do not set any widgets in this area, this theme sets the following widgets "Recent posts", "Category", and "Archive" by default. These default widgets will be hidden, when you set any widgets. If you installed our plugin VK All in One Expansion Unit (Free), you can use the following widgets, "VK_Recent posts",  "VK_Categories", and  "VK_archive list".', 'lightning' ),
		'page-side-widget-area'       => __( 'This widget area appears on the Pages page only. If you do not set any widgets in this area, this theme sets the "Child pages list widget" by default. This default widget will be hidden, when you set any widgets. <br><br> If you installed our plugin VK All in One Expansion Unit (Free), you can use the "VK_ child page list" widget for the alternative.', 'lightning' ),
		'attachment-side-widget-area' => __( 'This widget area appears on the Media page only.', 'lightning' ),
	);

	return $descriptions[ $target ];
}

/**
 * Alert message for customize widget area
 * カスタマイズ画面でのみ表示するウィジェットエリアの説明文
 *
 * @since 15.18.0
 * @param string $post_type : post type.
 * @return void
 */
function Lightning_customize_widget_area_alert( $post_type = 'post' ) {
	if ( is_customize_preview() ) {
		$sidebar_description = Lightning_get_descriptions( $post_type . '-side-widget-area' );
		$return              = '<div class="alert alert-warning widget-area-description">';
		$return             .= '<p class="mb-2">' . $sidebar_description . '</p>';
		$return             .= '<p>* ' . __( 'This message is displayed only on the customization screen. It will not be displayed on the general public screen.', 'lightning' ) . '</p>';
		$return             .= '</div>';
		echo wp_kses_post( $return );
	}
}
