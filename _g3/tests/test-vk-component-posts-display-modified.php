<?php
/**
 * VK_Component_Posts の display_modified（更新日表示）オプションのテスト
 *
 * vk-component 1.7.0 で追加された「更新日表示（display_modified）」を
 * テーマのテンプレートが使うレイアウト（media / card-intext / card-horizontal）で
 * 期待通りに描画できるかを検証する。
 *
 * @package Lightning G3
 */

use VektorInc\VK_Component\VK_Component_Posts;

/**
 * VK_Component_Posts_Display_Modified_Test
 */
class VK_Component_Posts_Display_Modified_Test extends WP_UnitTestCase {

	/**
	 * VK_Component_Posts::get_view() が display_modified を反映するか
	 *
	 * @return void
	 */
	public function test_get_view() {

		// テスト用の投稿を1件作成（このIDを各ケースで使い回す）.
		$post_id = wp_insert_post(
			array(
				'post_title'   => 'display_modified テスト投稿',
				'post_status'  => 'publish',
				'post_content' => 'content',
			)
		);
		// 投稿が正常に作成されたことを確認（0 や WP_Error でないこと）.
		$this->assertIsInt( $post_id );
		$this->assertGreaterThan( 0, $post_id );
		$post = get_post( $post_id );

		// 各レイアウト共通の基本オプション（テンプレートの指定に合わせた最小構成）.
		$base_options = array(
			'display_image'              => false,
			'display_image_overlay_term' => false,
			'display_excerpt'            => false,
			'display_new'                => false,
			'display_btn'                => false,
		);

		// テストの配列（条件と、出力HTMLに含まれる／含まれないべきマーカーをまとめて登録）.
		$test_array = array(
			array(
				// 更新日ありの基本ケース（media レイアウト）.
				'test_condition_name' => 'media レイアウトで display_modified が true の場合 => 更新日のマークアップが出力される',
				'options'             => array(
					'layout'           => 'media',
					'display_date'     => true,
					'display_modified' => true,
				),
				// マーカー => 含まれるべきか（true: 含む / false: 含まない）.
				'assertions'          => array(
					'-date modified'       => true,
					'fa-clock-rotate-left' => true,
				),
			),
			array(
				// 公開日と更新日の両方を表示するケース（card-intext レイアウト）.
				'test_condition_name' => 'card-intext で display_date と display_modified が両方 true の場合 => 公開日と更新日の両方が出力される',
				'options'             => array(
					'layout'           => 'card-intext',
					'display_date'     => true,
					'display_modified' => true,
				),
				'assertions'          => array(
					'-date published' => true,
					'-date modified'  => true,
				),
			),
			array(
				// テーマの現状（display_modified を false で明示）= 更新日は出ない.
				'test_condition_name' => 'media レイアウトで display_modified が false の場合 => 更新日のマークアップは出力されない',
				'options'             => array(
					'layout'           => 'media',
					'display_date'     => true,
					'display_modified' => false,
				),
				'assertions'          => array(
					'-date published' => true,
					'-date modified'  => false,
				),
			),
			array(
				// 境界値: 公開日も更新日も非表示なら日付ブロック自体が出力されない.
				'test_condition_name' => 'card-horizontal で display_date も display_modified も false の場合 => 日付ブロックが出力されない',
				'options'             => array(
					'layout'           => 'card-horizontal',
					'display_date'     => false,
					'display_modified' => false,
				),
				'assertions'          => array(
					'vk_post_dates' => false,
				),
			),
		);

		foreach ( $test_array as $case ) {
			// 基本オプションにケース固有のオプションをマージ.
			$options = array_merge( $base_options, $case['options'] );

			// 描画結果のHTMLを取得（the_view ではなく get_view で生のHTMLを取得）.
			$actual = VK_Component_Posts::get_view( $post, $options );

			// マーカー毎に、含まれる／含まれないを検証.
			foreach ( $case['assertions'] as $marker => $should_contain ) {
				if ( $should_contain ) {
					$this->assertStringContainsString( $marker, $actual, $case['test_condition_name'] . ' / ' . $marker );
				} else {
					$this->assertStringNotContainsString( $marker, $actual, $case['test_condition_name'] . ' / ' . $marker );
				}
			}
		}

		// 後始末: テスト用投稿を削除.
		wp_delete_post( $post_id, true );
	}
}
