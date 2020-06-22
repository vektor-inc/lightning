<?
/**
 * VK Old Options Notice
 *
 * @package Lightning
 */

/**
 * VK Old Options Notice
 */
class VK_Old_Options_Notice {

	public function __construct() {
		add_action( 'admin_notices', 'admin_notice' );
		add_action( 'admin_init', 'launch_update_options');
	}
	/**
	 * Option Judgment
	 *
	 * @param string $arg judge or action
	 * @return void
	 */
	public static function option_judgment( $arg ) {
		global $old_setting_array;
		foreach ( (array) $old_setting_array as $old_setting ) {
			if ( 'option' === $old_setting['data_type'] ) {
				$options = get_option( $old_setting['target_field'] );
				if ( isset( $options['old_value'] ) ) {
					if ( 'judge' === $arg ) {
						return true;
					} elseif ( 'action' === $arg )   {
						call_user_func( $options['callback'] );
					}
				}
			}
		}
	}

	public static function admin_notice() {
		global $pagenow;
		global $vk_update_link;
		if ( 'index.php' === $pagenow ) {
			if ( option_judgment( 'judge' ) ) {
				echo '<div class="notice notice-info"><p>' . __( 'Because old option is exists, you need to update detabase', 'lightning' ) . '<a href="' . $vk_update_link . '">' . __( 'Update database', 'lightning' ) . '</a></p></div>';

			}
		}
	}

	public static function launch_update_options() {
		global $vk_update_link;
		if( isset( $_GET[ $vk_update_link ] ) ) {
			option_judgment( 'action' );
		}
	}

}
