<?php
/**
 * Compatible functions
 *
 * @package lightning
 */

/**
 * This is converter that old options to new options value
 * This function is also used in test-lightning.php
 *
 * @return void
 */
function lightning_options_compatible() {
	$options = get_option( 'lightning_theme_options' );
	global $wp_query;

	$additional_post_types = get_post_types(
		array(
			'public'   => true,
			'_builtin' => false,
		),
		'names'
	);

	$archive_post_types = array( 'post' ) + $additional_post_types;

	if ( isset( $options['top_sidebar_hidden'] ) ) {
		if ( $options['top_sidebar_hidden'] ) {
			if ( isset( $options['layout']['front-page'] ) ) {
				if ( 'col-two' !== $options['layout']['front-page'] ) {
					$options['layout']['front-page'] = 'col-one-no-subsection';
				}
			} else {
				$options['layout']['front-page'] = 'col-one-no-subsection';
			}
		}
		$options['top_sidebar_hidden'] = null;
		update_option( 'lightning_theme_options', $options );
	}
	if ( isset( $options['layout']['archive'] ) ) {
		foreach ( $archive_post_types as $archive_post_type ) {
			// Old parameter exist && not default.
			if ( $options['layout']['archive'] && 'default' !== $options['layout']['archive'] ) {
				// New parameter is not exist.
				if ( empty( $options['layout'][ 'archive-' . $archive_post_type ] ) ) {
					$options['layout'][ 'archive-' . $archive_post_type ] = $options['layout']['archive'];
				}
			}
		}
		$options['layout']['archive'] = null;
		update_option( 'lightning_theme_options', $options );

	}
	if ( isset( $options['layout']['single'] ) && 'default' !== $options['layout']['single'] ) {
		foreach ( $archive_post_types as $archive_post_type ) {
			$options['layout'][ 'single-' . $archive_post_type ] = $options['layout']['single'];
		}
		$options['layout']['single'] = null;
		update_option( 'lightning_theme_options', $options );

	}
	if ( isset( $options['layout']['page'] ) && 'default' !== $options['layout']['page'] ) {
		$options['layout']['single-page'] = $options['layout']['page'];
		$options['layout']['page']        = null;
		update_option( 'lightning_theme_options', $options );
	}
}

/**
 * ページヘッダーとパンくずの非表示処理
 *
 * @return void
 */
function lightning_pageheader_and_breadcrumb_compatible() {

	$post_types = get_post_types();
	foreach ( $post_types as $key => $post_type ) {
		$args  = array(
			'post_type'      => $key,
			'posts_per_page' => -1,
		);
		$posts = get_posts( $args );

		foreach ( $posts as $post ) {
			setup_postdata( $post );
			$meta_value = $post->_lightning_design_setting;
			if ( ! empty( $meta_value['hidden_page_header_and_breadcrumb'] ) ) {
				unset( $meta_value['hidden_page_header_and_breadcrumb'] );
				$meta_value['hidden_page_header'] = true;
				$meta_value['hidden_breadcrumb']  = true;
				update_post_meta( $post->ID, '_lightning_design_setting', $meta_value );
			}
			wp_reset_postdata();
		}
	}
}

/**
 * テーア直下の固定ページテンプレートファイルが選択されている時にg2ディレクトリ参照に切り替える
 *
 * 本当はテンプレートはデフォルト指定にして meta 情報でレイアウト指定に切り替えたいが、子テーマに複製してカスタマイズしている人もいるため。
 * → G3に変更されるとヘッダーやフッターが困るが、そもそもG2からG3への切り替えは互換性が無いと告知しているので手動で切り替えてもらう
 */
function lightning_g2_template_compatible() {
	$args     = array(
		'post_type'      => 'page',
		'posts_per_page' => -1,
	);
	$wp_query = new WP_Query( $args );
	foreach ( $wp_query->posts as $post ) {
		$template = get_post_meta( $post->ID, '_wp_page_template', true );
		if ( 'page-onecolumn.php' === $template ) {
			update_post_meta( $post->ID, '_wp_page_template', '_g2/page-onecolumn.php' );
		} elseif ( 'page-lp.php' === $template ) {
			update_post_meta( $post->ID, '_wp_page_template', '_g2/page-lp.php' );
		} elseif ( 'page-lp-builder.php' === $template ) {
			update_post_meta( $post->ID, '_wp_page_template', '_g2/page-lp-builder.php' );
		}
	}
	wp_reset_postdata();
	wp_reset_query();
}

/**
 * Fall back for old hook name
 */
function lightning_g3_site_body_append_spell_miss_compatibility() {
	do_action( 'lightning_site_body_apepend', 'lightning_site_body_apepend' );
}
add_action( 'lightning_site_body_append', 'lightning_g3_site_body_append_spell_miss_compatibility' );
