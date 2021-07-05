<?php
/**
 * 古い固定ページ用テンプレートを G3 で使用できないように
 *
 * @param string[] $page_templates 固定ページ用のテンプレートリスト.
 */
function lightning_page_templates( $page_templates ) {

	// 世代を取得
	$generation = get_option( 'lightning_theme_generation' );

	// 古いテンプレートのリスト
	$old_templates = array(
		'_g2/page-lp-builder.php' => __( 'Landing Page for Page Builder ( not recommended )', 'lightning' ),
		'_g2/page-lp.php' => __( 'Landing Page ( not recommended )', 'lightning' ),
		'_g2/page-onecolumn.php' => __( 'No sidebar ( not recommended )', 'lightning' ),
	);

	// 世代が G2 以外なら
	if ( ! empty( $generation ) && 'g2' !== $generation ) {
		// 古いテンプレートを除外
		$page_templates = array_diff_assoc( $page_templates, $old_templates );
	}
	// $page_templates を返す
	return $page_templates;

}
add_filter( 'theme_page_templates', 'lightning_page_templates' );
