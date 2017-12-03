<?php

/*
$ vagrant ssh
$ cd $(wp theme path --dir lightning)
$ phpunit
*/

class LightningTest extends WP_UnitTestCase {

	public static function lightning_is_mobile_true(){
		return true;
	}

	function test_lightning_top_slide_count(){
		$test_array = array(
			// array(
			// 	'options' => array(),
			// 	'correct' => 3,
			// ),
			array(
				'options' => array(
					'top_slide_image_1' => 'https://lightning.nagoya/images/sample.jpg',
					'top_slide_image_2' => 'https://lightning.nagoya/images/sample.jpg',
				),
				'correct' => 2,
			),
			array(
				'options' => array(
					'top_slide_image_1' => 'https://lightning.nagoya/images/sample.jpg',
					'top_slide_image_5' => 'https://lightning.nagoya/images/sample.jpg',
				),
				'correct' => 2,
			),
			array(
				'options' => array(
					'top_slide_image_1' => 'https://lightning.nagoya/images/sample.jpg',
					'top_slide_image_2' => '',
					'top_slide_image_5' => 'https://lightning.nagoya/images/sample.jpg',
				),
				'correct' => 2,
			),
		);
		foreach ($test_array as $key => $value) {
			delete_option( 'lightning_theme_options' );
			$lightning_theme_options = $value['options'];
			add_option( 'lightning_theme_options',  $lightning_theme_options );
			$lightning_theme_options = get_option('lightning_theme_options');
			$result = lightning_top_slide_count( $lightning_theme_options );

			$this->assertEquals( $value['correct'], $result);
		}
	}

	function test_lightninig_top_slide_image(){
		$lightning_theme_options = get_option('lightning_theme_options');
		$lightning_theme_options['top_slide_image_1'] = 'https://lightning.nagoya/images/sample.jpg';
		$lightning_theme_options['top_slide_image_mobile_1'] = 'https://lightning.nagoya/images/sample_mobile.jpg';
		$lightning_theme_options['top_slide_image_2'] = '';
		$lightning_theme_options['top_slide_image_mobile_2'] = 'https://lightning.nagoya/images/sample_mobile.jpg';
		$lightning_theme_options['top_slide_image_3'] = 'https://lightning.nagoya/images/sample.jpg';
		update_option( 'lightning_theme_options',  $lightning_theme_options );

		// ユーザーエージェントがとれない時は is_mobileはfalseを返す
		$is_mobile_state = lightning_is_mobile();
		$this->assertEquals( false, $is_mobile_state);

		// モバイル時 //////////////////////////////////////////////////////////////////////////////

		// モバイルのフィルターフックが動作するかどうか
		add_filter( 'lightning_is_mobile' , array( __CLASS__, 'lightning_is_mobile_true'), 10,2 );
		$is_mobile_state = lightning_is_mobile();
		$this->assertEquals( true, $is_mobile_state );

	}

	function test_sanitaize_number(){
		$test_array = array(
			array(
				'input' => '１０',
				'correct' => 10,
			),
			array(
				'input' => 'test',
				'correct' => 0
			),
			array(
				'input' => '',
				'correct' => 0
			)
		);

		foreach ($test_array as $key => $value) {
			$return = lightning_sanitize_number($value['input']);
			$this->assertEquals( $value['correct'], $return );
		}
	}

	function test_lightning_sanitize_number_percentage(){
		$test_array = array(
			array(
				'input' => '100',
				'correct' => 100,
			),
			array(
				'input' => '0',
				'correct' => 0,
			),
			array(
				'input' => '10000',
				'correct' => 0,
			),
			array(
				'input' => '',
				'correct' => 0
			)
		);

		foreach ($test_array as $key => $value) {
			$return = lightning_sanitize_number_percentage($value['input']);
			$this->assertEquals( $value['correct'], $return );
		}
	}

}
