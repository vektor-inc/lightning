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

	function test_lightninig_top_slide_image(){
		$lightning_theme_options = get_option('lightning_theme_options');
		$lightning_theme_options['top_slide_image_1'] = 'https://lightning.nagoya/images/sample.jpg';
		$lightning_theme_options['top_slide_image_mobile_1'] = 'https://lightning.nagoya/images/sample_mobile.jpg';
		$lightning_theme_options['top_slide_image_2'] = '';
		$lightning_theme_options['top_slide_image_mobile_2'] = 'https://lightning.nagoya/images/sample_mobile.jpg';
		$lightning_theme_options['top_slide_image_3'] = 'https://lightning.nagoya/images/sample.jpg';
		update_option( 'lightning_theme_options',  $lightning_theme_options );

		// PC版スライド画像のURLが正しく返ってくるかどうか？
		$top_slide_image_src = lightning_top_slide_image_src(1);
		$this->assertEquals('https://lightning.nagoya/images/sample.jpg', $top_slide_image_src);

		// PC版の登録がなくモバイル版だけ登録されたときにモバイル版のURLではなく空を返すか？
		$top_slide_image_src = lightning_top_slide_image_src(2);
		$this->assertEquals( '', $top_slide_image_src );

		// ユーザーエージェントがとれない時は is_mobileはfalseを返す
		$is_mobile_state = lightning_is_mobile();
		$this->assertEquals( false, $is_mobile_state);

		// モバイル時 //////////////////////////////////////////////////////////////////////////////

		// モバイルのフィルターフックが動作するかどうか
		add_filter( 'lightning_is_mobile' , array( __CLASS__, 'lightning_is_mobile_true'), 10,2 );
		$is_mobile_state = lightning_is_mobile();
		$this->assertEquals( true, $is_mobile_state );

		// モバイル端末で閲覧時にモバイル画像を返すかどうか
		$top_slide_image_src = lightning_top_slide_image_src(1);
		$this->assertEquals('https://lightning.nagoya/images/sample_mobile.jpg', $top_slide_image_src);

		// モバイルの時にPC版だけの画像が登録されておりモバイル画像が非登録の場合にPC画像のURLを返すかどうか
		$top_slide_image_src = lightning_top_slide_image_src(3);
		$this->assertEquals('https://lightning.nagoya/images/sample.jpg', $top_slide_image_src);

	}
}