<?php

/*
cd /app
bash setup-phpunit.sh
source ~/.bashrc
cd wp-content/themes/lightning
phpunit
*/

class LightningMetaDesiginSettingCompatibleTest extends WP_UnitTestCase {
	
	function test_lightning_pageheader_and_breadcrumb_compatible() {
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_pageheader_and_breadcrumb_compatible' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// 
		delete_option('lightning_old_options');

		// Create test page
		$post           = array(
			'post_title'   => 'test page',
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_post_id = wp_insert_post( $post );
		$value['hidden_page_header_and_breadcrumb'] = true;
		update_post_meta( $test_post_id, '_lightning_design_setting', $value );

		// ジャッジが反応するかどうか
		$return = VK_Old_Options_Notice::option_judgment( 'judge' );
		$this->assertEquals( true, $return );

		VK_Old_Options_Notice::option_judgment( 'update' );
		$meta = get_post_meta( $test_post_id, '_lightning_design_setting', true );
		
		print '<pre style="text-align:left">';print_r($meta );print '</pre>';
		$this->assertEquals( true, $meta['hidden_page_header'] );
		$this->assertEquals( true, $meta['hidden_breadcrumb'] );

	}

	/**
	 * lightning_sanitize_design_setting() がデザイン設定の入力値を文字列として整形することをテストする。
	 */
	public function test_lightning_sanitize_design_setting() {
		$test_cases = array(
			array(
				'test_condition_name' => '文字列の値だけの配列 => そのまま',
				'input'               => array(
					'layout'              => 'col-two',
					'hidden_page_header'  => 'true',
					'siteContent_padding' => 'true',
				),
				'expected'            => array(
					'layout'              => 'col-two',
					'hidden_page_header'  => 'true',
					'siteContent_padding' => 'true',
				),
			),
			array(
				'test_condition_name' => 'プラグイン由来のキー（section_base・header_trans）=> 保持される',
				'input'               => array(
					'layout'       => 'default',
					'section_base' => 'use',
					'header_trans' => 'true',
				),
				'expected'            => array(
					'layout'       => 'default',
					'section_base' => 'use',
					'header_trans' => 'true',
				),
			),
			array(
				'test_condition_name' => 'HTML タグと前後の空白を含む値 => タグと空白を除去',
				'input'               => array( 'layout' => ' <b>col-two</b> ' ),
				'expected'            => array( 'layout' => 'col-two' ),
			),
			array(
				'test_condition_name' => 'HTML タグを含むキー => キーからもタグを除去',
				'input'               => array( '<i>layout</i>' => 'col-two' ),
				'expected'            => array( 'layout' => 'col-two' ),
			),
			array(
				'test_condition_name' => '入れ子の配列の値 => その項目だけ捨てる',
				'input'               => array(
					'layout' => 'col-two',
					'nested' => array( 'a' => 'b' ),
				),
				'expected'            => array( 'layout' => 'col-two' ),
			),
			array(
				'test_condition_name' => '配列以外（文字列）=> 空の配列',
				'input'               => 'col-two',
				'expected'            => array(),
			),
		);

		foreach ( $test_cases as $case ) {
			$this->assertSame( $case['expected'], lightning_sanitize_design_setting( $case['input'] ), $case['test_condition_name'] );
		}
	}

	/**
	 * lightning_design_setting_save() がクラシックエディターの保存時に、nonce と編集権限を確認したうえで整形した値を保存することをテストする。
	 */
	public function test_lightning_design_setting_save() {
		$admin_id      = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$author_id     = self::factory()->user->create( array( 'role' => 'author' ) );
		$subscriber_id = self::factory()->user->create( array( 'role' => 'subscriber' ) );

		// 管理者の投稿は、投稿者・購読者には編集権限が無い.
		$admin_post_id  = self::factory()->post->create( array( 'post_author' => $admin_id ) );
		$author_post_id = self::factory()->post->create( array( 'post_author' => $author_id ) );

		// 各ケースの前に入れておく保存済みの値（プラグイン由来のキーを含む）.
		$saved = array(
			'layout'       => 'col-one',
			'section_base' => 'use',
		);

		$test_cases = array(
			array(
				'test_condition_name' => '管理者が自分の投稿に nonce 付きで保存 => 送信した値が保存される',
				'user_id'             => $admin_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'valid',
				'posted'              => array(
					'layout'              => 'col-two',
					'hidden_page_header'  => 'true',
					'siteContent_padding' => 'true',
				),
				'expected'            => array(
					'layout'              => 'col-two',
					'hidden_page_header'  => 'true',
					'siteContent_padding' => 'true',
				),
			),
			array(
				'test_condition_name' => '投稿者が自分の投稿に nonce 付きで保存 => 送信した値が保存される',
				'user_id'             => $author_id,
				'post_id'             => $author_post_id,
				'nonce'               => 'valid',
				'posted'              => array( 'layout' => 'col-two' ),
				'expected'            => array( 'layout' => 'col-two' ),
			),
			array(
				'test_condition_name' => '投稿者が他人（管理者）の投稿に nonce 付きで保存 => 保存されず既存の値のまま',
				'user_id'             => $author_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'valid',
				'posted'              => array( 'layout' => 'col-two' ),
				'expected'            => $saved,
			),
			array(
				'test_condition_name' => '購読者（編集権限なし）が nonce 付きで保存 => 保存されず既存の値のまま',
				'user_id'             => $subscriber_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'valid',
				'posted'              => array( 'layout' => 'col-two' ),
				'expected'            => $saved,
			),
			array(
				'test_condition_name' => 'nonce が無い => 保存されず既存の値のまま',
				'user_id'             => $admin_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'none',
				'posted'              => array( 'layout' => 'col-two' ),
				'expected'            => $saved,
			),
			array(
				'test_condition_name' => 'nonce のアクション名が違う => 保存されず既存の値のまま',
				'user_id'             => $admin_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'other',
				'posted'              => array( 'layout' => 'col-two' ),
				'expected'            => $saved,
			),
			array(
				'test_condition_name' => 'HTML タグを含む値 => タグを除いて保存',
				'user_id'             => $admin_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'valid',
				'posted'              => array( 'layout' => '<b>col-two</b>' ),
				'expected'            => array( 'layout' => 'col-two' ),
			),
			array(
				'test_condition_name' => 'バックスラッシュを含む値 => そのまま保存（二重にスラッシュ除去されない）',
				'user_id'             => $admin_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'valid',
				'posted'              => array( 'layout' => 'col\\two' ),
				'expected'            => array( 'layout' => 'col\\two' ),
			),
			array(
				'test_condition_name' => '入れ子の配列の値 => その項目だけ捨てて保存',
				'user_id'             => $admin_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'valid',
				'posted'              => array(
					'layout' => 'col-two',
					'nested' => array( 'a' => 'b' ),
				),
				'expected'            => array( 'layout' => 'col-two' ),
			),
			array(
				'test_condition_name' => '配列以外（文字列）が送信された => 保存されず既存の値のまま',
				'user_id'             => $admin_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'valid',
				'posted'              => 'col-two',
				'expected'            => $saved,
			),
			array(
				'test_condition_name' => 'フィールドが送信されない => 設定が削除される',
				'user_id'             => $admin_id,
				'post_id'             => $admin_post_id,
				'nonce'               => 'valid',
				'posted'              => null,
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			update_post_meta( $case['post_id'], '_lightning_design_setting', $saved );
			wp_set_current_user( $case['user_id'] );

			// nonce は現在のユーザーに紐づくため、ユーザーを切り替えてから作る.
			$_POST = array();
			if ( 'valid' === $case['nonce'] ) {
				$_POST['noncename__lightning_desigin'] = wp_create_nonce( 'lightning_design_setting' );
			} elseif ( 'other' === $case['nonce'] ) {
				$_POST['noncename__lightning_desigin'] = wp_create_nonce( 'other_action' );
			}

			// WordPress が $_POST に付けるスラッシュを再現する.
			if ( null !== $case['posted'] ) {
				$_POST['_lightning_design_setting'] = wp_slash( $case['posted'] );
			}

			lightning_design_setting_save( $case['post_id'] );

			$this->assertSame( $case['expected'], get_post_meta( $case['post_id'], '_lightning_design_setting', true ), $case['test_condition_name'] );

			delete_post_meta( $case['post_id'], '_lightning_design_setting' );
		}

		$_POST = array();
		wp_set_current_user( 0 );
	}
}
