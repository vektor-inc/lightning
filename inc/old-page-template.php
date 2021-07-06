<?php
/**
 * 古い固定ページ用テンプレートを G3 で使用できないように
 *
 * @param string[] $page_templates 固定ページ用のテンプレートリスト.
 * @param WP_Theme $theme テーマのオブジェクト
 * @param WP_Post|null $post 記事のオブジェクト
 * @param @param string $post_type 投稿タイプ
 */
function lightning_page_templates( $page_templates, $theme, $post, $post_type ) {


	// 現在のテンプレートを取得
	$current_templates = get_post_meta( $post->ID, '_wp_page_template' );

	// 世代を取得
	$generation = get_option( 'lightning_theme_generation' );

	// 古いテンプレートのリスト
	$old_templates = array(
		'_g2/page-lp-builder.php' => __( 'Landing Page for Page Builder ( not recommended )', 'lightning' ),
		'_g2/page-lp.php' => __( 'Landing Page ( not recommended )', 'lightning' ),
		'_g2/page-onecolumn.php' => __( 'No sidebar ( not recommended )', 'lightning' ),
	);

	// 古いテンプレートを使用しているか
	$old_template_using = false;
	foreach ( $current_templates as $current_template ) {
		if ( array_key_exists( $current_template, $old_templates ) ) {
			$old_template_using = true;
		}
	}

	// 世代が G2 以外かつ古いテンプレートを使っていない場合
	if ( ! empty( $generation ) && 'g2' !== $generation && false === $old_template_using ) {
		// 古いテンプレートを除外
		$page_templates = array_diff_assoc( $page_templates, $old_templates );
	}

	// $page_templates を返す
	return $page_templates;

}
add_filter( 'theme_page_templates', 'lightning_page_templates', 10, 4 );
