<?php
/**
 * Admin Mail Checker
 *
 * @package Lightning
 */

/**
 * Admin Mail Checker
 */
function lightning_admin_mail_checker() {

	// 現在の管理者メールアドレス.
	$admin_email = get_option( 'admin_email' );

	// URL.
	$url = plugin_dir_url( __FILE__ );

	// チェックしたい文字列.
	$check_string = 'vektor-inc';

	// メールアドレスを変更する URL.
	$option_link = admin_url( 'options-general.php' );

	// 警告文.
	$notice  = '<div class="notice notice-warning"><p>';
	$notice .= __( 'The administrator email address contains "' . $check_string . '". Please change immediately as you will not receive any inquiries.', 'lightning' );
	$notice .= ' <a href="' . $option_link . '" class="button button-primary">' . __( 'Change email Now!', 'lightning' ) . '</a>';
	$notice .= '</p></div>';

	if (
		// メールアドレスにチェックしたい文字列が含まれていて.
		strpos( $admin_email, $check_string ) !== false &&
		// URL にチェックしたい文字列が 含まれていない.
		strpos( $url, $check_string ) === false
	) {
		// 警告文を表示.
		echo wp_kses_post( $notice );
	}
}
add_action( 'admin_notices', 'lightning_admin_mail_checker' );
