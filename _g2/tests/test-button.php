<?php
/**
 * Class ButtonTest
 *
 * @package VK_Component_Button
 *
 */

/**
 * Sample test case.
 */
class ButtonTest extends WP_UnitTestCase {

	function test_get_style_text() {
		$test_array = array(
			array(
				'btn_ghost'      => true,
				'btn_color_bg'   => '#f00',
				'btn_color_text' => '#fff',
				'correct'        => 'color:#fff;',
			),
			array(
				'btn_ghost'      => true,
				'btn_color_bg'   => '#f00',
				'btn_color_text' => '#333',
				'correct'        => 'color:#333;',
			),
			array(
				'btn_ghost'      => true,
				'btn_color_text' => '#333',
				'btn_color_bg'   => '#efefef',
				'shadow_use'     => true,
				'shadow_color'   => '#fff',
				'correct'        => 'color:#333;text-shadow:0 0 2px #fff;',
			),
			array(
				'btn_ghost'      => false,
				'btn_color_text' => '#fff',
				'btn_color_bg'   => '#f00',
				'correct'        => 'color:#fff;',
			),
			// 塗りボタンだが背景が明るい時
			array(
				'btn_ghost'      => false,
				'btn_color_text' => '#333',
				'btn_color_bg'   => '#efefef',
				'correct'        => 'color:#000;',
			),
		);
				print PHP_EOL;
		print '★★★★★-------------------------------' . PHP_EOL;
		print 'get_style_text' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $value ) {
			$result = VK_Component_Button::get_style_text( $value );
			print 'return  :' . $result . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $result );
		}
	}

	function test_get_style_text_hover() {
		$test_array = array(
			array(
				'btn_ghost'    => true,
				'btn_color_bg' => '#f00',
				'correct'      => 'color:#fff;',
			),
			array(
				'btn_ghost'    => true,
				'btn_color_bg' => '',
				'correct'      => 'color:#000;',
			),
			array(
				'btn_ghost'    => false,
				'btn_color_bg' => '#f00',
				'correct'      => 'color:#fff;',
			),
			array(
				'btn_ghost'    => true,
				'btn_color_bg' => '#efefef',
				'correct'      => 'color:#000;',
			),
		);
				print PHP_EOL;
		print '★★★★★-------------------------------' . PHP_EOL;
		print 'get_style_text_hover' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $value ) {
			$result = VK_Component_Button::get_style_text_hover( $value );
			print 'return  :' . $result . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $result );
		}
	}

	function test_get_style_bg() {
		$test_array = array(
			array(
				'btn_ghost'    => false,
				'btn_color_bg' => '#f00',
				'correct'      => 'background-color:#f00;',
			),
			array(
				'btn_ghost'    => true,
				'btn_color_bg' => '#f00',
				'correct'      => 'background:transparent;transition: .3s;',
			),
		);
				print PHP_EOL;
		print '★★★★★-------------------------------' . PHP_EOL;
		print 'get_style_bg' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $value ) {
			$result = VK_Component_Button::get_style_bg( $value );
			print 'return  :' . $result . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $result );
		}
	}

	function test_get_style_bg_hover() {
		$test_array = array(
			array(
				'btn_ghost'    => true,
				'btn_color_bg' => '#f00',
				'correct'      => 'background-color:#f00;',
			),
			array(
				'btn_ghost'    => false,
				'btn_color_bg' => '#c00',
				'correct'      => 'filter: brightness(1.2) saturate(2);',
			),
		);
				print PHP_EOL;
		print '★★★★★-------------------------------' . PHP_EOL;
		print 'get_style_bg_hover' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $value ) {
			$result = VK_Component_Button::get_style_bg_hover( $value );
			print 'return  :' . $result . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $result );
		}
	}

	function test_get_style_border() {
		$test_array = array(
			array(
				'btn_ghost'      => true,
				'btn_color_text' => '#fff',
				'correct'        => 'border-color:#fff;',
			),
			array(
				'btn_ghost'    => false,
				'btn_color_bg' => '#c00',
				'correct'      => 'border-color:#c00;',
			),
		);
				print PHP_EOL;
		print '★★★★★-------------------------------' . PHP_EOL;
		print 'get_style_border' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $value ) {
			$result = VK_Component_Button::get_style_border( $value );
			print 'return  :' . $result . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $result );

		}
	}

	function test_get_style_border_hover() {
		$test_array = array(
			array(
				'btn_ghost'    => true,
				'btn_color_bg' => '#c00',
				'correct'      => 'border-color:#c00;',
			),
			array(
				'btn_ghost'    => false,
				'btn_color_bg' => '#c00',
				'correct'      => 'border-color:#c00;',
			),
		);
				print PHP_EOL;
		print '★★★★★-------------------------------' . PHP_EOL;
		print 'get_style_border_hover' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $value ) {
			$result = VK_Component_Button::get_style_border_hover( $value );
			print 'return  :' . $result . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $result );

		}
	}

}
