<?php
/**
 * WP_Widget_ltg_post_list ウィジェットのテスト
 *
 * @package vektor-inc/lightning
 */

/**
 * WP_Widget_ltg_post_list の form() メソッドに関するテスト
 */
class WidgetNewPostsTest extends WP_UnitTestCase {

	/**
	 * テスト対象のウィジェットインスタンス
	 *
	 * @var WP_Widget_ltg_post_list
	 */
	private $widget;

	/**
	 * 各テスト前にウィジェットインスタンスを初期化する
	 */
	public function setUp(): void {
		parent::setUp();
		$this->widget = new WP_Widget_ltg_post_list();
	}

	/**
	 * 空の $instance で form() を呼び出しても E_WARNING が発生しないことを確認する
	 *
	 * ウィジェット初回追加時（保存前）は $instance が空配列で渡されるため、
	 * wp_parse_args() によるデフォルト値補完が正しく機能していることを検証する。
	 */
	public function test_form_with_empty_instance_does_not_trigger_warning() {
		// E_WARNING が発生した場合にテストを失敗させるためのエラーハンドラーを登録する
		$warning_occurred = false;
		set_error_handler(
			function( $errno, $errstr ) use ( &$warning_occurred ) {
				if ( E_WARNING === $errno ) {
					$warning_occurred = true;
				}
				// デフォルトのエラーハンドラーを実行しない
				return true;
			}
		);

		// 空の $instance で form() を呼び出す（ウィジェット初回追加時と同等の状況）
		ob_start();
		$this->widget->form( array() );
		ob_end_clean();

		// エラーハンドラーを元に戻す
		restore_error_handler();

		// E_WARNING が発生していないことを確認する
		$this->assertFalse( $warning_occurred, '空の $instance で form() を呼び出した際に E_WARNING が発生した' );
	}

	/**
	 * 空の $instance で form() を呼び出した際に、デフォルト値が正しく適用されることを確認する
	 *
	 * wp_parse_args() で設定されるデフォルト値が form() の出力に反映されることを検証する。
	 */
	public function test_form_with_empty_instance_uses_default_values() {
		// form() の出力をキャプチャする
		ob_start();
		$this->widget->form( array() );
		$output = ob_get_clean();

		// デフォルト件数 10 が input の value に含まれることを確認する
		$this->assertStringContainsString( 'value="10"', $output, 'デフォルトの表示件数（10）が form() の出力に含まれていない' );

		// デフォルト投稿タイプ post が input の value に含まれることを確認する
		$this->assertStringContainsString( 'value="post"', $output, 'デフォルトの投稿タイプ（post）が form() の出力に含まれていない' );
	}

	/**
	 * 保存済みの $instance を渡した際に、その値が form() の出力に反映されることを確認する
	 *
	 * 既存ユーザーの設定値が正しくフォームに表示されることを検証する。
	 */
	public function test_form_with_existing_instance_reflects_saved_values() {
		// 保存済み設定を模倣した $instance
		$instance = array(
			'label'     => 'お知らせ',
			'count'     => 3,
			'format'    => '0',
			'post_type' => 'news',
			'terms'     => '',
			'more_url'  => '',
			'more_text' => '',
		);

		// form() の出力をキャプチャする
		ob_start();
		$this->widget->form( $instance );
		$output = ob_get_clean();

		// 保存済みの件数 3 が input の value に含まれることを確認する
		$this->assertStringContainsString( 'value="3"', $output, '保存済みの表示件数（3）が form() の出力に反映されていない' );

		// 保存済みの投稿タイプ news が input の value に含まれることを確認する
		$this->assertStringContainsString( 'value="news"', $output, '保存済みの投稿タイプ（news）が form() の出力に反映されていない' );
	}
}
