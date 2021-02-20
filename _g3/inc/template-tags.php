<?php

function lightning_get_template_part( $slug, $name = null, $args = array() ) {
    $current_skin = get_option( 'lightning_design_skin' );
    if ( $current_skin !== 'origin3' ){
        get_template_part( $slug, $name, $args );
    } else {
        /* Almost the same as the core */
        $templates = array();
        $name      = (string) $name;
        if ( '' !== $name ) {
            $templates[] = LIG_G3_DIR . '/' . "{$slug}-{$name}.php";
        }
     
        $templates[] = LIG_G3_DIR . '/' . "{$slug}.php";
     
        /**
         * Fires before a template part is loaded.
         *
         * @since 5.2.0
         * @since 5.5.0 The `$args` parameter was added.
         *
         * @param string   $slug      The slug name for the generic template.
         * @param string   $name      The name of the specialized template.
         * @param string[] $templates Array of template files to search for, in order.
         * @param array    $args      Additional arguments passed to the template.
         */
        do_action( 'get_template_part', $slug, $name, $templates, $args );
     
        if ( ! locate_template( $templates, true, false, $args ) ) {
            return false;
        }
    }
}

function lightning_get_class_names( $position = '' ) {

    $class_names = array(
        'site-body' => 'site-body',
        'main-section' => 'main-section',
        'sub-section' => 'sub-section',
    );

    if ( $position ){
        $class_names[$position] = $position;
    }

    if ( lightning_is_layout_onecolumn() && lightning_is_subsection() ) {

        $class_names['main-section'] .= ' main-section--margin-bottom--on';

    } elseif ( ! lightning_is_layout_onecolumn() ) {

        $class_names['main-section'] .= ' main-section--col--two';
        $class_names['sub-section'] .= ' sub-section--col--two';
        // 2 column
        $options = get_option( 'lightning_theme_options' );
        // sidebar-position
        if ( isset( $options['sidebar_position'] ) && $options['sidebar_position'] === 'left' ) {
            $class_names['main-section'] .= ' main-section--pos--right';
            $class_names['sub-section'] .= ' sub-section--pos--left';
        }
    }

    if ( lightning_is_site_body_padding_off() ) {
        $class_names['site-body'] .= ' site-body--padding-vertical--off';
        $class_names['main-section'] .= ' main-section--margin-vertical--off';
    }

    return apply_filters( "lightning_get_class_names", $class_names, $position );
}

function lightning_get_the_class_name( $position = '' ){
    $class_names = lightning_get_class_names( $position );
    if ( $position && empty( $class_names[$position] ) ){
        $class_names[$position] = $position;
    }
    return esc_attr( apply_filters( "lightning_get_the_class_name", $class_names[$position], $position ) );
}

function lightning_the_class_name( $position = '' ){
    echo lightning_get_the_class_name( $position );
}



/*
  Theme default options
/*-------------------------------------------*/
function lightning_get_theme_options_default() {
	$theme_options_default = array(
		'front_pr_display'              => true,
	);
	return $theme_options_default;
}

/*
  lightning_get_theme_options()
/*-------------------------------------------*/
function lightning_get_theme_options() {
	$lightning_theme_options_default = lightning_get_theme_options_default();
	$lightning_theme_options         = get_option( 'lightning_theme_options', $lightning_theme_options_default );
	return $lightning_theme_options;
}

/*
  Head logo
/*-------------------------------------------*/
function lightning_get_print_headlogo() {
	$options = get_option( 'lightning_theme_options' );
	if ( ! empty( $options['head_logo'] ) ) {
		$head_logo = apply_filters( 'lightning_head_logo_image_url', $options['head_logo'] );
		if ( $head_logo ) {
			return '<img src="' . esc_url( $head_logo ) . '" alt="' . esc_attr( get_bloginfo( 'name' ) ) . '" />';
		}
	}
	return get_bloginfo( 'name' );
}
function lightning_print_headlogo() {
	echo lightning_get_print_headlogo();
}

function lightning_the_footer_copyight() {

	// copyright
	/*------------------*/
	$lightning_footerCopyRight = '<p>' . sprintf( __( 'Copyright &copy; %s All Rights Reserved.', 'lightning' ), get_bloginfo( 'name' ) ) . '</p>';
	echo apply_filters( 'lightning_footerCopyRightCustom', $lightning_footerCopyRight );

	// Powered
	/*------------------*/
	$lightning_footerPowered = __( '<p>Powered by <a href="https://wordpress.org/">WordPress</a> &amp; <a href="https://lightning.nagoya" target="_blank" title="Free WordPress Theme Lightning"> Lightning Theme</a> by Vektor,Inc. technology.</p>', 'lightning' );
	echo apply_filters( 'lightning_footerPoweredCustom', $lightning_footerPowered );

}

function lightning_get_theme_name() {
	return apply_filters( 'lightning_theme_name', 'Lightning' );
}

function lightning_get_theme_name_short() {
	return apply_filters( 'lightning_get_theme_name_short', 'LTG' );
}

function lightning_get_prefix() {
	$prefix = apply_filters( 'lightning_get_prefix', 'LTG' );
	if ( $prefix ) {
		$prefix .= ' ';
	}
	return $prefix;
}

function lightning_get_prefix_customize_panel() {
	$prefix_customize_panel = apply_filters( 'lightning_get_prefix_customize_panel', 'Lightning' );
	if ( $prefix_customize_panel ) {
		$prefix_customize_panel .= ' ';
	}
	return $prefix_customize_panel;
}