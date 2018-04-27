<?php

/*
$ vagrant ssh
$ cd $(wp theme path --dir lightning)
$ phpunit
*/

class LightningTest extends WP_UnitTestCase {

	public static function lightning_is_mobile_true() {
		return true;
	}

	function test_lightning_top_slide_count() {
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
		foreach ( $test_array as $key => $value ) {
			delete_option( 'lightning_theme_options' );
			$lightning_theme_options = $value['options'];
			add_option( 'lightning_theme_options', $lightning_theme_options );
			$lightning_theme_options = get_option( 'lightning_theme_options' );
			$result                  = lightning_top_slide_count( $lightning_theme_options );

			$this->assertEquals( $value['correct'], $result );
		}
	}


	function test_lightning_slide_cover_style() {
		$test_array = array(
			array(
				'options' => array(
					'top_slide_cover_color_1'   => '#ff0000',
					'top_slide_cover_opacity_1' => '80',
				),
				'correct' => 'background-color:#ff0000;opacity:0.8',
			),
			array(
				'options' => array(
					'top_slide_cover_color_1'   => '#ff0000',
					'top_slide_cover_opacity_1' => null,
				),
				'correct' => '',
			),
			array(
				'options' => array(
					'top_slide_cover_color_1'   => '#ff0000',
					'top_slide_cover_opacity_1' => 0,
				),
				'correct' => '',
			),
			array(
				'options' => array(
					'top_slide_cover_color_1'   => '',
					'top_slide_cover_opacity_1' => 50,
				),
				'correct' => '',
			),
			array(
				'options' => array(
					'top_slide_cover_color_1'   => null,
					'top_slide_cover_opacity_1' => 50,
				),
				'correct' => '',
			),
		);

		foreach ( $test_array as $key => $value ) {
			$lightning_theme_options = $value['options'];
			$result                  = lightning_slide_cover_style( $lightning_theme_options, 1 );
			$this->assertEquals( $value['correct'], $result );
		}
	}


	/*
	モバイル画像の表示仕様が変更になったので現在使用していない
	 */
	function test_lightninig_top_slide_image() {
		$lightning_theme_options                             = get_option( 'lightning_theme_options' );
		$lightning_theme_options['top_slide_image_1']        = 'https://lightning.nagoya/images/sample.jpg';
		$lightning_theme_options['top_slide_image_mobile_1'] = 'https://lightning.nagoya/images/sample_mobile.jpg';
		$lightning_theme_options['top_slide_image_2']        = '';
		$lightning_theme_options['top_slide_image_mobile_2'] = 'https://lightning.nagoya/images/sample_mobile.jpg';
		$lightning_theme_options['top_slide_image_3']        = 'https://lightning.nagoya/images/sample.jpg';
		update_option( 'lightning_theme_options', $lightning_theme_options );

		// ユーザーエージェントがとれない時は is_mobileはfalseを返す
		$is_mobile_state = lightning_is_mobile();
		$this->assertEquals( false, $is_mobile_state );

		// モバイル時 //////////////////////////////////////////////////////////////////////////////

		// モバイルのフィルターフックが動作するかどうか
		add_filter( 'lightning_is_mobile', array( __CLASS__, 'lightning_is_mobile_true' ), 10, 2 );
		$is_mobile_state = lightning_is_mobile();
		$this->assertEquals( true, $is_mobile_state );

	}


	function test_sanitaize_number() {
		$test_array = array(
			array(
				'input'   => '１０',
				'correct' => 10,
			),
			array(
				'input'   => 'test',
				'correct' => 0,
			),
			array(
				'input'   => '',
				'correct' => 0,
			),
		);

		foreach ( $test_array as $key => $value ) {
			$return = lightning_sanitize_number( $value['input'] );
			$this->assertEquals( $value['correct'], $return );
		}
	}

	function test_lightning_sanitize_number_percentage() {
		$test_array = array(
			array(
				'input'   => '100',
				'correct' => 100,
			),
			array(
				'input'   => '0',
				'correct' => 0,
			),
			array(
				'input'   => '10000',
				'correct' => 0,
			),
			array(
				'input'   => '',
				'correct' => 0,
			),
		);

		foreach ( $test_array as $key => $value ) {
			$return = lightning_sanitize_number_percentage( $value['input'] );
			$this->assertEquals( $value['correct'], $return );
		}
	}

	function test_lightning_is_frontpage_onecolumn() {
		$before_options = get_option( 'lightning_theme_options' );
		// トップに指定されてる固定ページIDを取得
		$before_page_on_front = get_option( 'page_on_front' );
		if ( $before_page_on_front ) {
			$page_on_front = $before_page_on_front;
		} else {
			$page_on_front = 1;
		}
		// トップに指定されてる固定ページのテンプレートを取得
		$before_template = get_post_meta( $page_on_front, '_wp_page_template', true );

		$test_array = array(
			// カスタマイザーでチェックが入っている場合（優先）
			array(
				'top_sidebar_hidden' => true,
				'_wp_page_template'  => 'default',
				'correct'            => true,
			),
			// カスタマイザーでチェックが入っていなくても固定ページで指定がある場合
			array(
				'top_sidebar_hidden' => false,
				'_wp_page_template'  => 'page-onecolumn.php',
				'correct'            => true,
			),

		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'is_frontpage_onecolumn' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $value ) {

			// カスタマイザーでの指定
			$options['top_sidebar_hidden'] = $value['top_sidebar_hidden'];
			update_option( 'lightning_theme_options', $options );

			// 固定ページ側のテンプレート
			update_option( 'page_on_front', $page_on_front );
			update_post_meta( $page_on_front, '_wp_page_template', $value['_wp_page_template'] );

			$return = lightning_is_frontpage_onecolumn();
			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			// $this->assertEquals( $value['correct'], $return );
		}

		/* テスト前の値に戻す
		/*--------------------------------*/
		update_option( 'lightning_theme_options', $before_options );
		update_option( 'page_on_front', $before_page_on_front );
		update_post_meta( $before_page_on_front, '_wp_page_template', $before_template );
	}

}
