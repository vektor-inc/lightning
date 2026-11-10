<?php
/**
 * データベース更新の実行条件.
 *
 * @package vektor-inc/lightning
 */

/**
 * VK_Old_Options_Notice の実行条件を確認する.
 */
class OldOptionsNoticeTest extends WP_UnitTestCase {

	/**
	 * テスト後にリクエスト上の更新用クエリを消す.
	 *
	 * @return void
	 */
	public function tear_down() {
		global $vk_update_link;
		unset( $_GET[ $vk_update_link ], $_REQUEST[ $vk_update_link ], $_REQUEST['_wpnonce'] );
		parent::tear_down();
	}

	/**
	 * 旧フラグを持つ投稿を作り、移行済みの印を消す.
	 *
	 * @return int
	 */
	private function create_post_with_old_flag() {
		delete_option( 'lightning_old_options' );
		$post_id = self::factory()->post->create();
		update_post_meta(
			$post_id,
			'_lightning_design_setting',
			array(
				'hidden_page_header_and_breadcrumb' => true,
			)
		);
		return $post_id;
	}

	/**
	 * 更新用クエリをリクエストに載せる.
	 *
	 * @return void
	 */
	private function set_update_request() {
		global $vk_update_link;
		$_GET[ $vk_update_link ]     = '1';
		$_REQUEST[ $vk_update_link ] = '1';
	}

	/**
	 * 権限がない利用者では、更新用のクエリがあっても保存値が変わらない.
	 *
	 * @return void
	 */
	public function test_launch_update_options_does_nothing_without_permission() {
		$post_id = $this->create_post_with_old_flag();
		$user_id = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $user_id );
		$this->set_update_request();

		VK_Old_Options_Notice::launch_update_options();

		$meta = get_post_meta( $post_id, '_lightning_design_setting', true );
		$this->assertTrue( $meta['hidden_page_header_and_breadcrumb'] );
		$this->assertFalse( get_option( 'lightning_old_options' ) );
	}

	/**
	 * ログインしていないときも、保存値は変わらない.
	 *
	 * @return void
	 */
	public function test_launch_update_options_does_nothing_when_logged_out() {
		$post_id = $this->create_post_with_old_flag();
		wp_set_current_user( 0 );
		$this->set_update_request();

		VK_Old_Options_Notice::launch_update_options();

		$meta = get_post_meta( $post_id, '_lightning_design_setting', true );
		$this->assertTrue( $meta['hidden_page_header_and_breadcrumb'] );
		$this->assertFalse( get_option( 'lightning_old_options' ) );
	}

	/**
	 * サイト管理者で nonce があるときは、旧フラグが2つに分かれて保存される.
	 *
	 * @return void
	 */
	public function test_launch_update_options_runs_for_admin_with_nonce() {
		$post_id = $this->create_post_with_old_flag();
		$user_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $user_id );
		$this->set_update_request();
		$_REQUEST['_wpnonce'] = wp_create_nonce( VK_Old_Options_Notice::UPDATE_NONCE_ACTION );

		VK_Old_Options_Notice::launch_update_options();

		$meta = get_post_meta( $post_id, '_lightning_design_setting', true );
		$this->assertTrue( $meta['hidden_page_header'] );
		$this->assertTrue( $meta['hidden_breadcrumb'] );
		$this->assertArrayNotHasKey( 'hidden_page_header_and_breadcrumb', $meta );
	}

	/**
	 * 更新ボタンはサイト管理者のダッシュボードにだけ出て、リンクに nonce が付く.
	 *
	 * @return void
	 */
	public function test_notice_is_shown_only_to_admin_and_includes_nonce() {
		global $pagenow;
		$pagenow = 'index.php';
		delete_option( 'lightning_old_options' );

		$subscriber_id = self::factory()->user->create( array( 'role' => 'subscriber' ) );
		wp_set_current_user( $subscriber_id );
		ob_start();
		VK_Old_Options_Notice::display_old_options_notice();
		$subscriber_html = ob_get_clean();
		$this->assertSame( '', $subscriber_html );

		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );
		ob_start();
		VK_Old_Options_Notice::display_old_options_notice();
		$admin_html = ob_get_clean();
		$this->assertStringContainsString( 'class="button button-primary"', $admin_html );
		$this->assertStringContainsString( '_wpnonce=', $admin_html );
	}
}
