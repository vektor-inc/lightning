<?php
/**
 * Class TermColorSanitizeTest
 *
 * @package vektor-inc/lightning
 */

class TermColorSanitizeTest extends WP_UnitTestCase {

	/**
	 * sanitize_hex() が3桁・6桁の16進数カラー値のみを受け付けることを確認する.
	 *
	 * @return void
	 */
	public function test_sanitize_hex() {
		$cases = array(
			array( '#ff0000', 'ff0000' ),
			array( '#abc', 'abc' ),
			// 旧正規表現には先頭アンカー（^）が無く末尾だけを見ていたため、16進数に見える
			// '000' で終わるこの文字列が誤って許可されていた。現在は拒否されることを確認する.
			array( 'notacolor000', '' ),
			// 属性からの脱出を狙った値も、末尾が16進数に見えるだけでは許可しないことを確認する.
			array( '" onmouseover="alert(1)//aaa', '' ),
			array( '#ff0000ff', '' ),
			// PCRE の $ は末尾の改行の直前にもマッチするため、末尾に改行を付けた値が
			// サニタイズを素通りしていた。現在は拒否されることを確認する.
			array( "abc\n", '' ),
		);

		foreach ( $cases as [ $input, $expected ] ) {
			$this->assertSame( $expected, Vk_term_color::sanitize_hex( $input ), "input: {$input}" );
		}
	}

	/**
	 * 配列など非文字列値を渡しても TypeError にならず、空文字として拒否されることを確認する.
	 *
	 * @return void
	 */
	public function test_sanitize_hex_rejects_non_string_without_error() {
		$this->assertSame( '', Vk_term_color::sanitize_hex( array( '#ff0000' ) ) );
	}

	/**
	 * term_color に登録された sanitize_callback が、term meta 保存時に実際に適用されることを確認する.
	 *
	 * @return void
	 */
	public function test_term_color_meta_save_sanitizes_invalid_value() {
		$term_id = self::factory()->term->create();

		update_term_meta( $term_id, 'term_color', 'notacolor000' );
		$this->assertSame( '', get_term_meta( $term_id, 'term_color', true ) );

		update_term_meta( $term_id, 'term_color', '#00ff00' );
		$this->assertSame( '00ff00', get_term_meta( $term_id, 'term_color', true ) );
	}

	/**
	 * save_term_meta_color() が nonce とタームの編集権限を確認したうえで、送信された色を整形して保存することを確認する.
	 * 管理画面での保存と同じく wp_update_term() → edited_category フック経由（route = update）と、
	 * 新規ターム作成と同じく wp_insert_term() → create_category フック経由（route = insert）で呼び出し、フックの配線ごと検証する.
	 *
	 * @return void
	 */
	public function test_save_term_meta_color() {
		// カテゴリーの編集権限（manage_categories）は管理者にはあり、投稿者・購読者には無い.
		$admin_id      = self::factory()->user->create( array( 'role' => 'administrator' ) );
		$author_id     = self::factory()->user->create( array( 'role' => 'author' ) );
		$subscriber_id = self::factory()->user->create( array( 'role' => 'subscriber' ) );

		// 各ケースの前に入れておく保存済みの値（sanitize_hex を通るため # は除かれて保存される）.
		$saved = '00ff00';

		$test_cases = array(
			array(
				'test_condition_name' => '管理者が nonce 付きで #ff0000 を送信 => ff0000 が保存される',
				'route'               => 'update',
				'user_id'             => $admin_id,
				'nonce'               => true,
				'term_color'          => '#ff0000',
				'expected'            => 'ff0000',
			),
			array(
				'test_condition_name' => '管理者が nonce 付きで 3 桁の #abc を送信 => abc が保存される',
				'route'               => 'update',
				'user_id'             => $admin_id,
				'nonce'               => true,
				'term_color'          => '#abc',
				'expected'            => 'abc',
			),
			array(
				'test_condition_name' => '投稿者（カテゴリーの編集権限なし）が nonce 付きで送信 => 保存されず既存の値のまま',
				'route'               => 'update',
				'user_id'             => $author_id,
				'nonce'               => true,
				'term_color'          => '#ff0000',
				'expected'            => $saved,
			),
			array(
				'test_condition_name' => '購読者（カテゴリーの編集権限なし）が nonce 付きで送信 => 保存されず既存の値のまま',
				'route'               => 'update',
				'user_id'             => $subscriber_id,
				'nonce'               => true,
				'term_color'          => '#ff0000',
				'expected'            => $saved,
			),
			array(
				'test_condition_name' => 'nonce が無い => 既存の値のまま',
				'route'               => 'update',
				'user_id'             => $admin_id,
				'nonce'               => false,
				'term_color'          => '#ff0000',
				'expected'            => $saved,
			),
			array(
				'test_condition_name' => 'term_color が送信されない => 既存の値のまま',
				'route'               => 'update',
				'user_id'             => $admin_id,
				'nonce'               => true,
				'term_color'          => null,
				'expected'            => $saved,
			),
			array(
				'test_condition_name' => '配列を送信 => 警告を出さずに空文字が保存される',
				'route'               => 'update',
				'user_id'             => $admin_id,
				'nonce'               => true,
				'term_color'          => array( '#ff0000' ),
				'expected'            => '',
			),
			array(
				'test_condition_name' => '属性からの脱出を狙った値を送信 => 空文字が保存される',
				'route'               => 'update',
				'user_id'             => $admin_id,
				'nonce'               => true,
				'term_color'          => '" onmouseover="alert(1)//aaa',
				'expected'            => '',
			),
			array(
				'test_condition_name' => '管理者が nonce 付きで #ff0000 を送信して新規ターム作成 => 新規タームに ff0000 が保存される',
				'route'               => 'insert',
				'user_id'             => $admin_id,
				'nonce'               => true,
				'term_color'          => '#ff0000',
				'expected'            => 'ff0000',
			),
			array(
				'test_condition_name' => '購読者（カテゴリーの編集権限なし）が nonce 付きで送信して新規ターム作成 => 保存されず空のまま',
				'route'               => 'insert',
				'user_id'             => $subscriber_id,
				'nonce'               => true,
				'term_color'          => '#ff0000',
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			// ターム作成時にも create_category 経由で save_term_meta_color() が走るため、前のケースの $_POST を先に空にする.
			$_POST = array();

			// update 経路は保存済みの値を持つタームを先に用意する（insert 経路は wp_insert_term() 自体が検証対象）.
			$term_id = 0;
			if ( 'update' === $case['route'] ) {
				$term_id = self::factory()->term->create( array( 'taxonomy' => 'category' ) );
				update_term_meta( $term_id, 'term_color', '#' . $saved );
			}

			// nonce は現在のユーザーに紐づくため、ユーザーを切り替えてから作る.
			wp_set_current_user( $case['user_id'] );
			if ( $case['nonce'] ) {
				$_POST['term_color_nonce'] = wp_create_nonce( 'class.term-color.php' );
			}

			// WordPress が $_POST に付けるスラッシュを再現する.
			if ( null !== $case['term_color'] ) {
				$_POST['term_color'] = wp_slash( $case['term_color'] );
			}

			if ( 'insert' === $case['route'] ) {
				// 管理画面の「新規カテゴリーを追加」と同じく create_category フック経由で呼び出す.
				$inserted = wp_insert_term( uniqid( 'term-color-insert-' ), 'category' );
				$this->assertNotWPError( $inserted, $case['test_condition_name'] . '（前提: タームが作成できること）' );
				$term_id = $inserted['term_id'];
			} else {
				// 管理画面での保存と同じく edited_category フック経由で呼び出す.
				wp_update_term( $term_id, 'category', array( 'description' => 'updated' ) );
			}

			$this->assertSame( $case['expected'], get_term_meta( $term_id, 'term_color', true ), $case['test_condition_name'] );
		}

		$_POST = array();
		wp_set_current_user( 0 );
	}
}
