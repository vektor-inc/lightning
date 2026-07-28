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

if ( ! function_exists( 'ltg_test_guard_theme_json_file_name' ) ) {
	/**
	 * theme.json のファイル名をテスト開始前の状態へ戻すガードを登録する
	 *
	 * @param string $theme_dir テーマのルートディレクトリ.
	 * @return void
	 */
	function ltg_test_guard_theme_json_file_name( $theme_dir ) {
		// テストの過程で存在し得る theme.json 系ファイルの名前.
		$file_names = array( 'theme.json', '_theme.json', 'no_theme.json' );

		// テスト開始時点のファイル名を記録する.
		$original_file_name = '';
		foreach ( $file_names as $file_name ) {
			if ( is_readable( $theme_dir . '/' . $file_name ) ) {
				$original_file_name = $file_name;
				break;
			}
		}

		// どれも見つからない場合は誤ってリネームしないよう何もしない.
		if ( '' === $original_file_name ) {
			return;
		}

		// テストが失敗・異常終了した場合でも必ず実行されるよう shutdown 時に復元する.
		register_shutdown_function(
			function () use ( $theme_dir, $file_names, $original_file_name ) {
				$original_path = $theme_dir . '/' . $original_file_name;

				// すでに開始時と同じ状態なら何もしない.
				if ( is_readable( $original_path ) ) {
					return;
				}

				// 現在のファイル名を探して開始時のファイル名に戻す.
				foreach ( $file_names as $file_name ) {
					$current_path = $theme_dir . '/' . $file_name;
					if ( is_readable( $current_path ) ) {
						rename( $current_path, $original_path );
						return;
					}
				}
			}
		);
	}
}

// このファイルは <テーマルート>/tests/ に置かれているため、親ディレクトリがテーマルート.
ltg_test_guard_theme_json_file_name( dirname( __DIR__ ) );
