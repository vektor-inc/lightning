<?php
/**
 * テスト実行で theme.json のファイル名が変わったまま残るのを防ぐガード
 *
 * LTG_Theme_Json_Activator は 'updated_option' フックで theme.json と _theme.json を
 * リネームするため、テスト中に lightning_theme_options を更新すると副作用で
 * ファイル名が変わる。リポジトリが追跡しているのは theme.json なので、
 * そのままだとテストを実行しただけで theme.json が消えた状態になってしまう。
 *
 * ここでテスト開始時点のファイル名を記録し、PHPUnit 終了時に必ず元の状態へ戻す。
 *
 * @package vektor-inc/lightning
 */

// 読み込むだけでファイル操作を登録するため、CLI（PHPUnit）以外では何もせずに終了する。
// 現状 tests/ は copy-files.js の excludedPaths でリリース zip から除外されており
// 公開サイトに置かれることはないが、除外設定が変わった場合の保険として残す.
if ( 'cli' !== PHP_SAPI && 'phpdbg' !== PHP_SAPI ) {
	return;
}

if ( ! function_exists( 'ltg_test_theme_json_file_names' ) ) {
	/**
	 * テストの過程で存在し得る theme.json 系ファイルの名前
	 *
	 * リポジトリが追跡しているのは theme.json だが、無効化状態では _theme.json 、
	 * 「ファイルが無い場合」のテスト中は no_theme.json になる。
	 *
	 * この一覧と下記の検出・復元は、テストクラス側
	 * （tests/test-ltg_theme_json_activator.php）からも呼ばれる。
	 * 実装を二重に持つとファイル名を変えたときに片方だけ直して乖離するため、
	 * 必ずここを唯一の定義とすること。
	 *
	 * @return string[]
	 */
	function ltg_test_theme_json_file_names() {
		return array( 'theme.json', '_theme.json', 'no_theme.json' );
	}
}

if ( ! function_exists( 'ltg_test_detect_theme_json_file_name' ) ) {
	/**
	 * 現在存在している theme.json 系ファイルの名前を取得する
	 *
	 * @param string $theme_dir テーマのルートディレクトリ.
	 * @return string 見つかったファイル名。見つからない場合は空文字.
	 */
	function ltg_test_detect_theme_json_file_name( $theme_dir ) {
		foreach ( ltg_test_theme_json_file_names() as $file_name ) {
			if ( is_readable( $theme_dir . '/' . $file_name ) ) {
				return $file_name;
			}
		}
		return '';
	}
}

if ( ! function_exists( 'ltg_test_restore_theme_json_file_name' ) ) {
	/**
	 * theme.json のファイル名を指定された状態へ戻す
	 *
	 * @param string $theme_dir          テーマのルートディレクトリ.
	 * @param string $original_file_name 戻したいファイル名.
	 * @return string 実際に戻した場合は戻す前のファイル名。何もしなかった場合は空文字.
	 */
	function ltg_test_restore_theme_json_file_name( $theme_dir, $original_file_name ) {
		// 戻し先が分からない場合は誤ってリネームしないよう何もしない.
		if ( '' === $original_file_name ) {
			return '';
		}

		$original_path = $theme_dir . '/' . $original_file_name;

		// すでに戻したい状態なら何もしない.
		if ( is_readable( $original_path ) ) {
			return '';
		}

		$current_file_name = ltg_test_detect_theme_json_file_name( $theme_dir );
		if ( '' === $current_file_name ) {
			return '';
		}

		// リネームに失敗した場合に戻す前のファイル名を返すと、実際には戻せていないのに
		// 「戻しました」と警告が出て事実と食い違うため、成功したときだけ返す.
		// ただし無言で返すと復元できていないことに誰も気付けないため、
		// 失敗したこと自体は STDERR に出す（戻せていない＝最も危険な状態のため）.
		if ( ! rename( $theme_dir . '/' . $current_file_name, $original_path ) ) {
			if ( defined( 'STDERR' ) ) {
				fwrite(
					STDERR,
					PHP_EOL . '[theme-json-guard] ' . $current_file_name . ' を ' . $original_file_name
						. ' に戻せませんでした。リポジトリの状態が変わったままなので手動で戻してください。' . PHP_EOL
				);
			}
			return '';
		}
		return $current_file_name;
	}
}

if ( ! function_exists( 'ltg_test_guard_theme_json_file_name' ) ) {
	/**
	 * theme.json のファイル名をテスト開始前の状態へ戻すガードを登録する
	 *
	 * @param string $theme_dir テーマのルートディレクトリ.
	 * @return void
	 */
	function ltg_test_guard_theme_json_file_name( $theme_dir ) {
		// テスト開始時点のファイル名を記録する.
		$original_file_name = ltg_test_detect_theme_json_file_name( $theme_dir );

		// どれも見つからない場合は誤ってリネームしないよう何もしない.
		if ( '' === $original_file_name ) {
			return;
		}

		// テストが失敗・異常終了した場合でも必ず実行されるよう shutdown 時に復元する.
		register_shutdown_function(
			function () use ( $theme_dir, $original_file_name ) {
				$renamed_from = ltg_test_restore_theme_json_file_name( $theme_dir, $original_file_name );

				// 無言で直すと、別ルートで再発しても誰も気付けなくなる。
				// 実際に復元したときだけ警告を出す（正常系では何も出ない）.
				if ( '' !== $renamed_from ) {
					fwrite(
						STDERR,
						PHP_EOL . '[theme-json-guard] ' . $renamed_from . ' を ' . $original_file_name . ' に戻しました。'
							. 'テスト中に theme.json のファイル名が変わっています。' . PHP_EOL
					);
				}
			}
		);
	}
}

// このファイルは <テーマルート>/tests/ に置かれているため、親ディレクトリがテーマルート.
ltg_test_guard_theme_json_file_name( dirname( __DIR__ ) );
