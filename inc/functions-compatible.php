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

/*
  Backward compatibility for header scroll option key
  ※ Lightning（G2/G3）の内部キーはタイポの `header_scrool` のままになっている。
  ※ Lightning Pro 側では `header_scroll` にリネーム済みのため、Pro で覚えた正スペル
  ※ `header_scroll` を Lightning に書いても効かない非対称が存在していた。
  ※ そこで `lightning_localize_options` フィルタの最終段で新キー `header_scroll` の値を
  ※ 旧キー `header_scrool` にコピーし、ユーザーがどちらのスペルでも書けるようにする。
  ※ Pro #358 と同じ目的だが、Lightning では同期方向が逆（新キー → 旧キー）となる点に注意。
/*-------------------------------------------*/
add_filter( 'lightning_localize_options', 'lightning_header_scrool_typo_compatible', PHP_INT_MAX, 1 );
/**
 * Sync the correctly-spelled "header_scroll" option key into the legacy "header_scrool" key.
 *
 * Lightning の内部キーはタイポの `header_scrool` が正規キーとして扱われており、
 * JS 側（lightning.min.js）もこのキー名を参照している。一方、ユーザーが Pro 側で
 * リネーム後の正スペル `header_scroll` を覚えている場合に Lightning でも同じ書き方
 * ができるよう、新キー `header_scroll` が設定されていれば旧キー `header_scrool` に
 * その値をコピーする。フィルタチェーンの最終段で実行されるよう priority を
 * PHP_INT_MAX に設定している。
 *
 * 正規キーは内部的には `header_scrool`（タイポ）、`header_scroll` は外部公開向けの
 * 正スペルキーとして扱う。両方のキーが同時に指定された場合は、ユーザーが意図して
 * 書いた可能性が高い「新キー（正スペル）」を後勝ちとする。
 *
 * @param array $options Localized options passed to the lightning-js script.
 * @return array Modified options with header_scrool synchronized from header_scroll when applicable.
 */
function lightning_header_scrool_typo_compatible( $options ) {
	// 配列でない場合はそのまま返す（防御的チェック）。
	if ( ! is_array( $options ) ) {
		return $options;
	}
	// 新キー（正スペル）`header_scroll` が指定されている場合のみ、
	// 旧キー（内部レガシー）`header_scrool` にその値をコピーする。
	// 旧キー単独で指定されている場合は従来通り旧キーがそのまま使われるため、何もしない。
	if ( array_key_exists( 'header_scroll', $options ) ) {
		$options['header_scrool'] = $options['header_scroll'];
	}
	return $options;
}
