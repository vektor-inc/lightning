<?php

/*
cd /app
bash setup-phpunit.sh
source ~/.bashrc
cd $(wp theme path --dir lightning)
phpunit
*/

class LightningTest extends WP_UnitTestCase {

	public static function lightning_is_mobile_true() {
		return true;
	}

	function test_lightning_get_theme_options() {
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_lightning_get_theme_options' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		$test_array = array(
			// array(
			// 'options'   => array(), // フィールド自体が存在しない場合にサンプル画像を返す
			// 'check_key' => 'top_slide_image_1',
			// 'correct'   => get_template_directory_uri() . '/assets/images/top_image_1.jpg',
			// ),
			array(
				'options'   => array(
					'top_slide_image_1' => null,
				),
				'check_key' => 'top_slide_image_1',
				'correct'   => '',
			),
			array(
				'options'   => array(
					'top_slide_image_1' => '',
				),
				'check_key' => 'top_slide_image_1',
				'correct'   => '',
			),
			// array(
			// 'options'   => array(
			// 'top_slide_image_1' => 'http://aaa.com/sample.jpg',
			// ),
			// 'check_key' => 'top_slide_image_1',
			// 'correct'   => 'http://aaa.com/sample.jpg',
			// ),
			// array(
			// 'options'   => array(),
			// 'check_key' => 'top_slide_text_title_1',
			// 'correct'   => __( 'Simple and Customize easy <br>WordPress theme.', 'lightning' ),
			// ),
			// array(
			// 'options'   => array(
			// 'top_slide_text_title_1' => null,
			// ),
			// 'check_key' => 'top_slide_text_title_1',
			// 'correct'   => '',
			// ),
			// array(
			// 'options'   => array(
			// 'top_slide_text_title_1' => '',
			// ),
			// 'check_key' => 'top_slide_text_title_1',
			// 'correct'   => '',
			// ),
		);
		// 操作前のオプション値を取得
		$before_options = get_option( 'lightning_theme_options' );
		foreach ( $test_array as $key => $value ) {
			delete_option( 'lightning_theme_options' );
			$lightning_theme_options = $value['options'];
			add_option( 'lightning_theme_options', $lightning_theme_options );

			$result    = lightning_get_theme_options();
			$check_key = $value['check_key'];
			print 'return  :' . $result[ $check_key ] . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $result[ $check_key ] );

		}
		// テストで入れたオプションを削除
		delete_option( 'lightning_theme_options' );
		if ( $before_options ) {
			// テスト前のオプション値に戻す
			add_option( 'lightning_theme_options', $before_options );
		}
	}

	function test_lightning_top_slide_count() {
		$test_array = array(
			// array(
			// 'options' => array(),
			// 'correct' => 3,
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

	function test_lightning_options_compatible() {
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_lightning_options_compatible' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		$test_array = array(
			array(
				'options'   => array(
					'top_sidebar_hidden' => true,
				),
				// 'check_keys' => array( array( 'layout' => 'front-page' ) )
				'correct'   => array(
					'layout' => array(
						'front-page' => 'col-one-no-subsection',
					),
				),
			),
			array(
				'options'   => array(
					'top_sidebar_hidden' => true,
					'layout' => array(
						'front-page' => 'col-two',
					),
				),
				// 'check_keys' => array( array( 'layout' => 'front-page' ) ),
				'correct'   => array(
					'layout' => array(
						'front-page' => 'col-two',
					),
				),
			),
		);
		// 操作前のオプション値を取得
		$before_options = get_option( 'lightning_theme_options' );
		foreach ( $test_array as $key => $value ) {
			delete_option( 'lightning_theme_options' );

			add_option( 'lightning_theme_options', $value['options'] );

			lightning_options_compatible();

			$return = get_option('lightning_theme_options');

			$this->assertEquals( $value['correct']['layout']['front-page'], $return['layout']['front-page'] );

		}
		// テストで入れたオプションを削除
		delete_option( 'lightning_theme_options' );
		if ( $before_options ) {
			// テスト前のオプション値に戻す
			add_option( 'lightning_theme_options', $before_options );
		}
	}

	function test_lightning_check_color_mode() {
		$test_array = array(
			array(
				'input'   => '#fff',
				'correct' => 'light',
			),
			array(
				'input'   => '#ffffff',
				'correct' => 'light',
			),
			array(
				'input'   => '#000',
				'correct' => 'dark',
			),
			array(
				'input'   => '#f00',
				'correct' => 'dark',
			),
			array(
				'input'   => '#ff0',
				'correct' => 'light',
			),
			array(
				'input'   => '#0ff',
				'correct' => 'light',
			),
			array(
				'input'   => '#808080',
				'correct' => 'light',
			),
			array(
				'input'   => '#7f7f7f',
				'correct' => 'dark',
			),
		);
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_lightning_check_color_mode' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $value ) {
			$return = lightning_check_color_mode( $value['input'] );
			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $return );
		}
	}

	function test_lightning_is_slide_outer_link() {
		$test_array = array(
			array(
				'options' => array(
					'top_slide_url_1'      => 'https://google.com',
					'top_slide_text_btn_1' => '詳しくはこちら',
				),
				'correct' => false,
			),
			array(
				'options' => array(
					'top_slide_url_1'      => '',
					'top_slide_text_btn_1' => '詳しくはこちら',
				),
				'correct' => false,
			),
			array(
				'options' => array(
					'top_slide_url_1'      => 'https://google.com',
					'top_slide_text_btn_1' => '',
				),
				'correct' => true,
			),
		);
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'test_lightning_is_slide_outer_link' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		foreach ( $test_array as $key => $value ) {
			$return = lightning_is_slide_outer_link( $value['options'], 1 );
			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $return );
		}
	}

}
