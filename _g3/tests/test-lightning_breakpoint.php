<?php
/**
 * Test for lightning_get_breakpoints() / lightning_get_breakpoint() / lightning_the_breakpoint()
 *
 * @package vektor-inc/lightning
 */

/**
 * LightningBreakpointTest
 */
class LightningBreakpointTest extends WP_UnitTestCase {

	/**
	 * 既定のブレイクポイント一覧
	 *
	 * @var array
	 */
	const DEFAULT_BREAKPOINTS = array(
		'xs-max' => '576px',
		'sm-max' => '768px',
		'md-max' => '992px',
		'lg-max' => '1200px',
		'xl-max' => '1400px',
	);

	/**
	 * テストケースで指定された上書き内容を lightning_breakpoints フィルターに登録する
	 *
	 * @param array $overrides キーと値の連想配列。空の場合はフィルターを登録しない.
	 * @return void
	 */
	private function set_breakpoints_filter( $overrides ) {
		if ( empty( $overrides ) ) {
			return;
		}
		add_filter(
			'lightning_breakpoints',
			function ( $breakpoints ) use ( $overrides ) {
				return array_merge( $breakpoints, $overrides );
			}
		);
	}

	/**
	 * Check lightning_get_breakpoints()
	 *
	 * @return void
	 */
	public function test_lightning_get_breakpoints() {

		$test_cases = array(
			array(
				'test_condition_name' => 'フィルターなしの場合 => 既定の5件を返す',
				'conditions'          => array(
					'filter_overrides' => array(),
				),
				'expected'            => array(
					'xs-max' => '576px',
					'sm-max' => '768px',
					'md-max' => '992px',
					'lg-max' => '1200px',
					'xl-max' => '1400px',
				),
			),
			array(
				'test_condition_name' => 'フィルターで md-max を 1000px に上書きした場合 => md-max だけ 1000px になる',
				'conditions'          => array(
					'filter_overrides' => array(
						'md-max' => '1000px',
					),
				),
				'expected'            => array(
					'xs-max' => '576px',
					'sm-max' => '768px',
					'md-max' => '1000px',
					'lg-max' => '1200px',
					'xl-max' => '1400px',
				),
			),
			array(
				'test_condition_name' => 'フィルターで独自キー xxl-max を追加した場合 => 既定の5件に追加されて6件になる',
				'conditions'          => array(
					'filter_overrides' => array(
						'xxl-max' => '1600px',
					),
				),
				'expected'            => array(
					'xs-max'  => '576px',
					'sm-max'  => '768px',
					'md-max'  => '992px',
					'lg-max'  => '1200px',
					'xl-max'  => '1400px',
					'xxl-max' => '1600px',
				),
			),
		);

		foreach ( $test_cases as $case ) {
			$this->set_breakpoints_filter( $case['conditions']['filter_overrides'] );

			$actual = lightning_get_breakpoints();

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );

			remove_all_filters( 'lightning_breakpoints' );
		}
	}

	/**
	 * Check lightning_get_breakpoint()
	 *
	 * @return void
	 */
	public function test_lightning_get_breakpoint() {

		// 未定義のキーを渡すケースで _doing_it_wrong() が呼ばれるため、想定内として登録する.
		$this->setExpectedIncorrectUsage( 'lightning_get_breakpoint' );

		$test_cases = array(
			array(
				'test_condition_name' => 'キーが xs-max の場合 => 576px',
				'conditions'          => array(
					'key'              => 'xs-max',
					'filter_overrides' => array(),
				),
				'expected'            => '576px',
			),
			array(
				'test_condition_name' => 'キーが md-max の場合 => 992px',
				'conditions'          => array(
					'key'              => 'md-max',
					'filter_overrides' => array(),
				),
				'expected'            => '992px',
			),
			array(
				'test_condition_name' => 'キーが xl-max の場合 => 1400px',
				'conditions'          => array(
					'key'              => 'xl-max',
					'filter_overrides' => array(),
				),
				'expected'            => '1400px',
			),
			array(
				'test_condition_name' => 'フィルターで md-max を 1000px に上書きした場合 => 1000px',
				'conditions'          => array(
					'key'              => 'md-max',
					'filter_overrides' => array(
						'md-max' => '1000px',
					),
				),
				'expected'            => '1000px',
			),
			array(
				'test_condition_name' => '未定義のキー foo-max の場合 => 空文字（不正なメディアクエリを作らないため）',
				'conditions'          => array(
					'key'              => 'foo-max',
					'filter_overrides' => array(),
				),
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			$this->set_breakpoints_filter( $case['conditions']['filter_overrides'] );

			$actual = lightning_get_breakpoint( $case['conditions']['key'] );

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );

			remove_all_filters( 'lightning_breakpoints' );
		}
	}

	/**
	 * Check lightning_the_breakpoint()
	 *
	 * @return void
	 */
	public function test_lightning_the_breakpoint() {

		// 未定義のキーを渡すケースで _doing_it_wrong() が呼ばれるため、想定内として登録する.
		$this->setExpectedIncorrectUsage( 'lightning_get_breakpoint' );

		$test_cases = array(
			array(
				'test_condition_name' => 'キーが sm-max の場合 => 768px を出力する',
				'conditions'          => array(
					'key'              => 'sm-max',
					'filter_overrides' => array(),
				),
				'expected'            => '768px',
			),
			array(
				'test_condition_name' => 'キーが lg-max の場合 => 1200px を出力する',
				'conditions'          => array(
					'key'              => 'lg-max',
					'filter_overrides' => array(),
				),
				'expected'            => '1200px',
			),
			array(
				'test_condition_name' => 'フィルターで lg-max を 1100px に上書きした場合 => 1100px を出力する',
				'conditions'          => array(
					'key'              => 'lg-max',
					'filter_overrides' => array(
						'lg-max' => '1100px',
					),
				),
				'expected'            => '1100px',
			),
			array(
				'test_condition_name' => '未定義のキー foo-max の場合 => 何も出力しない',
				'conditions'          => array(
					'key'              => 'foo-max',
					'filter_overrides' => array(),
				),
				'expected'            => '',
			),
		);

		foreach ( $test_cases as $case ) {
			$this->set_breakpoints_filter( $case['conditions']['filter_overrides'] );

			ob_start();
			lightning_the_breakpoint( $case['conditions']['key'] );
			$actual = ob_get_clean();

			$this->assertSame( $case['expected'], $actual, $case['test_condition_name'] );

			remove_all_filters( 'lightning_breakpoints' );
		}
	}
}
