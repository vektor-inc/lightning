<?php
/**
 * VK Old Options Notice
 *
 * @package Lightning
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
			add_action( 'admin_notices', array( __CLASS__, 'old_options_notice' ) );
			add_action( 'admin_init', array( __CLASS__, 'launch_update_options' ) );
		}

		const VERSION = "0.1.0";

		/**
		 * Option Judgment
		 *
		 * @param srting $arg jugge or update.
		 */
		public static function option_judgment( $arg ) {

			global $vk_old_options_name;
			$default = array(
				'saved_version' => "0.0.0",
			);
			$vk_old_options = get_option( $vk_old_options_name, $default );

			global $old_setting_array;
			foreach ( (array) $old_setting_array as $old_setting ) {
				if ( 'option' === $old_setting['data_type'] ) {
					$options     = get_option( $old_setting['target_field'] );
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

				if ( 'meta' === $old_setting['data_type'] ){
					if ( version_compare( $old_setting['check_version'], $vk_old_options['saved_version'] ) ) {
						if ( 'judge' === $arg ) {
							return true;
						} elseif ( 'update' === $arg ) {

							// meta情報の上書き関数を実行
							call_user_func( $old_setting['callback'] );
							
							// 保存されていたバージョン番号を確認識別用のバージョン番号で上書き
							$vk_old_options['saved_version'] = $old_setting['check_version'];
							update_option( $vk_old_options_name, $vk_old_options );

						}
					}	
				}
			}
		}

		// public static function options_updated_chack(){
		// 	$default = array(
		// 		'saved_version' => "0.0.0",
		// 	);
		// 	$options = get_option( 'vk_old_options', $default );
		// 	if ( version_compare( self::VERSION, $options['saved_version'] ) ) {
		// 		echo "アップデートが必要です。";
		// 		return true;
		// 	}
		// }


		/**
		 * Notice Old Option
		 */
		public static function old_options_notice() {
			global $pagenow;
			global $vk_update_link;
			if ( 'index.php' === $pagenow ) {
				if ( self::option_judgment( 'judge' ) ) {
					echo '<div class="notice notice-warning"><p><strong>Lightning : </strong> ' . __( 'Because old option is exists, you need to update database', 'lightning' ) . ' <a href="?' . $vk_update_link . '" class="button button-primary">' . __( 'Update database', 'lightning' ) . '</a></p></div>';
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

