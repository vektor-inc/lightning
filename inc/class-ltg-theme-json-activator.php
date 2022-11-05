<?php
if ( ! class_exists( 'LTG_Theme_Json_Activator' ) ) {
	class LTG_Theme_Json_Activator {

		public function __construct() {
			add_action( 'upgrader_process_complete', array( __CLASS__, 'default_activate_theme_json' ) );

			// 'update_option_lightning_theme_options' は保存前に実行されてしまい、
			// 判定が意図したものにならないため 'updated_option' で処理.
			add_action( 'updated_option', array( __CLASS__, 'rename_theme_json' ) );
			add_action( 'deleted_option', array( __CLASS__, 'rename_theme_json' ) );
			// Theme Updated.
			add_action( 'upgrader_process_complete', array( __CLASS__, 'rename_theme_json' ) );
		}

		/**
		 * Activate theme.json for new install
		 *
		 *  @since 15.1.0
		 *  @return void
		 */
		public static function default_activate_theme_json() {
			$options = get_option( 'lightning_theme_options' );
			if ( ! $options ) {
				$options = array(
					'theme_json' => true,
				);
				update_option( 'lightning_theme_options', $options );
			}
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
