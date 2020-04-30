<?php

require get_parent_theme_file_path( '/inc/woocommerce/customize.php' );

function lightning_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'lightning_add_woocommerce_support' );

/*
  Load CSS
/*-------------------------------------------*/
function lightning_woo_css() {
	$editor_style_path = get_template_directory_uri() . '/inc/woocommerce/css/woo.css';
	wp_enqueue_style( 'lightning-woo-style', $editor_style_path, array( 'lightning-common-style' ), LIGHTNING_THEME_VERSION );
	add_editor_style( $editor_style_path );
}
add_action( 'wp_enqueue_scripts', 'lightning_woo_css' );

/*
  WidgetArea initiate
/*-------------------------------------------*/

function lightning_widgets_init_product() {
	$plugin_path = 'woocommerce/woocommerce.php';
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( ! is_plugin_active( $plugin_path ) ) {
		return;
	}

	// Get post type name
	/*-------------------------------------------*/
	$post_type                = 'product';
	$woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id' );
	$label                    = get_the_title( $woocommerce_shop_page_id );
	$sidebar_description      = sprintf( __( 'This widget area appears on the %s contents page only.', 'lightning' ), $label );

	// Set post type widget area
	register_sidebar(
		array(
			'name'          => sprintf( __( 'Sidebar(%s)', 'lightning' ), $label ),
			'id'            => $post_type . '-side-widget-area',
			'description'   => $sidebar_description,
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h1 class="widget-title subSection-title">',
			'after_title'   => '</h1>',
		)
	);

}
add_action( 'widgets_init', 'lightning_widgets_init_product' );

/*
  Print Dynamic css
/*-------------------------------------------*/
$options = get_option( 'lightning_theme_options' );
if ( ! empty( $options['enqueue_point_footer'] ) ) {
	add_action( 'wp_footer', 'lightning_print_css_woo', 20 );
} else {
	add_action( 'wp_head', 'lightning_print_css_woo', 20 );
}
function lightning_print_css_woo() {
	$options     = get_option( 'lightning_theme_options' );
	$dynamic_css = '';

	if ( isset( $options['color_key'] ) && isset( $options['color_key_dark'] ) ) {
		$color_key      = ( ! empty( $options['color_key'] ) ) ? esc_html( $options['color_key'] ) : '#337ab7';
		$color_key_dark = ( ! empty( $options['color_key_dark'] ) ) ? esc_html( $options['color_key_dark'] ) : '#2e6da4';
		$dynamic_css   .= '/* ltg Woo custom */ 
		.woocommerce ul.product_list_widget li a:hover img { border-color:' . $color_key . '; }
		';
	} // if ( isset($options['color_key'] && isset($options['color_key_dark'] ) {

	if ( $dynamic_css ) {
		// delete br
		$dynamic_css = str_replace( PHP_EOL, '', $dynamic_css );
		// delete tab
		$dynamic_css = preg_replace( '/[\n\r\t]/', '', $dynamic_css );
		// multi space convert to single space
		$dynamic_css = preg_replace( '/\s(?=\s)/', '', $dynamic_css );

		// wp_add_inline_style() is not stable on change enquepoint system.
		echo '<style id="lightning-woo-style-custom" type="text/css">' . $dynamic_css . '</style>';
		// wp_add_inline_style( 'lightning-common-style', $dynamic_css );

	}

}
