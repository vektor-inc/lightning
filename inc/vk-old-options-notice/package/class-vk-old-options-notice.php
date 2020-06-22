<?php
/**
 * VK Old Options Notice
 *
 * @package Lightning
 */

/**
 * VK Old Options Notice
 */

function option_judgment( $arg ) {
	global $old_setting_array;
	foreach ( (array) $old_setting_array as $old_setting ) {
		if ( 'option' === $old_setting['data_type'] ) {
			$options     = get_option( $old_setting['target_field'] );
			$old_options = $old_setting['old_value'];

			if ( in_array( $old_options, $options, true ) ) {
				if ( 'judge' === $arg ) {
					return true;
				} elseif ( 'update' === $arg ) {
					call_user_func( $old_setting['callback'] );
				}
			}
		}
	}
}

function vk_old_options_notice() {
	global $pagenow;
	global $vk_update_link;
	if ( 'index.php' === $pagenow ) {
		if ( option_judgment( 'judge' ) ) {
			echo '<div class="notice notice-info"><p>' . __( 'Because old option is exists, you need to update detabase', 'lightning' ) . '<a href="?' . $vk_update_link . '">' . __( 'Update database', 'lightning' ) . '</a></p></div>';
		}
	}
}
add_action( 'admin_notices', 'vk_old_options_notice' );

function launch_update_options() {
	global $vk_update_link;
	if ( isset( $_GET[ $vk_update_link ] ) ) {
		option_judgment( 'update' );
	}
}
add_action( 'admin_init', 'launch_update_options' );
