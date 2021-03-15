<?php
/*
	Add metabox
/*-------------------------------------------*/
add_action( 'admin_menu', 'lightning_add_design_meta_box' );

// add meta_box
function lightning_add_design_meta_box() {

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
			'side'
		);
	}
}

/*
	入力フィールドの生成
/*-------------------------------------------*/

function lightning_design_setting_meta_fields() {

	// CSRF対策の設定（フォームにhiddenフィールドとして追加するためのnonceを「'noncename__lightning_desigin」として設定）
	wp_nonce_field( wp_create_nonce( __FILE__ ), 'noncename__lightning_desigin' );

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
	  .Page Header
	/*-------------------------------------------*/
	$form .= '<h4>' . __( 'Page Header and Breadcrumb', 'lightning' ) . '</h4>';

	$form .= '<ul>';

	// Page Header
	$id    = '_lightning_design_setting[hidden_page_header]';
	$checked = '';
	if ( ! empty( $saved_post_meta['hidden_page_header'] ) ) {
		$checked = ' checked';
	}
	$form .= '<li class="vk_checklist_item vk_checklist_item-style-vertical">' . '<input type="checkbox" id="' . $id . '" name="' . $id . '" value="true"' . $checked . '  class="vk_checklist_item_input"><label for="' . $id . '" class="vk_checklist_item_label">' . wp_kses_post( __( 'Don\'t display Page Header', 'lightning' ) ) . '</label></li>';

	
	// Bread Crumb
	$id    = '_lightning_design_setting[hidden_breadcrumb]';
	$checked = '';
	if ( ! empty( $saved_post_meta['hidden_breadcrumb'] ) ) {
		$checked = ' checked';
	}
	$form .= '<li class="vk_checklist_item vk_checklist_item-style-vertical">' . '<input type="checkbox" id="' . $id . '" name="' . $id . '" value="true"' . $checked . '  class="vk_checklist_item_input"><label for="' . $id . '" class="vk_checklist_item_label">' . wp_kses_post( __( 'Don\'t display Breadcrumb', 'lightning' ) ) . '</label></li>';
	
	$form .= '</ul>';

	/*
	  .siteContent padding
	/*-------------------------------------------*/
	$form .= '<h4>' . __( 'Padding and margin setting', 'lightning' ) . '</h4>';

	$id    = '_lightning_design_setting[siteContent_padding]';
	$name  = '_lightning_design_setting[siteContent_padding]';
	$label = __( 'Delete siteContent padding', 'lightning' );

	$form .= '<ul>';

	$checked = '';
	if ( ! empty( $saved_post_meta['siteContent_padding'] ) ) {
		$checked = ' checked';
	}

	$form .= '<li class="vk_checklist_item vk_checklist_item-style-vertical">' . '<input type="checkbox" id="' . esc_attr( $id ) . '" name="' . esc_attr( $name ) . '" value="true"' . $checked . ' class="vk_checklist_item_input"><label for="' . esc_attr( $name ) . '" class="vk_checklist_item_label">' . wp_kses_post( $label ) . '</label></li>';
	$form .= '</ul>';

	echo $form;

	do_action( 'lightning_design_setting_meta_fields' );

}

/*
	Save meta
/*-------------------------------------------*/
add_action( 'save_post', 'lightning_design_setting_save' );

function lightning_design_setting_save( $post_id ) {

	// // Recieve nonce ( CSRF )
	$noncename__lightning_desigin = isset( $_POST['noncename__lightning_desigin'] ) ? $_POST['noncename__lightning_desigin'] : null;

	if ( ! wp_verify_nonce( $noncename__lightning_desigin, wp_create_nonce( __FILE__ ) ) ) {
		return $post_id;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id; }

	$field       = '_lightning_design_setting';
	$field_value = ( isset( $_POST[ $field ] ) ) ? $_POST[ $field ] : '';

	if ( get_post_meta( $post_id, $field ) == '' ) {
		add_post_meta( $post_id, $field, $field_value, true );

	} elseif ( $field_value == '' ) {
		delete_post_meta( $post_id, $field, get_post_meta( $post_id, $field, true ) );

	} else {
		update_post_meta( $post_id, $field, $field_value );
	}

}
