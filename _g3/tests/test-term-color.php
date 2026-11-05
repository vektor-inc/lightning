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
	 * save_term_meta_color() が nonce を確認したうえで、送信された色を整形して保存することを確認する.
	 *
	 * @return void
	 */
	public function test_save_term_meta_color() {
		$test_cases = array(
			array(
				'test_condition_name' => 'nonce 付きで #ff0000 を送信 => ff0000 が保存される',
				'nonce'               => true,
				'term_color'          => '#ff0000',
				'expected'            => 'ff0000',
			),
			array(
				'test_condition_name' => 'nonce 付きで 3 桁の #abc を送信 => abc が保存される',
				'nonce'               => true,
				'term_color'          => '#abc',
				'expected'            => 'abc',
			),
			array(
				'test_condition_name' => 'nonce が無い => 既存の値のまま',
				'nonce'               => false,
				'term_color'          => '#ff0000',
				'expected'            => '00ff00',
			),
			array(
				'test_condition_name' => 'term_color が送信されない => 既存の値のまま',
				'nonce'               => true,
				'term_color'          => null,
				'expected'            => '00ff00',
			),
			array(
				'test_condition_name' => '配列を送信 => 警告を出さずに空文字が保存される',
				'nonce'               => true,
				'term_color'          => array( '#ff0000' ),
				'expected'            => '',
			),
			array(
				'test_condition_name' => '属性からの脱出を狙った値を送信 => 空文字が保存される',
				'nonce'               => true,
				'term_color'          => '" onmouseover="alert(1)//aaa',
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			$term_id = self::factory()->term->create( array( 'taxonomy' => 'category' ) );
			update_term_meta( $term_id, 'term_color', '#00ff00' );

			// WordPress が $_POST に付けるスラッシュを再現する.
			$_POST = array();
			if ( $case['nonce'] ) {
				$_POST['term_color_nonce'] = wp_create_nonce( 'class.term-color.php' );
			}
			if ( null !== $case['term_color'] ) {
				$_POST['term_color'] = wp_slash( $case['term_color'] );
			}

			// 管理画面での保存と同じく edited_category フック経由で呼び出す.
			wp_update_term( $term_id, 'category', array( 'description' => 'updated' ) );

			$this->assertSame( $case['expected'], get_term_meta( $term_id, 'term_color', true ), $case['test_condition_name'] );
		}

		$_POST = array();
	}
}
