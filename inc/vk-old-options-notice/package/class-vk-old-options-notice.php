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

	public static function option_judgment( $arg ) {
		global $old_setting_array;
		foreach ( (array) $old_setting_array as $old_setting ) {
			if ( 'option' === $old_setting['data_type'] ) {
				$options = get_option( $old_setting['target_field'] );
				if ( isset( $options['old_value'] ) ) {
					if ( 'judge' === $arg ) {
						return true;
					} else {
						call_user_func( $options['callback'] );
					}
				}
			}
		}
	}
}
