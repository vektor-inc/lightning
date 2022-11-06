<?php
if ( ! class_exists( 'LTG_Theme_Json_Activator' ) ) {
	class LTG_Theme_Json_Activator {

		public function __construct() {
			// New installation and update
			// add_action( 'upgrader_process_complete', array( __CLASS__, 'new_installation_and_update' ) );

			// add_filter( 'install_theme_complete_actions', array( __CLASS__, 'new_installation_and_update' ), 10, 4 );
			add_filter( 'install_theme_complete_actions', array( __CLASS__, 'install_theme_action' ), 10, 4 );
			add_filter( 'update_theme_complete_actions', array( __CLASS__, 'update_theme_action' ), 10, 2 );

			// 'update_option_lightning_theme_options' は保存前に実行されてしまい、
			// 判定が意図したものにならないため 'updated_option' で処理.
			// add_action( 'updated_option', array( __CLASS__, 'rename_theme_json' ) );
			// add_action( 'updated_option', array( __CLASS__, 'new_installation_and_update' ), 10, 3 );
			// add_action( 'deleted_option', array( __CLASS__, 'rename_theme_json' ) );
		}

		/**
		 * Theme install filter action.
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
				$options = get_option( 'lightning_theme_options' );
				if ( ! $options ) {
					$options = array(
						'theme_json' => true,
					);
					update_option( 'lightning_theme_options', $options );
				}
				self::rename_theme_json();

				$args = array( $install_actions, $api, $stylesheet, $theme_info );
				update_option( 'lightning_update_test', $args );
			}

			return $install_actions;
		}
		/**
		 * Theme update filter action.
		 *
		 * @since 15.1.0
		 *
		 * @param string[] $update_actions Array of theme action links.
		 * @param string   $theme          Theme directory name.
		 *
		 * @return string[] $update_actions
		 */
		public static function update_theme_action( $update_actions, $theme ) {
			self::rename_theme_json();

			$args = array( $update_actions, $theme );
			update_option( 'lightning_update_test', $args );

			return $update_actions;
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
