<?php
/**
 * Register post meta for REST API access (G2).
 * ブロックエディタからREST API経由でメタデータを読み書きするために登録する（G2）。
 *
 * @package lightning
 */

/**
 * Register _lightning_design_setting meta key for the REST API.
 *
 * @return void
 */
function lightning_g2_register_design_setting_meta() {
	$post_types = get_post_types( array( 'public' => true ) );

	foreach ( $post_types as $post_type ) {
		register_post_meta(
			$post_type,
			'_lightning_design_setting',
			array(
				'type'              => 'object',
				'single'            => true,
				'show_in_rest'      => array(
					'schema' => array(
						'type'                 => 'object',
						'properties'           => array(
							'layout'              => array( 'type' => 'string' ),
							'hidden_page_header'  => array( 'type' => 'string' ),
							'hidden_breadcrumb'   => array( 'type' => 'string' ),
							'siteContent_padding' => array( 'type' => 'string' ),
						),
						// Allow additional properties stored by plugins (e.g. section_base, header_trans)
						// and legacy keys (e.g. hidden_page_header_and_breadcrumb).
						// Without this, WP core auto-adds additionalProperties=false via
						// rest_default_additional_properties_to_false(), causing REST validation errors
						// for posts that have any extra keys in _lightning_design_setting.
						// プラグイン（Pro Unit 等）や旧バージョンが保存した追加プロパティを許容する。
						// 明示しないと WP コアが additionalProperties=false を自動付与し、
						// スキーマ外のキーを持つ投稿が保存不能になる。
						'additionalProperties' => array(
							'type' => 'string',
						),
					),
				),
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}
add_action( 'init', 'lightning_g2_register_design_setting_meta' );
