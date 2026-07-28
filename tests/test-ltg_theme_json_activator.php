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
	 * テスト開始前のファイル名（theme.json / _theme.json のどちらか）
	 * テスト終了後はこのファイル名に必ず戻す
	 *
	 * @var string
	 */
	private static $original_theme_json_file_name = '';

	/**
	 * テストクラス実行前にファイル名の初期状態を記録する
	 *
	 * @return void
	 */
	public static function set_up_before_class() {
		parent::set_up_before_class();
		// 初期状態のファイル名を記録しておき、tear_down() でこの状態に戻す.
		self::$original_theme_json_file_name = self::detect_theme_json_file_name();
	}

	/**
	 * 各テスト終了後にファイル名を初期状態へ戻す
	 * アサーション失敗や例外で途中終了した場合も PHPUnit が必ず呼ぶため、
	 * リポジトリの状態が壊れたまま残らない
	 *
	 * @return void
	 */
	public function tear_down() {
		self::restore_theme_json();
		parent::tear_down();
	}

	/**
	 * 現在存在している theme.json 系ファイルの名前を取得する
	 *
	 * ファイル名の一覧と検出・復元のアルゴリズムは tests/theme-json-guard.php に
	 * 一本化してある。ここで再実装すると、ファイル名を変えたときに片方だけ直して
	 * 乖離するため、必ず委譲すること。
	 *
	 * @return string : 見つかったファイル名。見つからない場合は空文字.
	 */
	private static function detect_theme_json_file_name() {
		return ltg_test_detect_theme_json_file_name( get_template_directory() );
	}

	/**
	 * theme.json のファイル名をテスト開始前の状態に戻す
	 *
	 * @return void
	 */
	private static function restore_theme_json() {
		ltg_test_restore_theme_json_file_name( get_template_directory(), self::$original_theme_json_file_name );
	}

	/**
	 * theme.json を無効化状態（_theme.json）にする
	 * 「ファイルが存在しない場合」のテストの前準備として、
	 * 意図的にファイル名を _theme.json へ切り替えるために使う（後片付け用ではない）
	 *
	 * @return void
	 */
	private static function deactivate_theme_json_file() {
		if ( is_readable( get_template_directory() . '/theme.json' ) ) {
			rename( get_template_directory() . '/theme.json', get_template_directory() . '/_theme.json' );
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

		// 次のテストの前準備として、意図的に無効化状態（_theme.json）にする.
		self::deactivate_theme_json_file();

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

		// 後片付けは tear_down() の restore_theme_json() が行う.
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
				rename( $file_need_rename, $file_before );
			}

			// Do update option.
			foreach ( $value['update_option'] as $option_key => $option_value ) {
				update_option( $option_key, $option_value );
			}

			$actual = is_readable( get_template_directory() . '/' . $value['expacted'] );
			$this->assertTrue( $actual );
		}
		// 後片付けは tear_down() の restore_theme_json() が行う.
	}
}
