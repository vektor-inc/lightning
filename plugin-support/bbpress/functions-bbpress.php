<?php
/**
 * bbPress Extension
 *
 * @package         Lightning
 */

/*
  CSS読み込み
/*-------------------------------------------*/
function ltg_bbp_load_css() {
	wp_enqueue_style( 'lightning-bbp-extension-style', get_template_directory_uri() . '/plugin-support/bbpress/css/style.css', array(), LIGHTNING_THEME_VERSION );
}
add_action( 'wp_enqueue_scripts', 'ltg_bbp_load_css' );

/*
  フォーラムのパンくずリスト書き換え
/*-------------------------------------------*/
add_filter(
	'lightning_panListHtml',
	function( $panListHtml ) {
		if ( function_exists( 'bbp_get_forum_post_type' ) ) {
			$postType = lightning_get_post_type();
			if ( $postType['slug'] == 'topic' ) {

				// Microdata
				// http://schema.org/BreadcrumbList
				/*-------------------------------------------*/
				$microdata_li = ' itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"';

				$before_html = '<!-- [ .breadSection ] -->
<div class="section breadSection">
<div class="container">
<div class="row">
<ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList">';

				$after_html = '</ol>
</div>
</div>
</div>
<!-- [ /.breadSection ] -->';

				$args        = array(
					// HTML
					'before'         => $before_html,
					'after'          => $after_html,
					'sep'            => '',
					'crumb_before'   => '<li' . $microdata_li . '><span>',
					'crumb_after'    => '</span></li>',
					'home_text'      => '<i class="fa fa-home"></i> HOME',
					'current_before' => '',
					'current_after'  => '',
				);
				$panListHtml = bbp_get_breadcrumb( $args );
			}
		}
		return $panListHtml;
	}
);

/*
  トピックの内容の前にトピックタイトル追加
/*-------------------------------------------*/
function ltg_bbp_add_topic_title() {
	$skin = get_option( 'lightning_design_skin' );
	if ( $skin != 'Variety' ) {
		echo '<div><h2>' . get_the_title() . '</h2></div>';
	}
}
add_action( 'bbp_template_before_single_topic', 'ltg_bbp_add_topic_title' );
