<?php
/**
 *
 * LTG_Theme_Json_Activator
 *
 * このクラスは theme.json の有効化・無効化を制御します。
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

		/**
		 * Constructor
		 *
		 * @return void
		 */
		public function __construct() {

			// New install action.
			add_filter( 'install_theme_complete_actions', array( __CLASS__, 'install_theme_action' ), 10, 4 );
			// Update action.
			add_filter( 'upgrader_install_package_result', array( __CLASS__, 'update_theme_action' ), 10, 2 );

			// 設定を保存された時のアクション.
			// 'update_option_lightning_theme_options' は保存前に実行されてしまい、
			// 判定・ファイル名の書き換えが意図したものにならないため 'updated_option' で処理.
			add_action( 'updated_option', array( __CLASS__, 'update_option_action' ), 10, 1 );

			add_action( 'customize_register', array( __CLASS__, 'customize_register' ), 11, 1 );
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
		 * @param array|WP_Error $result     Result from WP_Upgrader::install_package().
		 * @param array          $hook_extra Extra arguments passed to hooked filters.
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
		 * Update option action.
		 *
		 * @since 15.1.0
		 * @param string $option : Update option name.
		 * @return void
		 */
		public static function update_option_action( $option = '' ) {
			// lightning_theme_options が更新された場合のみリネームを実行.
			if ( 'lightning_theme_options' === $option ) {
				// theme.json のリネームを実行.
				self::rename_theme_json();
			}
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

		/**
		 * Customize register
		 */
		public static function customize_register( $wp_customize ) {

			if ( class_exists( 'VK_Custom_Html_Control' ) ) {
				$wp_customize->add_setting(
					'lightning_theme_json_title',
					array(
						'sanitize_callback' => 'sanitize_text_field',
					)
				);
				$wp_customize->add_control(
					new VK_Custom_Html_Control(
						$wp_customize,
						'lightning_theme_json_title',
						array(
							'label'            => __( 'theme.json Setting', 'lightning-g3-pro-unit' ),
							'section'          => 'lightning_function',
							'type'             => 'text',
							'custom_title_sub' => '',
							'custom_html'      => '',
							'priority'         => 1,
						)
					)
				);
			}

			$wp_customize->add_setting(
				'lightning_theme_options[theme_json]',
				array(
					// デフォルトを true にすると、既存ユーザーが他の箇所を変更した時に theme.json が有効になってしまうので false にしておく.
					// If the default is true, theme.json will be valid when existing users change other parts, so set it to false.
					'default'           => false,
					'type'              => 'option',
					'capability'        => 'edit_theme_options',
					'sanitize_callback' => array( 'VK_Helpers', 'sanitize_boolean' ),
				)
			);
			$wp_customize->add_control(
				'lightning_theme_options[theme_json]',
				array(
					'label'       => __( 'Enable theme.json', 'lightning-g3-pro-unit' ),
					'section'     => 'lightning_function',
					'settings'    => 'lightning_theme_options[theme_json]',
					'type'        => 'checkbox',
					'description' => '<ul class="admin-custom-discription"><li>' . __( 'Enabling theme.json mainly enhances editing functionality.', 'lightning-g3-pro-unit' ) . '</li><li>' . __( 'However, if you enable it later on an existing site, some html structures such as group blocks will be changed, so if you enable it on a site other than a new site, please verify the display verification thoroughly.', 'lightning-g3-pro-unit' ) . '</li></ul>',
					'priority'    => 1,
				)
			);
		}
	}

	new LTG_Theme_Json_Activator();
}
