<?php
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
				'before_title'  => '<h4 class="widget-title sub-section-title">',
				'after_title'   => '</h4>',
			)
		);
		register_sidebar(
			array(
				'name'          => __( 'Sidebar(Common top)', 'lightning' ),
				'id'            => 'common-side-top-widget-area',
				'before_widget' => '<aside class="widget %2$s" id="%1$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h4 class="widget-title sub-section-title">',
				'after_title'   => '</h4>',
			)
		);
		register_sidebar(
			array(
				'name'          => __( 'Sidebar(Common bottom)', 'lightning' ),
				'id'            => 'common-side-bottom-widget-area',
				'before_widget' => '<aside class="widget %2$s" id="%1$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h4 class="widget-title sub-section-title">',
				'after_title'   => '</h4>',
			)
		);
		register_sidebar(
			array(
				'name'          => __( 'Sidebar(Search Result)', 'lightning' ),
				'id'            => 'search-side-widget-area',
				'before_widget' => '<aside class="widget %2$s" id="%1$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h4 class="widget-title sub-section-title">',
				'after_title'   => '</h4>',
			)
		);

		// Sidebar( post_type )

		$postTypes     = get_post_types( array( 'public' => true ) );

		foreach ( $postTypes as $postType ) {

			// Get post type name
			/*-------------------------------------------*/
			$post_type_object = get_post_type_object( $postType );

			if ( $post_type_object ) {
				// Set post type name
				$postType_name = esc_html( $post_type_object->labels->name );

				$sidebar_description = '';
				if ( $postType === 'post' || $postType === 'page' || $postType === 'attachment' ) {

					if ( function_exists( 'Lightning_get_descriptions' ) ){
						$sidebar_description = Lightning_get_descriptions( $postType . '-side-widget-area' );
					}

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
						'before_title'  => '<h4 class="widget-title sub-section-title">',
						'after_title'   => '</h4>',
					)
				);
			} // if($post_type_object){

		} // foreach ($postTypes as $postType) {

		// Site body bottom widget area

		register_sidebar(
			array(
				'name'          => __( 'Widget area of before footer', 'lightning' ),
				'id'            => 'footer-before-widget',
				'before_widget' => '<aside class="widget %2$s" id="%1$s">',
				'after_widget'  => '</aside>',
				'before_title'  => '<h4 class="widget-title sub-section-title">',
				'after_title'   => '</h4>',
			)
		);

		// footer widget area

		$footer_widget_area_count = 3;
		$footer_widget_area_count = apply_filters( 'lightning_footer_widget_area_count', $footer_widget_area_count );

		for ( $i = 1; $i <= $footer_widget_area_count; ) {
			register_sidebar(
				array(
					'name'          => __( 'Footer widget area', 'lightning' ) . ' ' . $i,
					'id'            => 'footer-widget-' . $i,
					'before_widget' => '<aside class="widget %2$s" id="%1$s">',
					'after_widget'  => '</aside>',
					'before_title'  => '<h4 class="widget-title site-footer-title">',
					'after_title'   => '</h4>',
				)
			);
			++$i;
		}
	}
} // if ( ! function_exists( 'lightning_widgets_init' ) ) {
	add_action( 'widgets_init', 'lightning_widgets_init' );
