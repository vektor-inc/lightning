<?php
/**
 * Lightning_Header_Scrool_Typo_Compatible_Test
 *
 * `lightning_header_scrool_typo_compatible()` のテスト。
 * 新キー（正スペル）`header_scroll` を旧キー（内部レガシー）`header_scrool` に
 * コピーする後方互換 hook の挙動を検証する。
 *
 * @package vektor-inc/lightning
 */

/**
 * Class Lightning_Header_Scrool_Typo_Compatible_Test
 */
class Lightning_Header_Scrool_Typo_Compatible_Test extends WP_UnitTestCase {

	/**
	 * lightning_header_scrool_typo_compatible() の動作テスト。
	 *
	 * 各ケースで `lightning_localize_options` フィルタへの入力（$options）と
	 * 期待される出力（$expected）を配列で定義し、ループで一括検証する。
	 */
	public function test_lightning_header_scrool_typo_compatible() {

		$test_cases = array(
			array(
				'test_condition_name' => '新キー header_scroll = false を指定した場合、旧キー header_scrool にも false がコピーされる',
				'options'             => array(
					'header_scroll' => false,
				),
				'expected'            => array(
					'header_scroll' => false,
					'header_scrool' => false,
				),
			),
			array(
				'test_condition_name' => '新キー header_scroll = true を指定した場合、旧キー header_scrool にも true がコピーされる',
				'options'             => array(
					'header_scroll' => true,
				),
				'expected'            => array(
					'header_scroll' => true,
					'header_scrool' => true,
				),
			),
			array(
				'test_condition_name' => '新キー header_scroll が無い場合、旧キー header_scrool はそのまま改変されない',
				'options'             => array(
					'header_scrool' => true,
				),
				'expected'            => array(
					'header_scrool' => true,
				),
			),
			array(
				'test_condition_name' => '旧キーと新キーの両方が指定された場合、新キー（正スペル）の値が後勝ちで旧キーに上書きされる',
				'options'             => array(
					'header_scrool' => true,
					'header_scroll' => false,
				),
				'expected'            => array(
					'header_scrool' => false,
					'header_scroll' => false,
				),
			),
			array(
				'test_condition_name' => '空配列を渡した場合、空配列のまま返る（新キーが無いので何もしない）',
				'options'             => array(),
				'expected'            => array(),
			),
			array(
				'test_condition_name' => '非配列（null）を渡した場合、防御的チェックでそのまま返る',
				'options'             => null,
				'expected'            => null,
			),
			array(
				'test_condition_name' => '非配列（文字列）を渡した場合、防御的チェックでそのまま返る',
				'options'             => 'invalid_string',
				'expected'            => 'invalid_string',
			),
			array(
				'test_condition_name' => 'issue #1326 シナリオ: design-skin が priority 10 で旧キー true を入れた後、ユーザーが priority 11 で新キー false を入れた場合、最終的に旧キーが false に上書きされる',
				// この最終 options は「design-skin の priority 10（旧キー true）→ ユーザーフィルタ priority 11（新キー false）」が
				// 順に適用された後の状態を再現したもの。compat hook は priority PHP_INT_MAX で実行されるので
				// このタイミングでは両キーが共存している。
				'options'             => array(
					'header_scrool' => true,
					'header_scroll' => false,
				),
				'expected'            => array(
					'header_scrool' => false,
					'header_scroll' => false,
				),
			),
		);

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_header_scrool_typo_compatible()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_cases as $case ) {
			// 対象関数を直接呼び出して結果を取得する。
			$actual = lightning_header_scrool_typo_compatible( $case['options'] );

			// 期待値と一致するかを検証する。
			$this->assertEquals( $case['expected'], $actual, $case['test_condition_name'] );
		}
	}

	/**
	 * フィルタチェーン全体での挙動を検証する統合テスト。
	 *
	 * `lightning_localize_options` フィルタを実際に発火させ、
	 * 後方互換 hook が priority PHP_INT_MAX で最終段に動作することを確認する。
	 * issue #1326 のシナリオ（design-skin が priority 10 で旧キー true、
	 * ユーザーが priority 11 で新キー false）を再現する。
	 */
	public function test_lightning_header_scrool_typo_compatible_via_filter_chain() {

		// design-skin 相当: priority 10 で旧キー header_scrool に true をセットする。
		$design_skin_filter = function ( $options ) {
			$options['header_scrool'] = true;
			return $options;
		};
		add_filter( 'lightning_localize_options', $design_skin_filter, 10, 1 );

		// ユーザー追加コード相当: priority 11 で新キー header_scroll に false をセットする。
		$user_filter = function ( $options ) {
			$options['header_scroll'] = false;
			return $options;
		};
		add_filter( 'lightning_localize_options', $user_filter, 11, 1 );

		try {
			// 実際のフィルタを発火させる。
			$result = apply_filters( 'lightning_localize_options', array() );

			// 後方互換 hook により、最終的に旧キー header_scrool が false に上書きされていることを確認する。
			$this->assertArrayHasKey( 'header_scrool', $result, 'header_scrool キーが結果に含まれること' );
			$this->assertFalse( $result['header_scrool'], 'issue #1326 シナリオで旧キー header_scrool が新キー header_scroll の値 false で後勝ち上書きされる' );
			$this->assertFalse( $result['header_scroll'], '新キー header_scroll は false のまま維持される' );
		} finally {
			// 後処理: 追加したフィルタを削除する（他テストへの副作用防止）。
			// アサーション失敗で例外が出ても remove_filter を確実に実行する。
			remove_filter( 'lightning_localize_options', $design_skin_filter, 10 );
			remove_filter( 'lightning_localize_options', $user_filter, 11 );
		}
	}
}
