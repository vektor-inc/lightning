<?php

/*
cd /app
bash setup-phpunit.sh
source ~/.bashrc
cd $(wp theme path --dir lightning)
phpunit
*/

class LightningBreadCrumbTest extends WP_UnitTestCase {

	function test_lightning_bread_crumb() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_bread_crumb' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		get_template_part( 'template-parts/breadcrumb' );
		print PHP_EOL;

		$before_option         = get_option( 'lightning_theme_options' );
		$before_page_for_posts = get_option( 'page_for_posts' ); // 投稿トップに指定するページ
		$before_page_on_front  = get_option( 'page_on_front' ); // フロントに指定する固定ページ
		$before_show_on_front  = get_option( 'show_on_front' ); // トップページ指定するかどうか page or posts

		/*** ↓↓ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) */

		register_post_type(
			'event',
			array(
				'label'       => 'event',
				'has_archive' => true,
				'public'      => true,
			)
		);
		register_taxonomy(
			'event_cat',
			'event',
			array(
				'label'        => 'Event Category',
				'rewrite'      => array( 'slug' => 'event_cat' ),
				'hierarchical' => true,
			)
		);

		// Create test category
		$catarr  = array(
			'cat_name' => 'test_category',
		);
		$cate_id = wp_insert_category( $catarr );

		$catarr        = array(
			'cat_name'        => 'test_category_child',
			'category_parent' => $cate_id,
		);
		$cate_child_id = wp_insert_category( $catarr );

		$catarr          = array(
			'cat_name' => 'no_post_category',
		);
		$cate_no_post_id = wp_insert_category( $catarr );

		// Create test term
		$args          = array(
			'slug' => 'event_category_name',
		);
		$term_info     = wp_insert_term( 'event_category_name', 'event_cat', $args );
		$event_term_id = $term_info['term_id'];

		// Create test post
		$post    = array(
			'post_title'    => 'test',
			'post_status'   => 'publish',
			'post_content'  => 'content',
			'post_category' => array( $cate_id ),
		);
		$post_id = wp_insert_post( $post );
		// 投稿にカテゴリー指定
		wp_set_object_terms( $post_id, 'test_category_child', 'category' );

		// Create test page
		$post           = array(
			'post_title'   => 'normal page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$normal_page_id = wp_insert_post( $post );

		$post = array(
			'post_title'   => 'child page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
			'post_parent'  => $normal_page_id,

		);
		$child_page_id = wp_insert_post( $post );

		// Create test home page
		$post         = array(
			'post_title'   => 'post_top',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$home_page_id = wp_insert_post( $post );

		// Create test home page
		$post          = array(
			'post_title'   => 'front_page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$front_page_id = wp_insert_post( $post );

		// custom post type.
		$post          = array(
			'post_title'   => 'event-test-post',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$event_post_id = wp_insert_post( $post );
		// set event category to event post
		wp_set_object_terms( $event_post_id, 'event_category_name', 'event_cat' );

		/*** ↑↑ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) */

		/*
		 Test Array
		/*--------------------------------*/
		$test_array = array(

			// Front page //////////////////////////////////////////////////////

			// array(
			// 'target_url'        => home_url( '/' ),
			// 'correct'           => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="'.home_url().'/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li></ol></div></div></div><!-- [ /.breadSection ] -->.',
			// ),

			// 404ページ
			array(
				'target_url' => home_url( '/?name=aaaaa' ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li><span>Not found</span><meta itemprop="position" content="2" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),
			// 検索結果（検索キーワードなし）
			array(
				'target_url' => home_url( '/?s=' ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li><span>Search Results</span><meta itemprop="position" content="2" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),
			// 検索結果（検索キーワード:aaa）
			array(
				'target_url' => home_url( '/?s=aaa' ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li><span>Search Results for : aaa</span><meta itemprop="position" content="2" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// 固定ページ
			// HOME > 固定ページ名
			array(
				'target_url' => get_permalink( $normal_page_id ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li><span>normal page</span><meta itemprop="position" content="2" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 固定ページ
			// HOME > 固定ページ名
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $normal_page_id ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li><span>normal page</span><meta itemprop="position" content="2" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// 固定ページの子ページ
			// HOME > 親ページ > 子ページ
			array(
				'target_url' => get_permalink( $child_page_id ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_permalink( $normal_page_id ) . '"><span itemprop="name">normal page</span></a><meta itemprop="position" content="2" /></li><li><span>child page</span><meta itemprop="position" content="3" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定
			// 固定ページの子ページ
			// HOME > 親ページ > 子ページ
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $child_page_id ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_permalink( $normal_page_id ) . '"><span itemprop="name">normal page</span></a><meta itemprop="position" content="2" /></li><li><span>child page</span><meta itemprop="position" content="3" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// トップページに最新の投稿（投稿トップ未指定） / 子カテゴリー
			// HOME > 親カテゴリー > 子カテゴリー
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_term_link( $cate_child_id, 'category' ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_term_link( $cate_id, 'category' ) . '"><span itemprop="name">test_category</span></a><meta itemprop="position" content="2" /></li><li><span>test_category_child</span><meta itemprop="position" content="3" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// トップページに最新の投稿 / 投稿トップページ無指定 / 記事ページ
			// HOME > 親カテゴリー > 子カテゴリー > 記事タイトル
			array(
				'options'    => array(
					'page_for_posts' => null,
				),
				'target_url' => get_permalink( $post_id ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_term_link( $cate_id, 'category' ) . '"><span itemprop="name">test_category</span></a><meta itemprop="position" content="2" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_term_link( $cate_child_id, 'category' ) . '"><span itemprop="name">test_category_child</span></a><meta itemprop="position" content="3" /></li><li><span>test</span><meta itemprop="position" content="4" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定
			// HOME > 投稿トップの固定ページ名
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_permalink( $home_page_id ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li><span>post_top</span><meta itemprop="position" content="2" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 子カテゴリー
			// HOME > 投稿トップの固定ページ名 > 親カテゴリー > 子カテゴリー
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_term_link( $cate_child_id, 'category' ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/?page_id=' . $home_page_id . '"><span itemprop="name">post_top</span></a><meta itemprop="position" content="2" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_term_link( $cate_id, 'category' ) . '"><span itemprop="name">test_category</span></a><meta itemprop="position" content="3" /></li><li><span>test_category_child</span><meta itemprop="position" content="4" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 投稿のないカテゴリーアーカイブページ
			// HOME > 投稿トップの固定ページ名 > 投稿のないカテゴリー名
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => get_term_link( $cate_no_post_id, 'category' ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/?page_id=' . $home_page_id . '"><span itemprop="name">post_top</span></a><meta itemprop="position" content="2" /></li><li><span>no_post_category</span><meta itemprop="position" content="3" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 年別アーカイブ
			// HOME > 投稿トップの固定ページ名 > アーカイブ名
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => $home_page_id,
				),
				'target_url' => home_url() . '/?post_type=post&year=' . date( 'Y' ) . '',
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/?page_id=' . $home_page_id . '"><span itemprop="name">post_top</span></a><meta itemprop="position" content="2" /></li><li><span>' . date( 'Y' ) . '</span><meta itemprop="position" content="3" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// トップページに固定ページ / 投稿トップページ無指定 / 年別アーカイブ
			// HOME > アーカイブ名
			array(
				'options'    => array(
					'page_on_front'  => $front_page_id,
					'show_on_front'  => 'page',
					'page_for_posts' => null,
				),
				'target_url' => home_url( '/' ) . '?post_type=post&year=' . date( 'Y' ) . '',
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li><span>' . date( 'Y' ) . '</span><meta itemprop="position" content="2" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// カスタム投稿タイプトップ
			// HOME > 投稿タイプ名
			array(
				'target_url' => home_url() . '/?post_type=event',
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li><span>event</span><meta itemprop="position" content="2" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// カスタム投稿タイプ / カスタム分類アーカイブ
			// HOME > 投稿タイプ名 > カスタム分類
			array(
				'target_url' => get_term_link( $event_term_id ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_post_type_archive_link( 'event' ) . '"><span itemprop="name">event</span></a><meta itemprop="position" content="2" /></li><li><span>event_category_name</span><meta itemprop="position" content="3" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// カスタム投稿タイプ / 年別アーカイブ
			// HOME > 投稿タイプ名 > アーカイブ名
			array(
				'target_url' => home_url() . '/?post_type=event&year=' . date( 'Y' ) . '',
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_post_type_archive_link( 'event' ) . '"><span itemprop="name">event</span></a><meta itemprop="position" content="2" /></li><li><span>' . date( 'Y' ) . '</span><meta itemprop="position" content="3" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

			// カスタム投稿タイプ / 記事詳細
			// HOME > 投稿タイプ名 > カスタム分類 > 記事タイトル
			array(
				'target_url' => get_permalink( $event_post_id ),
				'correct'    => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemscope itemtype="https://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . home_url() . '/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a><meta itemprop="position" content="1" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_post_type_archive_link( 'event' ) . '"><span itemprop="name">event</span></a><meta itemprop="position" content="2" /></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="' . get_term_link( $event_term_id ) . '"><span itemprop="name">event_category_name</span></a><meta itemprop="position" content="3" /></li><li><span>event-test-post</span><meta itemprop="position" content="4" /></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),

		);

		foreach ( $test_array as $value ) {
			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					update_option( $option_key, $option_value );
				}
			}

			// Move to test page
			$this->go_to( $value['target_url'] );
			$return = lightning_bread_crumb();

			// global $wp_query;
			// print '<pre style="text-align:left">';print_r($wp_query->query);print '</pre>';

			print PHP_EOL;
			print $value['target_url'] . PHP_EOL;
			print 'return------------------------------------' . PHP_EOL;
			print $return . PHP_EOL;
			print 'correct------------------------------------' . PHP_EOL;
			print $value['correct'] . PHP_EOL;
			print '------------------------------------' . PHP_EOL;

			$this->assertEquals( $value['correct'], $return );

			if ( ! empty( $value['options'] ) && is_array( $value['options'] ) ) {
				foreach ( $value['options'] as $option_key => $option_value ) {
					delete_option( $option_key );
				}
			}
		}

		/*
		 テスト前の値に戻す
		/*--------------------------------*/
		wp_delete_post( $post_id );
		wp_delete_post( $home_page_id );
		$cate_id = wp_delete_category( $catarr );
		update_option( 'lightning_theme_options', $before_option );
		update_option( 'page_for_posts', $before_page_for_posts );
		update_option( 'page_on_front', $before_page_on_front );
		update_option( 'show_on_front', $before_show_on_front );
	}

}
