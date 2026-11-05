<?php
/**
 * Test for register_post_meta schema of _lightning_design_setting.
 * _lightning_design_setting の REST スキーマ検証テスト。
 *
 * スキーマ外のキー（プラグインや旧バージョンが保存したもの）を持つ投稿で
 * REST API 経由の更新が失敗しないことを確認する。
 *
 * @package lightning
 * @see https://github.com/vektor-inc/lightning/issues/1318
 */

class RegisterDesignSettingMetaTest extends WP_UnitTestCase {

	/**
	 * Test that REST API update succeeds with various _lightning_design_setting values.
	 * さまざまな _lightning_design_setting 値で REST 更新が成功することをテストする。
	 */
	public function test_lightning_g3_register_design_setting_meta() {

		// テスト用の固定ページを作成する。
		// Create a test page.
		$post_id = self::factory()->post->create(
			array(
				'post_type'   => 'page',
				'post_status' => 'publish',
			)
		);

		// テスト用の管理者ユーザーを作成してログインする（REST メタ更新には権限が必要）。
		// Create an admin test user and log in (REST meta update requires capability).
		$admin_id = self::factory()->user->create( array( 'role' => 'administrator' ) );
		wp_set_current_user( $admin_id );

		$test_cases = array(
			// 正常系: G3 スキーマ内のキーのみ。
			array(
				'test_condition_name' => 'G3 スキーマ内のキー layout のみの場合 => 200',
				'meta_value'         => array( 'layout' => 'col-two' ),
				'expected'           => 200,
			),
			// 正常系: メタが空配列の場合。
			array(
				'test_condition_name' => 'メタが空配列の場合 => 200',
				'meta_value'         => array(),
				'expected'           => 200,
			),
			// 正常系: layout + site_body_padding（G3 スキーマ内のみ）。
			array(
				'test_condition_name' => 'G3 スキーマの layout + site_body_padding のみの場合 => 200',
				'meta_value'         => array(
					'layout'            => 'col-one',
					'site_body_padding' => 'true',
				),
				'expected'           => 200,
			),
			// 異常系（修正前は 500 だった）: Pro Unit 由来の section_base + header_trans。
			// Path A: クラシックエディタ経由で Pro Unit が保存するキー。
			array(
				'test_condition_name' => 'Pro Unit 由来の section_base + header_trans がある場合 => 200（Path A）',
				'meta_value'         => array(
					'layout'       => 'default',
					'section_base' => 'use',
					'header_trans' => 'true',
				),
				'expected'           => 200,
			),
			// 異常系（修正前は 500 だった）: G2 時代の旧キー。
			// Path B: G2 時代のレイアウト設定が残存。
			array(
				'test_condition_name' => 'G2 旧キー hidden_page_header + hidden_breadcrumb がある場合 => 200（Path B）',
				'meta_value'         => array(
					'hidden_page_header' => 'true',
					'hidden_breadcrumb'  => 'true',
				),
				'expected'           => 200,
			),
			// 異常系（修正前は 500 だった）: 正当キー + G2 旧キーの混在。
			// Path B-3: layout が正当でも旧キーが混ざるだけで全体 NG だった。
			array(
				'test_condition_name' => 'G3 正当キー layout + G2 旧キー hidden_page_header の混在 => 200（Path B-3）',
				'meta_value'         => array(
					'layout'             => 'col-two',
					'hidden_page_header' => 'true',
				),
				'expected'           => 200,
			),
			// 異常系（修正前は 500 だった）: 後方互換マイグレーション前の旧キー。
			// Path C: hidden_page_header_and_breadcrumb（マイグレーション前）。
			array(
				'test_condition_name' => '後方互換変換前の旧キー hidden_page_header_and_breadcrumb がある場合 => 200（Path C）',
				'meta_value'         => array(
					'hidden_page_header_and_breadcrumb' => 'true',
				),
				'expected'           => 200,
			),
		);

		foreach ( $test_cases as $case ) {
			// メタ値をセットする。
			// Set the meta value.
			update_post_meta( $post_id, '_lightning_design_setting', $case['meta_value'] );

			// REST PUT で null を送信する（ブロックエディタが変更なしで更新する場合のシミュレーション）。
			// Simulate Gutenberg save without changes (sends null for unchanged meta).
			$request = new WP_REST_Request( 'POST', '/wp/v2/pages/' . $post_id );
			$request->set_body( wp_json_encode( array( 'meta' => array( '_lightning_design_setting' => null ) ) ) );
			$request->set_header( 'content-type', 'application/json' );
			$response = rest_get_server()->dispatch( $request );

			$this->assertEquals(
				$case['expected'],
				$response->get_status(),
				$case['test_condition_name']
			);

			// メタをクリーンアップする。
			// Clean up meta.
			delete_post_meta( $post_id, '_lightning_design_setting' );
		}
	}

	/**
	 * lightning_sanitize_design_setting() がデザイン設定の入力値を文字列として整形することをテストする。
	 */
	public function test_lightning_sanitize_design_setting() {
		$test_cases = array(
			array(
				'test_condition_name' => '文字列の値だけの配列 => そのまま',
				'input'               => array(
					'layout'            => 'col-two',
					'site_body_padding' => 'true',
				),
				'expected'            => array(
					'layout'            => 'col-two',
					'site_body_padding' => 'true',
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
					'layout'            => 'col-two',
					'site_body_padding' => 'true',
				),
				'expected'            => array(
					'layout'            => 'col-two',
					'site_body_padding' => 'true',
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
