<?php

$theme_opt = wp_get_theme( get_template() );

define( 'LIGHTNING_THEME_VERSION', $theme_opt->Version );
define( 'LIGHTNING_SHORT_NAME', 'LTG THEME' );
/*-------------------------------------------*/
/*	Theme setup
/*-------------------------------------------*/
/*	Load JS
/*-------------------------------------------*/
/*	Load CSS
/*-------------------------------------------*/
/*	Load Theme Customizer additions.
/*-------------------------------------------*/
/*	Load Custom template tags for this theme.
/*-------------------------------------------*/
/*	Load designskin manager
/*-------------------------------------------*/
/*	Load tga(Plugin install)
/*-------------------------------------------*/
/*	Load Front PR Blocks
/*-------------------------------------------*/

/*-------------------------------------------*/
/*	WidgetArea initiate
/*-------------------------------------------*/
/*	Year Artchive list 'year' and count insert to inner </a>
/*-------------------------------------------*/
/*	Category list 'count insert to inner </a>
/*-------------------------------------------*/
/*	Global navigation add cptions
/*-------------------------------------------*/
/*	headfix enable
/*-------------------------------------------*/
/*	Tag Cloud _ Change font size
/*-------------------------------------------*/
/*	HOME _ Default content hidden
/*-------------------------------------------*/
/*-------------------------------------------*/
/*  Remove lightning-advanced-unit's function.
/*-------------------------------------------*/

/*-------------------------------------------*/
/*	Theme setup
/*-------------------------------------------*/

add_action( 'after_setup_theme', 'lightning_theme_setup' );
function lightning_theme_setup() {

	global $content_width;

	/*-------------------------------------------*/
	/*  Title tag
	/*-------------------------------------------*/
	add_theme_support( 'title-tag' );

	/*-------------------------------------------*/
	/*	Admin page _ Eye catch
	/*-------------------------------------------*/
	add_theme_support( 'post-thumbnails' );
	set_post_thumbnail_size( 320, 180, true );

	/*-------------------------------------------*/
	/*	Custom menu
	/*-------------------------------------------*/
	register_nav_menus( array( 'Header' => 'Header Navigation' ) );
	register_nav_menus( array( 'Footer' => 'Footer Navigation' ) );

	load_theme_textdomain( 'lightning', get_template_directory() . '/languages' );

	/*-------------------------------------------*/
	/*	Set content width
	/* 	(Auto set up to media max with.)
	/*-------------------------------------------*/
	if ( ! isset( $content_width ) ) {
		$content_width = 750;
	}

	/*-------------------------------------------*/
	/*	Add theme support for selective refresh for widgets.
	/*-------------------------------------------*/
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*-------------------------------------------*/
	/*	Admin page _ Add editor css
	/*-------------------------------------------*/
	if ( ! apply_filters( 'lightning-disable-theme_style', false ) ) {
		add_editor_style( 'design_skin/origin/css/editor.css' );
	}

	/*-------------------------------------------*/
	/*	Feed Links
	/*-------------------------------------------*/
	add_theme_support( 'automatic-feed-links' );

	/*-------------------------------------------*/
	/*	Option init
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

/*-------------------------------------------*/
/*	Load JS
/*-------------------------------------------*/

add_action( 'wp_enqueue_scripts', 'lightning_addJs' );
function lightning_addJs() {
	wp_enqueue_script( 'html5shiv', '//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js', [],false,true );
	wp_script_add_data( 'html5shiv', 'conditional', 'lt IE 9' );
	wp_enqueue_script( 'respond', '//oss.maxcdn.com/respond/1.4.2/respond.min.js', [],false,true );
	wp_script_add_data( 'respond', 'conditional', 'lt IE 9' );
	wp_enqueue_script( 'lightning-js', get_template_directory_uri() . '/js/lightning.min.js', array( 'jquery' ), LIGHTNING_THEME_VERSION,true );
}


add_action( 'wp_enqueue_scripts', 'lightning_commentJs' );
function lightning_commentJs() {
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_enqueue_scripts', 'lightning_unit_script', 100 );
function lightning_unit_script() {
	wp_register_script( 'lightning_unit_script', get_template_directory_uri() . '/js/sidebar-fix.js', array( 'jquery', 'lightning-js' ), LIGHTNING_THEME_VERSION );
	wp_enqueue_script( 'lightning_unit_script' );
}

/*-------------------------------------------*/
/*	Load CSS
/*-------------------------------------------*/
add_action( 'wp_enqueue_scripts', 'lightning_css' );
function lightning_css() {
	wp_enqueue_style( 'lightning-theme-style', get_stylesheet_uri(), array( 'lightning-design-style' ), LIGHTNING_THEME_VERSION );
}

/*-------------------------------------------*/
/*	Load Theme Customizer additions.
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/customizer.php' );
require get_parent_theme_file_path( '/inc/sidebar-position.php' );
require get_parent_theme_file_path( '/inc/sidebar-child-list-hidden.php' );
require get_parent_theme_file_path( '/inc/sidebar-fix.php' );
require get_parent_theme_file_path( 'inc/widgets/widget-full-wide-title.php' );
require get_parent_theme_file_path( 'inc/widgets/widget-new-posts.php' );

/*-------------------------------------------*/
/*	Load Custom template tags for this theme.
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/template-tags.php' );

/*-------------------------------------------*/
/*	Load designskin manager
/*-------------------------------------------*/

function lightning_is_new_skin() {
	$skin_current = get_option( 'lightning_design_skin' );
	if ( $skin_current == 'origin' || $skin_current == '' ) {
		// New Skin System
		return true;
	} else {
		$old_skin_system_functions_url = WP_PLUGIN_DIR . '/lightning-skin-' . $skin_current . '/old-functions/old-skin-system-functions.php';
		if ( file_exists( $old_skin_system_functions_url ) ) {
			// New Skin System
			return true;
		} else {
			// Old Skin System
			return false;
		}
	}
}

if ( lightning_is_new_skin() ) {
	require get_parent_theme_file_path( '/inc/class-design-manager.php' );
} else {
	require get_parent_theme_file_path( '/inc/class-design-manager-old.php' );
}


/*-------------------------------------------*/
/*	Load tga(Plugin install)
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/tgm-plugin-activation/tgm-config.php' );

/*-------------------------------------------*/
/*	Load Font Awesome
/*-------------------------------------------*/
require get_parent_theme_file_path( '/inc/font-awesome-config.php' );

/*-------------------------------------------*/
/*	Load Front PR Blocks
/*-------------------------------------------*/
get_template_part( 'inc/front-page-pr' );

/*-------------------------------------------*/
/*	WidgetArea initiate
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
				if ($postType == 'post'){

					$sidebar_description_text = 'This widget area appears on the Posts page only. If you don’t set any widgets in this area, this theme sets the following widgets "Recent posts”, “Category”, and “Archive” by default. These default widgets will be hidden, when you set any widgets. <br><br> If you installed our plugin VK All in One Expansion Unit (Free), you can use the following widgets, "VK_Recent posts”,  “VK_Categories”, and  “VK_archive list”.';
					$sidebar_description = __( $sidebar_description_text, 'lightning');

				}elseif ($postType == 'page'){

					$sidebar_description_text = 'This widget area appears on the Pages page only. If you don’t set any widgets in this area, this theme sets the “Child pages list widget” by default. This default widget will be hidden, when you set any widgets. <br><br> If you installed our plugin VK All in One Expansion Unit (Free), you can use the "VK_ child page list” widget for the alternative.';
					$sidebar_description = __( $sidebar_description_text, 'lightning');

				}elseif ($postType == 'attachment'){

					$sidebar_description_text = 'This widget area appears on the Media page only.';
					$sidebar_description = __( $sidebar_description_text, 'lightning');

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

/*-------------------------------------------*/
/*	Year Artchive list 'year' and count insert to inner </a>
/*-------------------------------------------*/
function lightning_archives_link( $html ) {
	return preg_replace( '@</a>(.+?)</li>@', '\1</a></li>', $html );
}
add_filter( 'get_archives_link', 'lightning_archives_link' );

/*-------------------------------------------*/
/*	Category list count insert to inner </a>
/*-------------------------------------------*/
function lightning_list_categories( $output, $args ) {
	$output = preg_replace( '/<\/a>\s*\((\d+)\)/', ' ($1)</a>', $output );
	return $output;
}
add_filter( 'wp_list_categories', 'lightning_list_categories', 10, 2 );

/*-------------------------------------------*/
/*	Global navigation add cptions
/*-------------------------------------------*/
class description_walker extends Walker_Nav_Menu {
	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;

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

/*-------------------------------------------*/
/*	headfix enable
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

/*-------------------------------------------
/*	Tag Cloud _ Change font size
/*-------------------------------------------*/
function lightning_tag_cloud_filter( $args ) {
	$args['smallest'] = 10;
	$args['largest']  = 10;
	return $args;
}
add_filter( 'widget_tag_cloud_args', 'lightning_tag_cloud_filter' );

/*-------------------------------------------*/
/*	HOME _ Default content hidden
/*-------------------------------------------*/
add_filter( 'is_lightning_home_content_display', 'lightning_home_content_hidden' );
function lightning_home_content_hidden( $flag ) {
	global $lightning_theme_options;
	if ( isset( $lightning_theme_options['top_default_content_hidden'] ) && $lightning_theme_options['top_default_content_hidden'] ) {
		$flag = false;
	}
	return $flag;
}

/*-------------------------------------------*/
/*  Remove lightning-advanced-unit's function.
/*-------------------------------------------*/
$if_existed_in_plugins = array(
	'customize_register' => 'lightning_adv_unit_customize_register_sidebar_position',
	'customize_register' => 'lightning_adv_unit_customize_register_sidebar_child_list_hidden',
	'wp_head' => 'lightning_adv_unit_sidebar_position_custom',
	'wp_head' => 'lightning_adv_unit_sidebar_child_list_hidden_css',
	'widgets_init' => 'lightning_adv_unit_widget_register_full_wide_title',
	'widgets_init' => 'lightning_adv_unit_widget_register_post_list',

);
foreach ($if_existed_in_plugins as $key => $val){
	$priority = has_filter( $key, $val );
	if ( $priority ){
		remove_filter( $key, $val, $priority );
		remove_action( $key, $val, $priority);
	}
}

/*-------------------------------------------*/
/*  Move jQuery to footer
/*-------------------------------------------*/
add_action( 'init', 'lightning_move_jquery_to_footer' );
function lightning_move_jquery_to_footer() {
	if ( is_admin() || lightning_is_login_page() ) {
		return;
	}

	global $wp_scripts;
	$jquery = $wp_scripts->registered['jquery-core'];
	$jquery_ver = $jquery->ver;
	$jquery_src = $jquery->src;

	wp_deregister_script( 'jquery' );
	wp_deregister_script( 'jquery-core' );

	wp_register_script( 'jquery', false, ['jquery-core'], $jquery_ver, true );
	wp_register_script( 'jquery-core', $jquery_src, [], $jquery_ver, true );
}

function lightning_is_login_page() {
	return in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php'));
}