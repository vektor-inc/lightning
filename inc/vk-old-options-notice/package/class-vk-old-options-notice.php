<?php
/**
 * VK Old Options Notice
 *
 * @package lightning
 */

if ( ! class_exists( 'VK_Old_Options_Notice' ) ) {
	/**
	 * VK Old Option Notice
	 */
	class VK_Old_Options_Notice {

		/**
		 * Constructor
		 */
		public function __construct() {
			// 管理画面に更新ボタンを表示する処理
			// Display update button on dashboard.
			add_action( 'admin_notices', array( __CLASS__, 'display_old_options_notice' ) );
			// アップデートを実行
			// Do Update.
			add_action( 'admin_init', array( __CLASS__, 'launch_update_options' ) );
		}

		const VERSION = '0.1.0';

		/**
		 * Option Judgment
		 *
		 * @param srting $arg jugge or update.
		 */
		public static function option_judgment( $arg ) {

			// configで指定したアップデート状況識別用のoption値保存名.
			$default = array(
				'saved_version' => '0.0.0',
			);
			// 保存されているアップデート識別用バージョンを取得.
			global $vk_old_options_name;
			$vk_saved_options = get_option( $vk_old_options_name, $default );

			// チェックバージョンが配列でなく直に保存されていたら配列に入れ直す.
			if ( ! is_array( $vk_saved_options ) ) {
				$version          = $vk_saved_options;
				$vk_saved_options = array(
					'saved_version' => $version,
				);
			}

			// 古い情報の保存先・内容・それに対するコールバック関数の配列をループして処理する.
			global $lightning_old_setting_array;
			foreach ( (array) $lightning_old_setting_array as $old_setting ) {
				// 確認対象が option 値の場合.
				if ( 'option' === $old_setting['data_type'] ) {

					// 今保存されている option 値を取得
					// Get saved option value.
					$options = get_option( $old_setting['target_field'] );

					if ( $options && is_array( $options ) ) {

						// 置換すべき古いオプション値
						// Sould comvert old option value.
						$old_options = $old_setting['old_value'];

						foreach ( $options as $key => $options_value ) {

							foreach ( $old_options as $old_key => $old_options_value ) {
								if ( $options_value === $old_options_value ) {
									if ( 'judge' === $arg ) {
										return true;
									} elseif ( 'update' === $arg ) {
										call_user_func( $old_setting['callback'] );
									}
								}
							}
						}
					}
				}

				// 確認対象が meta 情報の場合.
				if ( 'meta' === $old_setting['data_type'] ) {

					// 保存されている最後の確認バージョンより新しいチェック項目がある場合
					// In case exixt of newer check item.
					if ( version_compare( $old_setting['check_version'], $vk_saved_options['saved_version'] ) > 0 ) {
						if ( 'judge' === $arg ) {

							return true;

						} elseif ( 'update' === $arg ) {

							// meta情報の上書き関数を実行.
							call_user_func( $old_setting['callback'] );

							// 保存されていたバージョン番号を確認識別用のバージョン番号で上書き.

							$vk_saved_options['saved_version'] = $old_setting['check_version'];
							// $vk_saved_options['saved_version'] = $old_setting['check_version'];
							update_option( $vk_old_options_name, $vk_saved_options );

						}
					}
				}
			}
		}

		/**
		 * Display Notice Old Option
		 */
		public static function display_old_options_notice() {
			global $pagenow;
			global $vk_update_link;
			if ( 'index.php' === $pagenow ) {
				if ( self::option_judgment( 'judge' ) ) {
					echo wp_kses_post( '<div class="notice notice-warning"><p><strong>Lightning : </strong> ' . __( 'Because old option is exists, you need to update database', 'lightning' ) . ' <a href="?' . $vk_update_link . '" class="button button-primary">' . __( 'Update database', 'lightning' ) . '</a></p></div>' );
				}
			}
		}

		/**
		 * Launch Update Options
		 */
		public static function launch_update_options() {
			global $vk_update_link;
			if ( isset( $_GET[ $vk_update_link ] ) ) {
				self::option_judgment( 'update' );
			}
		}
	}
	new VK_Old_Options_Notice();
}
