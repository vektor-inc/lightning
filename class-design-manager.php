<?php

class Lightning_Design_Manager{

	static function init(){
		add_action( 'customize_register', array( __CLASS__, 'customize_register' ) );
		add_action( 'wp_loaded', array( __CLASS__, 'ignite_Design' ) );
		add_filter('lightning-disable-theme_style', array( __CLASS__, 'css_disable') );
	}

	static function customize_register( $wp_customize ){
		$wp_customize->add_section(
			'lightning_design_skin',
			array(
				'title'       => __( 'Design_skin', 'lightning' ),
				'priority'    => 500
			)
		);

		$wp_customize->add_setting( 'lightning_design_skin', array(
			'default'           => 'default',
			'type'              => 'option',
			'capability'        => 'edit_theme_options',
			'sanitize_callback' => array( __CLASS__, 'sanitize_design_skins' ),
		) );


		$skins = self::get_skins();
		while(list($k,$v) = each($skins)){ $skins[$k] = isset( $v['name'] )? $v['name'] : $k ; }

		$wp_customize->add_control( 'lightning_design_skin', array(
			'label'		=> __( 'design skin' , 'lightning' ),
			'section'	=> 'lightning_design_skin',
			'settings'  => 'lightning_design_skin',
			'type'		=> 'select',
			'priority'	=> 504,
			'choices'   => $skins,
		));

	}

	static function get_skins(){
		$ex_skins = apply_filters( 'lightning_Design_skins', array() );
		ksort( $ex_skins );
		$skins['default'] = array( 'name' => 'default skin' );
		return $skins + $ex_skins;
	}

	static function get_current_skin(){
		$current_skin = get_option( 'lightning_design_skin' );
		if( in_array( $current_skin, array_keys( self::get_skins() ) ) ) return $current_skin;
		return 'default';
	}

	static function get_useble_skins(){
		return array_keys( self::get_skins() );
	}

	static function sanitize_design_skins( $call ){
		return $call;
	}

	static function ignite_Design(){
		$skins = self::get_skins();
		if( isset($skins[self::get_current_skin()]['callback']) and $skins[self::get_current_skin()]['callback'] )
			call_user_func_array( $skins[self::get_current_skin()]['callback'], array() );
	}

	static function css_disable( $flag ){
		$skins = self::get_skins();
		if( isset($skins[self::get_current_skin()]['disable_css']) and $skins[self::get_current_skin()]['disable_css'] )
			$flag = true;
		return $flag;
	}
}

Lightning_Design_Manager::init();


// add_filter( 'lightning_Design_skins', 'lightning_Register_skin' );
// function lightning_Register_skin( $array ){
// 	$array['test'] = array(
// 		'name'     => 'テストスキン',
// 		'callback' => 'callback_function',
// 		'disable_css' => true,
// 	);
// 	return $array;
// }

// function callback_function(){
// 	echo "i'm current";
// }
