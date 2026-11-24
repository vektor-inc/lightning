<?php
/*
	Add metabox
/*-------------------------------------------*/
add_action( 'admin_menu', 'lightning_add_design_meta_box' );

// add meta_box
function lightning_add_design_meta_box() {

	// 投稿トップは固定ページでなくアーカイプページ判定されるので、
	// 投稿トップにわりあてた固定ページで指定したカラム数は反映されない。
	// よって、誤解を避けるためにレイアウト設定を含む Lightningデザイン設定のmetabox自体表示しないようにする
	if ( isset( $_GET['post'] ) && $_GET['post'] === get_option( 'page_for_posts' ) && 'page' === get_option( 'show_on_front' ) ) {
		return;
	}

	$args       = array(
		'public' => true,
	);
	$post_types = get_post_types( $args, 'object' );

	foreach ( $post_types as $post_type => $value ) {
		add_meta_box(
			'lightning_design_setting',
			__( 'Lightning design setting', 'lightning' ),
			'lightning_design_setting_meta_fields',
			$post_type,
			'side',
			'default',
			array( '__back_compat_meta_box' => true )
		);
	}
}

/*
	入力フィールドの生成
/*-------------------------------------------*/

function lightning_design_setting_meta_fields() {

	echo '<div class="lightning_design_setting_meta_fields">';

	// CSRF対策の設定（フォームにhiddenフィールドとして追加するためのnonceを「'noncename__lightning_desigin」として設定）
	// アクション名は lightning_design_setting_save() の wp_verify_nonce() と同じ固定文字列にすること。
	// 時刻で変わる値をアクション名にすると、発行から 12 時間の区切りを跨いだ保存が通らなくなる.
	wp_nonce_field( 'lightning_design_setting', 'noncename__lightning_desigin' );

	global $post;

	/*
	  Layout setting
	/*-------------------------------------------*/
	echo '<h4>' . __( 'Layout setting', 'lightning' ) . '</h4>';

	$id              = '_lightning_design_setting[layout]';
	$saved_post_meta = get_post_meta( $post->ID, '_lightning_design_setting', true );

	if ( ! empty( $saved_post_meta['layout'] ) ) {
		$saved = $saved_post_meta['layout'];
	} else {
		$saved = '';
	}

	$options = array(
		'default'               => __( 'Use common settings', 'lightning' ),
		'col-two'               => __( '2 column', 'lightning' ),
		'col-one-no-subsection' => __( '1 column', 'lightning' ),
		'col-one'               => __( '1 column (with sidebar element)', 'lightning' ),
	);
	$options = apply_filters( 'lighghtning_columns_setting_choice', $options );

	$form = '<select id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '">';
	foreach ( $options as $key => $value ) {
		$selected = '';
		if ( $key === $saved ) {
			$selected = ' selected';
		}
		$form .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $value ) . '</option>';
	}
	$form .= '</select>';

	/*
	  .siteContent padding
	/*-------------------------------------------*/
	$form .= '<h4>' . __( 'Padding and margin setting', 'lightning' ) . '</h4>';

	$id    = '_lightning_design_setting[site_body_padding]';
	$name  = '_lightning_design_setting[site_body_padding]';
	$label = __( 'Delete site-body padding', 'lightning' );

	$form .= '<ul>';

	$checked = '';
	if ( ! empty( $saved_post_meta['site_body_padding'] ) ) {
		$checked = ' checked';
	}

	$form .= '<li class="vk_checklist_item vk_checklist_item-style-vertical">' . '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="true"' . $checked . ' class="vk_checklist_item_input"><label for="' . esc_attr( $name ) . '" class="vk_checklist_item_label">' . wp_kses_post( $label ) . '</label></li>';
	$form .= '</ul>';

	echo $form;

	do_action( 'lightning_design_setting_meta_fields' );

	echo '</div>';

}

/*
	Enqueue block editor panel script
/*-------------------------------------------*/
add_action( 'enqueue_block_editor_assets', 'lightning_g3_enqueue_design_setting_panel' );

function lightning_g3_enqueue_design_setting_panel() {
	$screen = get_current_screen();
	if ( ! $screen || ! $screen->is_block_editor || empty( $screen->post_type ) ) {
		return;
	}
	$script_path = get_parent_theme_file_path( '/assets/js/design-setting-panel.js' );
	if ( ! file_exists( $script_path ) ) {
		return;
	}
	wp_enqueue_script(
		'lightning-design-setting-panel',
		get_parent_theme_file_uri( '/assets/js/design-setting-panel.js' ),
		array( 'wp-plugins', 'wp-editor', 'wp-element', 'wp-components', 'wp-data', 'wp-core-data', 'wp-i18n' ),
		filemtime( $script_path ),
		true
	);
	wp_localize_script( 'lightning-design-setting-panel', 'lightningPanelI18n', array(
		'panelTitle'       => __( 'Lightning design setting', 'lightning' ),
		'layoutSetting'    => __( 'Layout setting', 'lightning' ),
		'useCommon'        => __( 'Use common settings', 'lightning' ),
		'col2'             => __( '2 column', 'lightning' ),
		'col1'             => __( '1 column', 'lightning' ),
		'col1Sidebar'      => __( '1 column (with sidebar element)', 'lightning' ),
		'paddingMargin'    => __( 'Padding and margin setting', 'lightning' ),
		'deletePadding'    => __( 'Delete site-body padding', 'lightning' ),
	) );
}

/*
	Save meta
/*-------------------------------------------*/
add_action( 'save_post', 'lightning_design_setting_save' );

/**
 * 投稿ごとのデザイン設定の入力値を整形する
 *
 * REST API 側のスキーマ（register-meta.php）と同じく、各項目の値は文字列として扱う。
 * キーと値を sanitize_text_field() で整形し、入れ子の配列など文字列にできない値は捨てる。
 *
 * @param mixed $setting フォームから送信されたデザイン設定（スラッシュ除去済み）.
 * @return array 整形後のデザイン設定. 配列以外が渡された場合は空の配列.
 */
function lightning_sanitize_design_setting( $setting ) {
	$sanitized = array();

	if ( ! is_array( $setting ) ) {
		return $sanitized;
	}

	foreach ( $setting as $key => $value ) {
		// 入れ子の配列など文字列にできない値は保存しない.
		if ( ! is_scalar( $value ) ) {
			continue;
		}

		$key = sanitize_text_field( (string) $key );
		if ( '' === $key ) {
			continue;
		}

		$sanitized[ $key ] = sanitize_text_field( (string) $value );
	}

	return $sanitized;
}

/**
 * 投稿ごとのデザイン設定をクラシックエディターの保存時に保存する
 *
 * ブロックエディターからの保存は REST API 経由（register-meta.php）で行われるため、
 * この関数は nonce が送信されず何もしない。
 *
 * @param int $post_id 保存対象の投稿 ID.
 * @return void
 */
function lightning_design_setting_save( $post_id ) {

	// nonce の確認（CSRF 対策）。アクション名はフォーム側の wp_nonce_field() と同じ固定文字列にすること.
	$nonce = isset( $_POST['noncename__lightning_desigin'] ) ? sanitize_text_field( wp_unslash( $_POST['noncename__lightning_desigin'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'lightning_design_setting' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// 権限の確認（この投稿を編集できるユーザーだけが保存できる）.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	$field = '_lightning_design_setting';

	// フィールドが送信されていない場合は設定を削除する.
	if ( ! isset( $_POST[ $field ] ) ) {
		delete_post_meta( $post_id, $field );
		return;
	}

	// フォームからは配列で送信される。配列以外は不正な入力として保存しない.
	if ( ! is_array( $_POST[ $field ] ) ) {
		return;
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- lightning_sanitize_design_setting() で整形している.
	$field_value = lightning_sanitize_design_setting( wp_unslash( $_POST[ $field ] ) );

	// update_post_meta() はスラッシュ付きの値を期待するため、wp_slash() で渡す.
	update_post_meta( $post_id, $field, wp_slash( $field_value ) );
}
