<?php
/*-------------------------------------------*/
/*  Load modules
/*-------------------------------------------*/
if ( ! class_exists( 'Vk_Font_Awesome_Selector' ) )
{
	get_template_part( '/library/font-awesome-selector/class.font-awesome-selector' );

	global $vk_font_awesome_selector_textdomain;
	$vk_font_awesome_selector_textdomain = 'lightning';

}