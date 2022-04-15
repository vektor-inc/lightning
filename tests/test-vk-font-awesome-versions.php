<?php
use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;

class VkFontAwesomeVersionsTest extends WP_UnitTestCase {

	/**
	 * Test get_icon_tag() method
	 *
	 * @return void
	 */
	function test_get_icon_tag() {

		$tests = array(
			array(
				'option_fa_version' => '4.7',
				'saved_value'       => 'fa-file-text-o',
				'correct'           => '<i class="fa fa-file-text-o"></i>',
			),
			array(
				'option_fa_version' => '6_WebFonts_CSS',
				'saved_value'       => 'far fa-file-alt',
				'correct'           => '<i class="far fa-file-alt"></i>',
			),
			array(
				'option_fa_version' => '6_WebFonts_CSS',
				'saved_value'       => '<i class="far fa-file-alt"></i>',
				'correct'           => '<i class="far fa-file-alt"></i>',
			),
		);

		foreach ( $tests as $key => $value ){
			update_option( 'vk_font_awesome_version', $value['option_fa_version'] );
			$return = VkFontAwesomeVersions::get_icon_tag( $value['saved_value'] );
			$this->assertEquals( $value['correct'], $return );
		}
	}
}
