<?php

$theme_opt = wp_get_theme( get_template() );

define( 'LIGHTNING_THEME_VERSION', $theme_opt->Version );
define( 'LIGHTNING_SHORT_NAME', 'LTG THEME' );
/*
  Theme setup
/*
  Load JS
/*
  Load CSS
/*
  Load Theme Customizer additions.
/*
  Load Custom template tags for this theme.
/*
  Load widgets
/*
  Load designskin manager
/*
  Load tga(Plugin install)
/*
  Load Front PR Blocks
/*
  WidgetArea initiate
/*
  Year Artchive list 'year' and count insert to inner </a>
/*
  Category list 'count insert to inner </a>
/*
  Global navigation add cptions
/*
  headfix enable
/*
  Tag Cloud _ Change font size
/*
  HOME _ Default content hidden
/*
  Move jQuery to footer
/*
  disable_tgm_notification_except_admin
/*
  Add defer first aid
/*-------------------------------------------*/


/*
  Theme setup
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'lightning_theme_setup' );
function lightning_theme_setup() {

	global $content_width;

	/*
	  Title tag
	/*-------------------------------------------*/
	add_theme_support( 'title-tag' );

	/*
	  editor-styles
	/*-------------------------------------------*/
	add_theme_support( 'editor-styles' );

	// When this support that printed front css and it's overwrite skin table style and so on
	// add_theme_support( 'wp-block-styles' );

	add_theme_support( 'align-wide' );

	/*
	  custom-background
	/*-------------------------------------------*/
	add_theme_support( 'custom-background' );

	/*
	  Admin page _ Eye catch
	/*-------------------------------------------*/
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 320, 180, true );

	/*
	  Custom menu
	/*-------------------------------------------*/
	register_nav_menus( array( 'Header' => 'Header Navigation' ) );
	register_nav_menus( array( 'Footer' => 'Footer Navigation' ) );

	load_theme_textdomain( 'lightning', get_template_directory() . '/languages' );

	/*
	  Set content width
	/* 	(Auto set up to media max with.)
	/*-------------------------------------------*/
	if ( ! isset( $content_width ) ) {
		$content_width = 750;
	}

	/*
	  Add theme support for selective refresh for widgets.
	/*-------------------------------------------*/
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	  Feed Links
	/*-------------------------------------------*/
	add_theme_support( 'automatic-feed-links' );

	/*
	  WooCommerce
	/*-------------------------------------------*/
	add_theme_support( 'woocommerce' );

	/*
	  Option init
	/*-------------------------------------------*/
	/*
	Save default option first time.
	When only customize default that, Can't save default value.
	*/
	$theme_options_default = lightning_theme_options_default();
	if ( ! get_option( 'lightning_theme_options' ) ) {
		add_option( 'lightning_theme_options', $theme_options_default );
		$lightning_theme_options = $theme_options_default;
	}

}

/*
  Load JS
/*-------------------------------------------*/

add_action( 'wp_enqueue_scripts', 'lightning_addJs' );
function lightning_addJs() {
	wp_enqueue_script( 'lightning-js', get_template_directory_uri() . '/assets/js/lightning.min.js', array( 'jquery' ), LIGHTNING_THEME_VERSION, true );
}

add_action( 'wp_enqueue_scripts', 'lightning_commentJs' );
function lightning_commentJs() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/*
  Load CSS
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'lightning_load_css_action' );
function lightning_load_css_action() {
	$hook_point = apply_filters( 'lightning_enqueue_point_common_and_theme_css', 'wp_enqueue_scripts' );
	add_action( $hook_point, 'lightning_common_style' );
	add_action( $hook_point, 'lightning_theme_style' );
}

function lightning_common_style() {
	wp_enqueue_style( 'lightning-common-style', get_template_directory_uri() . '/assets/css/common.css', array(), LIGHTNING_THEME_VERSION );
}
function lightning_theme_style() {
	wp_enqueue_style( 'lightning-theme-style', get_stylesheet_uri(), array(), LIGHTNING_THEME_VERSION );
}

/*
  Load Editor CSS
/*-------------------------------------------*/
add_action( 'after_setup_theme', 'lightning_load_common_editor_css' );
function lightning_load_common_editor_css() {
	/*
	 Notice : Use url then if you use local environment https has error that bring to get css error and don't refrected */
	/* Notice : add_editor_style() is only one args. */
	add_editor_style( 'assets/css/common_editor.css' );
}

require get_parent_theme_file_path( '/functions-compatible.php' );


/*
  Load tga(Plugin install)
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/tgm-plugin-activation/tgm-config.php' );

/*
  Load enqueue-point-controller
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/enqueue-point-controller/enqueue-point-controller.php' );

/*
  Load Theme Customizer additions.
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/customize/customize.php' );
require get_parent_theme_file_path( '/inc/customize/customize-design.php' );
require get_parent_theme_file_path( '/inc/customize/customize-top-slide.php' );

/*
  Load allow customize modules
/*-------------------------------------------*/
get_template_part( 'inc/vk-mobile-nav/vk-mobile-nav-config' );

/*
  Load Custom template tags for this theme.
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/template-tags.php' );
require get_parent_theme_file_path( '/inc/template-tags-old.php' );
require get_parent_theme_file_path( '/inc/class-vk-helpers.php' );

/*
  Load modules
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/package-manager.php' );
require get_parent_theme_file_path( '/inc/class-design-manager.php' );
require get_parent_theme_file_path( '/inc/font-awesome/font-awesome-config.php' );
require get_parent_theme_file_path( '/inc/term-color/term-color-config.php' );
require get_parent_theme_file_path( '/inc/vk-components/vk-components-config.php' );
require get_parent_theme_file_path( '/inc/template-redirect.php' );
require get_parent_theme_file_path( '/inc/layout-controller/layout-controller.php' );

/*
  Load woocommerce modules
/*-------------------------------------------*/
if ( class_exists( 'woocommerce' ) ) {
	require get_parent_theme_file_path( '/inc/woocommerce/functions-woo.php' );
}

/*
  WidgetArea initiate
/*-------------------------------------------*/
if ( ! function_exists( 'lightning_widgets_init' ) ) {
	function lightning_widgets_init() {
		// sidebar widget area
		register_sidebar(
			array(
				'name'          => __( 'Sidebar(Home)', 'lightning' ),
				'id'            => 'front-side-top-widget-area',
				'before_widget' => '<aside class="widget %2$s" id="%1$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h1 class="widget-title subSection-title">',
				'after_title'   => '</h1>',
			)
		);
			register_sidebar(
				array(
					'name'          => __( 'Sidebar(Common top)', 'lightning' ),
					'id'            => 'common-side-top-widget-area',
					'before_widget' => '<aside class="widget %2$s" id="%1$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h1 class="widget-title subSection-title">',
					'after_title'   => '</h1>',
				)
			);
			register_sidebar(
				array(
					'name'          => __( 'Sidebar(Common bottom)', 'lightning' ),
					'id'            => 'common-side-bottom-widget-area',
					'before_widget' => '<aside class="widget %2$s" id="%1$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h1 class="widget-title subSection-title">',
					'after_title'   => '</h1>',
				)
			);

		// Sidebar( post_type )

		$postTypes = get_post_types( array( 'public' => true ) );

		foreach ( $postTypes as $postType ) {

			// Get post type name
			/*-------------------------------------------*/
			$post_type_object = get_post_type_object( $postType );
			if ( $post_type_object ) {
				// Set post type name
				$postType_name = esc_html( $post_type_object->labels->name );

				$sidebar_description = '';
				if ( $postType == 'post' ) {

					$sidebar_description = __( 'This widget area appears on the Posts page only. If you do not set any widgets in this area, this theme sets the following widgets "Recent posts", "Category", and "Archive" by default. These default widgets will be hidden, when you set any widgets. <br><br> If you installed our plugin VK All in One Expansion Unit (Free), you can use the following widgets, "VK_Recent posts",  "VK_Categories", and  "VK_archive list".', 'lightning' );

				} elseif ( $postType == 'page' ) {

					$sidebar_description = __( 'This widget area appears on the Pages page only. If you do not set any widgets in this area, this theme sets the "Child pages list widget" by default. This default widget will be hidden, when you set any widgets. <br><br> If you installed our plugin VK All in One Expansion Unit (Free), you can use the "VK_ child page list" widget for the alternative.', 'lightning' );

				} elseif ( $postType == 'attachment' ) {

					$sidebar_description = __( 'This widget area appears on the Media page only.', 'lightning' );

				} else {

					$sidebar_description = sprintf( __( 'This widget area appears on the %s contents page only.', 'lightning' ), $postType_name );

				}

				// Set post type widget area
				register_sidebar(
					array(
						'name'          => sprintf( __( 'Sidebar(%s)', 'lightning' ), $postType_name ),
						'id'            => $postType . '-side-widget-area',
						'description'   => $sidebar_description,
						'before_widget' => '<aside class="widget %2$s" id="%1$s">',
						'after_widget'  => '</aside>',
						'before_title'  => '<h1 class="widget-title subSection-title">',
						'after_title'   => '</h1>',
					)
				);
			} // if($post_type_object){

		} // foreach ($postTypes as $postType) {

		// Home content top widget area

			register_sidebar(
				array(
					'name'          => __( 'Home content top', 'lightning' ),
					'id'            => 'home-content-top-widget-area',
					'before_widget' => '<div class="widget %2$s" id="%1$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h2 class="mainSection-title">',
					'after_title'   => '</h2>',
				)
			);

		// footer upper widget area

			register_sidebar(
				array(
					'name'          => __( 'Widget area of upper footer', 'lightning' ),
					'id'            => 'footer-upper-widget-1',
					'before_widget' => '<aside class="widget %2$s" id="%1$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h1 class="widget-title subSection-title">',
					'after_title'   => '</h1>',
				)
			);

		// footer widget area

			$footer_widget_area_count = 3;
			$footer_widget_area_count = apply_filters( 'lightning_footer_widget_area_count', $footer_widget_area_count );

		for ( $i = 1; $i <= $footer_widget_area_count; ) {
			register_sidebar(
				array(
					'name'          => __( 'Footer widget area ', 'lightning' ) . $i,
					'id'            => 'footer-widget-' . $i,
					'before_widget' => '<aside class="widget %2$s" id="%1$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h1 class="widget-title subSection-title">',
					'after_title'   => '</h1>',
				)
			);
			$i++;
		}

		// LP widget area

			$args  = array(
				'post_type'      => 'page',
				'post_status'    => 'publish,private,draft',
				'posts_per_page' => -1,
				'meta_key'       => '_wp_page_template',
				'meta_value'     => 'page-lp.php',
			);
			$posts = get_posts( $args );

			if ( $posts ) {
				foreach ( $posts as $key => $post ) {
					register_sidebar(
						array(
							/* Translators: %s: LP title */
							'name'          => sprintf( __( 'LP widget "%s"', 'lightning' ), esc_html( $post->post_title ) ),
							'id'            => 'lp-widget-' . $post->ID,
							'before_widget' => '<div class="widget %2$s" id="%1$s">',
							'after_widget'  => '</div>',
							'before_title'  => '<h2 class="mainSection-title">',
							'after_title'   => '</h2>',
						)
					);
				}
			}
			wp_reset_postdata();
	}
} // if ( ! function_exists( 'lightning_widgets_init' ) ) {
add_action( 'widgets_init', 'lightning_widgets_init' );

/*
  Year Artchive list 'year' and count insert to inner </a>
/*-------------------------------------------*/
function lightning_archives_link( $html ) {
	return preg_replace( '@</a>(.+?)</li>@', '\1</a></li>', $html );
}
add_filter( 'get_archives_link', 'lightning_archives_link' );

/*
  Category list count insert to inner </a>
/*-------------------------------------------*/
function lightning_list_categories( $output, $args ) {
	$output = preg_replace( '/<\/a>\s*\((\d+)\)/', ' ($1)</a>', $output );
	return $output;
}
add_filter( 'wp_list_categories', 'lightning_list_categories', 10, 2 );

/*
  Global navigation add cptions
/*-------------------------------------------*/
class description_walker extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';
		$classes     = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		$output     .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

		$prepend     = '<strong class="gMenu_name">';
		$append      = '</strong>';
		$description = ! empty( $item->description ) ? '<span class="gMenu_description">' . esc_attr( $item->description ) . '</span>' : '';

		if ( $depth != 0 ) {
			$description = $append = $prepend = '';
		}

		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . $prepend . apply_filters( 'the_title', $item->title, $item->ID ) . $append;
		$item_output .= $description . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}

/*
  headfix enable
/*-------------------------------------------*/
add_filter( 'body_class', 'lightning_body_class' );
function lightning_body_class( $class ) {
	// header fix
	if ( apply_filters( 'lightning_headfix_enable', true ) ) {
		$class[] = 'headfix';
	}
	// header height changer
	if ( apply_filters( 'lightning_header_height_changer_enable', true ) ) {
		$class[] = 'header_height_changer';
	}
	return $class;
}

// lightning headfix disabel sample
/*
add_filter( 'lightning_headfix_enable', 'lightning_headfix_disabel');
function lightning_headfix_disabel(){
	return false;
}
*/

// lightning header height changer disabel sample
/*
add_filter( 'lightning_header_height_changer_enable', 'lightning_header_height_changer_disabel');
function lightning_header_height_changer_disabel(){
	return false;
}
*/

/*
  Tag Cloud _ Change font size
/*-------------------------------------------*/
function lightning_tag_cloud_filter( $args ) {
	$args['smallest'] = 10;
	$args['largest']  = 10;
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'lightning_tag_cloud_filter' );

/*
  HOME _ Default content hidden
/*-------------------------------------------*/
add_filter( 'is_lightning_home_content_display', 'lightning_home_content_hidden' );
function lightning_home_content_hidden( $flag ) {
	global $lightning_theme_options;
	if ( isset( $lightning_theme_options['top_default_content_hidden'] ) && $lightning_theme_options['top_default_content_hidden'] ) {
		$flag = false;
	}
	return $flag;
}

/*
  disable_tgm_notification_except_admin
/*-------------------------------------------*/
add_action( 'admin_head', 'lightning_disable_tgm_notification_except_admin' );
function lightning_disable_tgm_notification_except_admin() {
	if ( ! current_user_can( 'administrator' ) ) {
		$allowed_html = array(
			'style' => array( 'type' => array() ),
		);
		$text         = '<style>#setting-error-tgmpa { display:none; }</style>';
		echo wp_kses( $text, $allowed_html );
	}
}

/*
  Add defer first aid
// function lightning_add_defer_to_scripts( $tag, $handle ) {
// if ( ! preg_match( '/\b(async|defer)\b/', $tag ) ) {
// return str_replace( ' src', ' defer src', $tag );
// }
// return $tag;
// }
//
// if ( ! is_admin() ) {
// add_filter( 'script_loader_tag', 'lightning_add_defer_to_scripts', 10, 2 );
// }

*/


/*
-------------------------------------------*/
/*
  embed card
/*-------------------------------------------*/

  remove_action( 'embed_footer', 'print_embed_sharing_dialog' );

function lightning_embed_styles() {
	wp_enqueue_style( 'wp-oembed-embed', get_template_directory_uri() . '/assets/css/wp-embed.css' );
}
  add_action( 'embed_head', 'lightning_embed_styles' );
