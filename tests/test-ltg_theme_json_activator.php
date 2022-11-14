<?php
/**
 * Class LTG_Theme_Json_Activator_Test
 *
 * @package vektor-inc/lightning
 */

/**
 * LTG_Theme_Json_Activator_Test.
 */
class LTG_Theme_Json_Activator_Test extends WP_UnitTestCase {

	/**
	 * Reset theme json
	 * テーマの theme.json は初期状態では _theme.json でなくてはならない。
	 * テストの過程で theme.json に変更された場合、テスト後に _theme.json に戻す
	 *
	 * @return void
	 */
	public static function reset_theme_json() {
		if ( is_readable( get_template_directory() . '/theme.json' ) ) {
			$file_before = rename( get_template_directory() . '/theme.json', get_template_directory() . '/_theme.json' );
		}
	}

	/**
	 * LTG_Theme_Json_Activator test
	 *
	 * ファイルの書き換えが正常に実行されるかどうかをテスト
	 */
	public function test_LTG_Theme_Json_Activator() {
		$test_array = array(
			// Lightning を初めてインストールする場合.
			// lightning_theme_options 自体まだ存在しない.
			array(
				'lightning_theme_options' => null,
				'expected'                => true,
			),
			// 既存の Lightning のサイト（まだ theme.json を有効化していない）.
			// まだ lightning_theme_options はあるが lightning_theme_options[theme_json] は存在しない.
			array(
				'lightning_theme_options' => array(
					'sample' => true,
				),
				'expected'                => false,
			),
			// 手動で theme.json を有効化した場合.
			array(
				'lightning_theme_options' => array(
					'theme_json' => true,
				),
				'expected'                => true,
			),
			// 手動で theme.json を無効化した場合.
			array(
				'lightning_theme_options' => array(
					'theme_json' => false,
				),
				'expected'                => false,
			),
		);
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_Theme_Json_Activator' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		foreach ( $test_array as $key => $value ) {

			if ( ! empty( $value['lightning_theme_options'] ) ) {
				update_option( 'lightning_theme_options', $value['lightning_theme_options'] );
			} else {
				delete_option( 'lightning_theme_options' );
			}

			// とりあえず判定が正しいかどうかを確認.
			$this->assertEquals( $value['expected'], LTG_Theme_Json_Activator::is_theme_json() );

			// ファイル名が正しいかどうかを確認.
			$actual = LTG_Theme_Json_Activator::rename_theme_json();

			if ( $value['expected'] ) {
				$expected_rename = 'theme.json';
			} else {
				$expected_rename = '_theme.json';
			}

			print 'return  :' . esc_attr( $actual ) . PHP_EOL;
			print 'expected :' . esc_attr( $expected_rename ) . PHP_EOL;
			$this->assertEquals( $expected_rename, $actual );
		}

		self::reset_theme_json();

		/*******************************************
		 * Lightningの中に theme.json 用のファイルがない場合のテスト
		 */

		// Change theme.json file name for no file test.
		$rename = rename( get_template_directory() . '/_theme.json', get_template_directory() . '/no_theme.json' );
		if ( $rename ) {
			$actual = LTG_Theme_Json_Activator::rename_theme_json();
			$this->assertEquals( 'Missing theme.json file.', $actual );
			// Set back file name.
			$rename = rename( get_template_directory() . '/no_theme.json', get_template_directory() . '/_theme.json' );
		}

	}

	/**
	 * オプション値のアップデートで lightning_theme_options を保存された場合のみファイルの書き換えが実行されるかどうか
	 */
	public function test_option_update_rename() {
		$test_array = array(
			// Do not rename case.
			// lightning_theme_options じゃない保存値の場合はリネーム処理を実行しない.
			array(
				// 本当は有効化設定.
				'lightning_theme_options' => array(
					'theme_json' => true,
				),
				// ファイルは無効化状態(_theme.json).
				'before_theme_json_file'  => false,
				// テストでアップデート対象のオプション.
				'update_option'           => array(
					'test_option' => 'test_value',
				),
				// 実行後のファイル名 : lightning_theme_options の保存ではないので変更しない.
				'expacted'                => '_theme.json',
			),
			// Rename case.
			// lightning_theme_options のアップデートの場合にファイル名の書き換えを実行.
			array(
				'lightning_theme_options' => array(
					'theme_json' => false,
				),
				// テスト実行前に _theme.json に設定.
				'before_theme_json_file'  => false,
				'update_option'           => array(
					'lightning_theme_options' => array(
						'theme_json' => true,
					),
				),
				'expacted'                => 'theme.json',
			),

		);

		foreach ( $test_array as $value ) {
			// とりあえずテストする前の lightning_theme_options をセット.
			update_option( 'lightning_theme_options', $value['lightning_theme_options'] );

			// テスト実行前の theme.json のファイル名に設定.
			if ( $value['before_theme_json_file'] ) {
				$file_before      = get_template_directory() . '/theme.json';
				$file_need_rename = get_template_directory() . '/_theme.json';
			} else {
				$file_before      = get_template_directory() . '/_theme.json';
				$file_need_rename = get_template_directory() . '/theme.json';
			}

			if ( ! is_readable( $file_before ) ) {
				$file_before = rename( $file_need_rename, $file_before );
			}

			// Do update option.
			foreach ( $value['update_option'] as $option_key => $option_value ) {
				update_option( $option_key, $option_value );
			}

			$actual = is_readable( get_template_directory() . '/' . $value['expacted'] );
			$this->assertTrue( $actual );

		}
		self::reset_theme_json();
	}
}
