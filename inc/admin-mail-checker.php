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

	global $hook_suffix;

	if ( 'options-general.php' === $hook_suffix ){
		return;
	}

	// 現在の管理者メールアドレス.
	$admin_email = get_option( 'admin_email' );

	// URL.
	$url = home_url( '/' );

	// チェックしたい文字列.
	$check_string = 'vektor-inc';

	// メールアドレスを変更する URL.
	$option_link = admin_url( 'options-general.php' );

	// 警告文.
	$notice  = '<div class="notice notice-warning"><p>';
	$notice .= sprintf( __( 'The administrator email address contains "%s". Please change immediately as you will not receive any inquiries.', 'lightning' ), $check_string );
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
