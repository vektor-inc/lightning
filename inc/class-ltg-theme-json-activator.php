<?php
if ( ! class_exists( 'LTG_Theme_Json_Activator' ) ) {
	class LTG_Theme_Json_Activator {

		public function __construct() {
			// New installation and update
			// add_action( 'upgrader_process_complete', array( __CLASS__, 'new_installation_and_update' ) );

			add_filter( 'install_theme_complete_actions', array( __CLASS__, 'new_installation_and_update' ), 10, 4 );

			// 'update_option_lightning_theme_options' は保存前に実行されてしまい、
			// 判定が意図したものにならないため 'updated_option' で処理.
			// add_action( 'updated_option', array( __CLASS__, 'rename_theme_json' ) );
			// add_action( 'updated_option', array( __CLASS__, 'new_installation_and_update' ), 10, 3 );
			// add_action( 'deleted_option', array( __CLASS__, 'rename_theme_json' ) );
		}

		/**
		 * Activate theme.json for new install and update.
		 *
		 *  @since 15.1.0
		 *  @return void
		 */
		public static function new_installation_and_update( $install_actions, $api, $stylesheet, $theme_info ) {

			if ( 'lightning' === $stylesheet ) {
				// New installation *******************************.
				$options = get_option( 'lightning_theme_options' );
				if ( ! $options ) {
					$options = array(
					'theme_json' => true,
					);
					update_option( 'lightning_theme_options', $options );
				}
				// New installation and Update *******************************.
				self::rename_theme_json();

				// $saved_option = get_option( 'lightning_update_test' );
				$args = array($install_actions, $api, $stylesheet, $theme_info);
				// if ( ! is_array( $saved_option ) ) {
				// 	$saved_option = array();
				// }
				// array_push( $saved_option, $args );

				// $theme_info->get_data();
				// update_option( 'lightning_update_test', $theme_info->get_data() );
				// update_option( 'lightning_update_test', $theme_info->get_data() );
				update_option( 'lightning_update_test', $args );
			}
			return $install_actions;
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
		$option = get_option( 'lightning_update_test' );
		print '<pre style="text-align:left">';
		print_r( $option );
		print '</pre>';
		// echo '+++ '.get_option( 'lightning_update_test' ) . ' +++';
	}
);
