<?php
/**
 * LTG_G3_Slider のテスト用フィクスチャ trait
 *
 * スライダーのオプション設定と後片付けは複数のテストファイルで共通して必要になるため、
 * trait に切り出して二重管理を避ける.
 *
 * @package vektor-inc/lightning
 */

trait LTG_G3_Slider_Options_Trait {

	/**
	 * テスト終了ごとにスライダーのオプションを削除する.
	 *
	 * @return void
	 */
	public function tearDown(): void {
		delete_option( 'lightning_theme_options' );
		wp_cache_flush();
		parent::tearDown();
	}

	/**
	 * スライダーのオプションを既定値にマージして保存する.
	 *
	 * @param array $overrides 既定値に上書きするオプション.
	 * @return void
	 */
	private function set_slider_options( $overrides = array() ) {
		$defaults = lightning_g3_slider_default_options();
		$options  = array_merge( $defaults, $overrides );
		update_option( 'lightning_theme_options', $options );
		wp_cache_flush();
	}
}
