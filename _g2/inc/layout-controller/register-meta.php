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
						'type'       => 'object',
						'properties' => array(
							'layout'              => array( 'type' => 'string' ),
							'hidden_page_header'  => array( 'type' => 'string' ),
							'hidden_breadcrumb'   => array( 'type' => 'string' ),
							'siteContent_padding' => array( 'type' => 'string' ),
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
