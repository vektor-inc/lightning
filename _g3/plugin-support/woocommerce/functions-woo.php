<?php
/**
 * WooCommerce support file
 *
 * @package vektor-inc/lightning
 */

/**
 * WooCommerce support
 *
 * @return void
 */
function lightning_add_woocommerce_support() {
	add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'lightning_add_woocommerce_support' );

require_once dirname( __FILE__ ) . '/customize.php';

/**
 * Load WooCommerce CSS
 *
 * @return void
 */
function lightning_woo_css() {
	wp_enqueue_style( 'lightning-woo-style', get_template_directory_uri() . '/plugin-support/woocommerce/css/woo.css', array( 'lightning-common-style' ), LIGHTNING_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'lightning_woo_css' );

/**
 * Load editor css
 *
 * @return void
 */
function lightning_add_woocommerce_css_to_editor() {
	add_editor_style( '/_g3/plugin-support/woocommerce/css/woo.css' );
}
add_action( 'after_setup_theme', 'lightning_add_woocommerce_css_to_editor' );

/**
 * Adding support for WooCommerce 3.0’s new gallery feature
 *
 * Reffer https://woocommerce.wordpress.com/2017/02/28/adding-support-for-woocommerce-2-7s-new-gallery-feature-to-your-theme/
 *
 * @return void
 */
function lightning_woo_product_gallery_setup() {
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );
}
add_action( 'after_setup_theme', 'lightning_woo_product_gallery_setup' );


/**
 * Lightning_widgets_init_product
 * WidgetArea initiate
 *
 * WooCommerce post type (product) is not public?
 * Product widget area does not create automatically by Lightning that make manually by this function
 *
 * @return void
 */
function lightning_widgets_init_product() {
	$plugin_path = 'woocommerce/woocommerce.php';
	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	if ( ! is_plugin_active( $plugin_path ) ) {
		return;
	}

	// Get post type name.
	$post_type                = 'product';
	$woocommerce_shop_page_id = get_option( 'woocommerce_shop_page_id' );
	$label                    = get_the_title( $woocommerce_shop_page_id );
	// translators: %s post type .
	$sidebar_description = sprintf( __( 'This widget area appears on the %s contents page only.', 'lightning' ), $label );

	// Set post type widget area.
	register_sidebar(
		array(
			// translators: %s post type .
			'name'          => sprintf( __( 'Sidebar(%s)', 'lightning' ), $label ),
			'id'            => $post_type . '-side-widget-area',
			'description'   => $sidebar_description,
			'before_widget' => '<aside class="widget %2$s" id="%1$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h4 class="widget-title sub-section-title">',
			'after_title'   => '</h4>',
		)
	);

}
add_action( 'widgets_init', 'lightning_widgets_init_product' );
