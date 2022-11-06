<?php
/**
 *
 * LTG_Theme_Json_Activator
 *
 * このクラスは theme.json の有効化・無効化を制御する
 *
 * 新規インストールには自動的に theme.json を有効化する
 * 既存のユーザーには自動的に theme.json が有効化されてはいけない
 *
 * @package vektor-inc/lighting
 */

if ( ! class_exists( 'LTG_Theme_Json_Activator' ) ) {

	/**
	 * LTG_Theme_Json_Activator
	 */
	class LTG_Theme_Json_Activator {

		public function __construct() {

			// New install action.
			add_filter( 'install_theme_complete_actions', array( __CLASS__, 'install_theme_action' ), 10, 4 );
			// Update action.
			add_filter( 'upgrader_install_package_result', array( __CLASS__, 'update_theme_action' ), 10, 2 );

			// 設定を保存された時のアクション.
			// 'update_option_lightning_theme_options' は保存前に実行されてしまい、
			// 判定が意図したものにならないため 'updated_option' で処理.
			add_action( 'updated_option', array( __CLASS__, 'rename_theme_json' ), 10, 1 );
		}

		/**
		 * Theme install filter action.
		 *
		 * Lightning をはじめてインストールされた場合には自動的に theme.json を有効化したい。
		 * _theme.json ファイルを書き換えるだけだとカスタマイズ画面でアップデートされた時に無効化されてしまうので、
		 * option(lightning_theme_options) にもその旨保存しておく。
		 *
		 * 適切なアクションフックがなかったためフィルターを利用しているので、第一引数はそのまま返す
		 *
		 * @since 15.1.0
		 *
		 * @param string[] $install_actions Array of theme action links.
		 * @param object   $api             Object containing WordPress.org API theme data.
		 * @param string   $stylesheet      Theme directory name.
		 * @param WP_Theme $theme_info      Theme object.
		 *
		 * @return string[] $update_actions
		 */
		public static function install_theme_action( $install_actions, $api, $stylesheet, $theme_info ) {

			if ( 'lightning' === $stylesheet ) {
				// New installation *******************************.
				// lightning_theme_options が存在しているかどうかで Lightning の新規インストールかどうかを判定.
				$options = get_option( 'lightning_theme_options' );
				if ( ! $options ) {
					$options = array(
						'theme_json' => true,
					);
					update_option( 'lightning_theme_options', $options );
				}

				// theme.json のリネームを実行.
				self::rename_theme_json();

				// 実際には利用しないがデバッグ用に保存.
				$args = array( $install_actions, $api, $stylesheet, $theme_info );
				update_option( 'lightning_update_info', $args );
			}

			return $install_actions;
		}
		/**
		 * Theme update filter action.
		 *
		 * 適切なアクションフックがなかったためフィルターを利用しているので、第一引数はそのまま返す
		 *
		 * @since 15.1.0
		 *
		 * @param array|WP_Error $result     Result from WP_Upgrader::install_package().
		 * @param array          $hook_extra Extra arguments passed to hooked filters.
		 *
		 * @return string[] $result
		 */
		public static function update_theme_action( $result, $hook_extra ) {

			// theme.json のリネームを実行.
			self::rename_theme_json();

			// 実際には利用しないがデバッグ用に保存.
			$args = array( $result, $hook_extra );
			update_option( 'lightning_update_info', $args );
			return $result;
		}

		/**
		 * Judge theme.json file is active or not
		 *
		 * @since 15.1.0
		 * @return bool $is_theme_json
		 */
		public static function is_theme_json() {
			$options = get_option( 'lightning_theme_options' );
			if ( ! $options || ( ! empty( $options['theme_json'] ) && true === $options['theme_json'] ) ) {
				$is_theme_json = true;
			} else {
				$is_theme_json = false;
			}
			return $is_theme_json;
		}
		/**
		 * Rename theme.json file
		 * Before update option
		 *
		 * @since 15.1.0
		 * @return string : rename statue ( for PHPUnit test )
		 */
		public static function rename_theme_json() {

			$_theme_json_path = get_template_directory() . '/_theme.json';
			$theme_json_path  = get_template_directory() . '/theme.json';
			if ( self::is_theme_json() ) {
				if ( is_readable( $_theme_json_path ) ) {
					$rename = rename( $_theme_json_path, $theme_json_path );
					if ( ! $rename ) {
						return __( 'Rename failed.', 'lightning' );
					}
				}
				$path = get_template_directory() . '/theme.json';
				if ( is_readable( $path ) ) {
					return 'theme.json';
				}
			} else {
				if ( is_readable( $theme_json_path ) ) {
					$rename = rename( $theme_json_path, $_theme_json_path );
					if ( ! $rename ) {
						return __( 'Rename failed.', 'lightning' );
					}
				}
				$path = get_template_directory() . '/_theme.json';
				if ( is_readable( $path ) ) {
					return '_theme.json';
				}
			}
		}
	}

	new LTG_Theme_Json_Activator();
}

// ★★★ test ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
add_action(
	'admin_notices',
	function() {
		$option = get_option( 'lightning_update_info' );
		print '<pre style="text-align:left">';
		print_r( $option );
		print '</pre>';
		// echo '+++ '.get_option( 'lightning_update_test' ) . ' +++';
	}
);
