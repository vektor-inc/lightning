<?php

define( 'LIG_G3_DIR', '_g3' );
define( 'LIG_G2_DIR', '_g2' );

define( 'LIG_DEBUG', false );

require_once __DIR__ . '/vendor/autoload.php';

if ( true === LIG_DEBUG ) {
	function lightning_debug_mode() {
		$options = lightning_get_theme_options();
		// $options = get_option( 'lightning_theme_options' );
		// unset( $options['layout'] );
		// update_option( 'lightning_theme_options', $options );
		print '<pre style="text-align:left">';
		print_r( $options );
		print '</pre>';
	}
	add_action( 'lightning_site_header_after', 'lightning_debug_mode' );
}

/**
 * Check is G3
 *
 * @return bool
 */
function lightning_is_g3() {

	$return = true;
	$g       = get_option( 'lightning_theme_generation' );
	$options = get_option( 'lightning_theme_options' );

	if ( '1' === get_option( 'fresh_site' ) ) {
		// 新規サイトの場合はG3に指定.
		update_option( 'lightning_theme_generation', 'g3' );
		$return = true;
	} else if ( 'g3' === $g ) {
		$return = true;
	} elseif ( 'g2' === $g ) {
		$return = false;
	} else {
		$skin    = get_option( 'lightning_design_skin' );
		if ( 'origin2' === $skin ) {
			update_option( 'lightning_theme_generation', 'g2' );
			$return = false;
		} elseif ( 'origin3' === $skin ) {
			update_option( 'lightning_theme_generation', 'g3' );
			$return = true;
		} elseif ( empty( $options ) ) {
			// 後から Lightning をインストールした場合は G3 にする
			// （新規サイトではない && lightning_theme_options が存在しない）
			update_option( 'lightning_theme_generation', 'g3' );
			$return = true;
		} else {
			// これ以外は旧ユーザー（Lightning Pro）の可能性が高いのでG2.
			update_option( 'lightning_theme_generation', 'g2' );
			$return = false;
		}
	}
	return apply_filters( 'lightning_is_g3', $return );
}

require __DIR__ . '/inc/class-ltg-template-redirect.php';

/**
 * 最終的に各Gディレクトリに移動
 */
if ( ! function_exists( 'lightning_get_template_part' ) ) {
	function lightning_get_template_part( $slug, $name = null, $args = array() ) {

		if ( lightning_is_g3() ) {
			$g_dir = '_g3';
		} else {
			$g_dir = '_g2';
		}

		/**
		 * 読み込み優先度
		 *
		 * 1.child g階層 nameあり
		 * 2.child 直下 nameあり
		 * 3.parent g階層 nameあり
		 *
		 * 4.child g階層 nameなし
		 * 5.child 直下 nameなし
		 * 6.parent g階層 nameなし
		 */

		/* Almost the same as the core */
		$template_path_array = array();
		$name                = (string) $name;

		// Child theme G directory
		if ( preg_match( '/^' . $g_dir . '/', $slug ) ) {
			// 1. g階層がもともと含まれている場合
			if ( '' !== $name ) {
				$template_path_array[] = get_stylesheet_directory() . "/{$slug}-{$name}.php";
			}
		} else {
			// g階層が含まれていない場合

			// 1. g階層付きのファイルパス
			if ( '' !== $name ) {
				$template_path_array[] = get_stylesheet_directory() . '/' . $g_dir . "/{$slug}-{$name}.php";
			}
			// 2. 直下のファイルパス
			if ( '' !== $name ) {
				$template_path_array[] = get_stylesheet_directory() . "/{$slug}-{$name}.php";
			}
		}

		if ( preg_match( '/^' . $g_dir . '/', $slug ) ) {
			// 3. g階層がもともと含まれている場合
			if ( '' !== $name ) {
				$template_path_array[] = get_template_directory() . "/{$slug}-{$name}.php";
			}
		} else {
			// 3. g階層がもともと含まれていない場合
			if ( '' !== $name ) {
				$template_path_array[] = get_template_directory() . '/' . $g_dir . "/{$slug}-{$name}.php";
			}
		}

		// Child theme G directory
		if ( preg_match( '/^' . $g_dir . '/', $slug ) ) {
			// 4. g階層がもともと含まれている場合
			$template_path_array[] = get_stylesheet_directory() . "/{$slug}.php";
		} else {
			// g階層が含まれていない場合
			// 4. g階層付きのファイルパス
			$template_path_array[] = get_stylesheet_directory() . '/' . $g_dir . "/{$slug}.php";
			// 5. 直下のファイルパス
			$template_path_array[] = get_stylesheet_directory() . "/{$slug}.php";
		}

		if ( preg_match( '/^' . $g_dir . '/', $slug ) ) {
			// g階層がもともと含まれている場合
			// 6. 親のg階層
			$template_path_array[] = get_template_directory() . "/{$slug}.php";
		} else {
			// 6. 親のg階層
			$template_path_array[] = get_template_directory() . '/' . $g_dir . "/{$slug}.php";
		}

		foreach ( (array) $template_path_array as $template_path ) {
			if ( file_exists( $template_path ) ) {
				$require_once = false;
				load_template( $template_path, $require_once );
				break;
			}
		}
	}
}

if ( lightning_is_g3() ) {
	require __DIR__ . '/' . LIG_G3_DIR . '/functions.php';
} else {
	require __DIR__ . '/' . LIG_G2_DIR . '/functions.php';
}

require __DIR__ . '/inc/customize-basic.php';
require __DIR__ . '/inc/tgm-plugin-activation/tgm-config.php';
require __DIR__ . '/inc/vk-old-options-notice/vk-old-options-notice-config.php';
require __DIR__ . '/inc/admin-mail-checker.php';
require __DIR__ . '/inc/functions-compatible.php';
require __DIR__ . '/inc/font-awesome/font-awesome-config.php';
require __DIR__ . '/inc/old-page-template.php';

require __DIR__ . '/inc/class-ltg-theme-json-activator.php';
new LTG_Theme_Json_Activator();

/**
 * 世代切り替えした時に同時にスキンも変更する処理
 *
 * 世代は lightning_theme_generation で管理している。
 *
 *      generetionに変更がある場合
 *          今の世代でのスキン名を lightning_theme_options の配列の中に格納しておく
 *          lightning_theme_option の中に格納されている新しい世代のスキンを取得
 *          スキンをアップデートする *
 */

function lightning_change_generation( $old_value, $value, $option ) {
	// 世代変更がある場合
	if ( $value !== $old_value ) {

		// 現状のスキンを取得
		$current_skin = get_option( 'lightning_design_skin' );

		if ( $current_skin ) {
			// オプションを取得
			$options = get_option( 'lightning_theme_options' );
			if ( ! $options || ! is_array( $options ) ) {
				$options = array();
			}
			$options[ 'previous_skin_' . $old_value ] = $current_skin;
			// 既存のスキンをオプションに保存
			update_option( 'lightning_theme_options', $options );
		}

		// 前のスキンが保存されている場合
		if ( ! empty( $options[ 'previous_skin_' . $value ] ) ) {
			$new_skin = esc_attr( $options[ 'previous_skin_' . $value ] );

			// 前のスキンが保存されていない場合
		} elseif ( 'g3' === $value ) {
				$new_skin = 'origin3';
		} else {
			$new_skin = 'origin2';
		}
		update_option( 'lightning_design_skin', $new_skin );
	}
}
add_action( 'update_option_lightning_theme_generation', 'lightning_change_generation', 10, 3 );
