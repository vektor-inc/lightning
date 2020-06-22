<?php
/**
 * VK Old Options Notice
 *
 * @package Lightning
 */

if ( ! class_exists( 'VK_Old_Options_Notice' ) ) {
	class VK_Old_Options_Notice {

		public function __construct() {
			add_action( 'admin_notices', array( __CLASS__, 'old_options_notice' ) );
			add_action( 'admin_init', array( __CLASS__, 'launch_update_options' ) );
		}

		public function option_judgment( $arg ) {
			global $old_setting_array;
			foreach ( (array) $old_setting_array as $old_setting ) {
				if ( 'option' === $old_setting['data_type'] ) {
					$options     = get_option( $old_setting['target_field'] );
					$old_options = $old_setting['old_value'];
					if ( array_intersect_assoc( $old_options, $options ) ) {
						if ( 'judge' === $arg ) {
							return true;
						} elseif ( 'update' === $arg ) {
							call_user_func( $old_setting['callback'] );
						}
					}
				}
			}
		}

		public function old_options_notice() {
			global $pagenow;
			global $vk_update_link;
			if ( 'index.php' === $pagenow ) {
				if ( self::option_judgment( 'judge' ) ) {
					echo '<div class="notice notice-info"><p>' . __( 'Because old option is exists, you need to update detabase', 'lightning' ) . '<a href="?' . $vk_update_link . '">' . __( 'Update database', 'lightning' ) . '</a></p></div>';
				}
			}
		}
		public function launch_update_options() {
			global $vk_update_link;
			if ( isset( $_GET[ $vk_update_link ] ) ) {
				self::option_judgment( 'update' );
			}
		}
	}
	new VK_Old_Options_Notice();
}

