<?php

class Lightning_Design_Manager{

	static function init(){
		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'ignite_Design' ) );
		add_filter( 'lightning-disable-theme_style', array( __CLASS__, 'css_disable') );
	}

	static function customize_register( $wp_customize ){
		// $wp_customize->add_section(
		// 	'lightning_design_skin',
		// 	array(
		// 		'title'       => __( 'Lightning Design skin', 'lightning' ),
		// 		'priority'    => 450
		// 	)
		// );

		$wp_customize->add_setting( 'lightning_design_skin', array(
			'default'           => 'origin',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => array( __CLASS__, 'sanitize_design_skins' ),
		) );

		$skins = self::get_skins();
		while(list($k,$v) = each($skins)){ $skins[$k] = isset( $v['name'] )? $v['name'] : $k ; }

		$wp_customize->add_control( 'lightning_design_skin', array(
			'label'		=> __( 'Design skin' , 'lightning' ),
			'section'	=> 'lightning_design',
			'settings'  => 'lightning_design_skin',
			'description' => '<span style="color:red;font-weight:bold;">'.__( 'If you change the skin, please save once and reload the page.', 'lightning' ) . '</span><br/>' .
				__( 'If you reload after the saving, it will be displayed skin-specific configuration items.', 'lightning') . '<br/> '.
				__( '*There is also a case where there is no skin-specific installation item.', 'lightning' ),
			'type'		=> 'select',
			'priority'	=> 100,
			'choices'   => $skins,
		));

		if ( self::get_current_skin() != 'origin' ) self::set_customizer_callback( $wp_customize );
	}

	static function get_skins(){
		$ex_skins = apply_filters( 'lightning_design_skins', array() );
		ksort( $ex_skins );
		$skins['origin'] = array( 'name' => 'Lightning Origin' );
		return $skins + $ex_skins;
	}

	static function get_current_skin(){
		$current_skin = get_option( 'lightning_design_skin' );
		if ( in_array( $current_skin, array_keys( self::get_skins() ) ) ) return $current_skin;
		return 'origin';
	}

	///
	static function get_useble_skins(){
		return array_keys( self::get_skins() );
	}

	static function sanitize_design_skins( $call ){
		return $call;
	}

	static function ignite_Design(){
		$skins = self::get_skins();
		if ( isset($skins[self::get_current_skin()]['callback']) and $skins[self::get_current_skin()]['callback'] )
			call_user_func_array( $skins[self::get_current_skin()]['callback'], array() );
	}

	static function set_customizer_callback( $wp_customize ){
		$skins = self::get_skins();
		if ( isset($skins[self::get_current_skin()]['customizer']) and $skins[self::get_current_skin()]['customizer'] )
			call_user_func_array( $skins[self::get_current_skin()]['customizer'], array( $wp_customize ) );
	}

	static function css_disable( $flag ){
		$skins = self::get_skins();
		if ( isset($skins[self::get_current_skin()]['disable_css']) and $skins[self::get_current_skin()]['disable_css'] )
			$flag = true;
		return $flag;
	}
}

Lightning_Design_Manager::init();

if ( Lightning_Design_Manager::get_current_skin() == 'origin' )
	get_template_part('/design_skin/origin/origin');

// add_filter( 'lightning_design_skins', 'lightning_register_skin' );
// function lightning_register_skin( $array ){
//  $array['sample'] = array(
// 	 'name'     => 'Sample Skin',                            // Skin Name
// 	 'callback' => 'lightning_skin_current_function_sample', // Require skins function name
// 	 'disable_css' => true,                                  // kill default design(origin) style
// 	 // 'customizer' => 'customizer_function_sample'
//  );
//  return $array;
// }

// function callback_function(){
// 	echo "i'm current";
// }

// function customizer_function( $wp_customize ){
// 		$wp_customize->add_setting( 'lightning_design_test_sample', array(
// 			'origin'            => null,
// 			'type'              => 'option',
// 			'capability'        => 'edit_theme_options',
// 		) );

// 		$wp_customize->add_control('lightning_design_test_sample',array(
// 			'label' => 'its test button',
// 			'section' => 'lightning_design_skin',
// 			'description' => 'test checkbox',
// 			'type' => 'checkbox'
// 		));

// 	return;
// }
