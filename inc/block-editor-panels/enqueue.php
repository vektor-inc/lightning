<?php
/**
 * Block Editor Panel - Enqueue and Meta Registration
 * ブロックエディターパネル - スクリプト登録とメタ登録
 *
 * Registers the _lightning_design_setting meta for REST API and
 * enqueues the block editor sidebar panel script.
 * _lightning_design_setting メタを REST API に登録し、
 * ブロックエディターサイドバーパネルスクリプトを読み込む。
 *
 * @package Lightning
 */

/**
 * Register post meta for block editor panel.
 * ブロックエディターパネル用のポストメタを登録する。
 *
 * Registers _lightning_design_setting as an object meta with all possible
 * sub-fields for both G2 and G3 generations.
 * G2/G3 両世代の全サブフィールドを含むオブジェクトメタとして
 * _lightning_design_setting を登録する。
 *
 * @return void
 */
function lightning_register_panel_meta() {
	$post_types = get_post_types( array( 'public' => true ) );
	foreach ( $post_types as $post_type ) {
		register_post_meta(
			$post_type,
			'_lightning_design_setting',
			array(
				'type'          => 'object',
				'single'        => true,
				'show_in_rest'  => array(
					'schema' => array(
						'type'       => 'object',
						'properties' => array(
							'layout'              => array( 'type' => 'string' ),
							'site_body_padding'   => array( 'type' => 'string' ),
							'hidden_page_header'  => array( 'type' => 'string' ),
							'hidden_breadcrumb'   => array( 'type' => 'string' ),
							'siteContent_padding' => array( 'type' => 'string' ),
						),
					),
				),
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'lightning_register_panel_meta' );

/**
 * Enqueue block editor panel script.
 * ブロックエディターパネルスクリプトを読み込む。
 *
 * Loads the compiled JS for the Lightning design setting sidebar panel
 * and passes generation info and i18n strings via wp_localize_script.
 * Lightning デザイン設定サイドバーパネル用のコンパイル済み JS を読み込み、
 * wp_localize_script で世代情報と翻訳文字列を渡す。
 *
 * @return void
 */
function lightning_enqueue_editor_panel() {
	$screen = get_current_screen();
	if ( ! $screen || ! $screen->is_block_editor || empty( $screen->post_type ) ) {
		return;
	}

	$asset_path = get_template_directory() . '/inc/block-editor-panels/build/index.asset.php';
	if ( ! file_exists( $asset_path ) ) {
		return;
	}
	$asset = include $asset_path;

	wp_enqueue_script(
		'lightning-editor-panel',
		get_template_directory_uri() . '/inc/block-editor-panels/build/index.js',
		$asset['dependencies'],
		$asset['version'],
		true
	);

	// Detect G2 or G3 / G2 か G3 かを判定する.
	$generation = function_exists( 'lightning_is_g3' ) && lightning_is_g3() ? 'g3' : 'g2';

	wp_localize_script(
		'lightning-editor-panel',
		'lightningPanelData',
		array(
			'generation' => $generation,
			'panelTitle' => __( 'Lightning design setting', 'lightning' ),
			'i18n'       => array(
				'layoutSetting'        => __( 'Layout setting', 'lightning' ),
				'useCommon'            => __( 'Use common settings', 'lightning' ),
				'twoCol'               => __( '2 column', 'lightning' ),
				'oneCol'               => __( '1 column', 'lightning' ),
				'oneColSidebar'        => __( '1 column (with sidebar element)', 'lightning' ),
				'paddingSetting'       => __( 'Padding and margin setting', 'lightning' ),
				'deletePadding'        => __( 'Delete site-body padding', 'lightning' ),
				'deleteSiteContent'    => __( 'Delete siteContent padding', 'lightning' ),
				'pageHeaderBreadcrumb' => __( 'Page Header and Breadcrumb', 'lightning' ),
				'hidePageHeader'       => __( "Don't display Page Header", 'lightning' ),
				'hideBreadcrumb'       => __( "Don't display Breadcrumb", 'lightning' ),
			),
		)
	);
}
add_action( 'enqueue_block_editor_assets', 'lightning_enqueue_editor_panel' );
