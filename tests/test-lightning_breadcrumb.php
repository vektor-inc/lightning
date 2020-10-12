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
        get_template_part('template-parts/breadcrumb');
        print PHP_EOL;

		$before_option         = get_option( 'lightning_theme_options' );
		$before_page_for_posts = get_option( 'page_for_posts' ); // 投稿トップに指定するページ
		$before_page_on_front  = get_option( 'page_on_front' ); // フロントに指定する固定ページ
		$before_show_on_front  = get_option( 'show_on_front' ); // トップページ指定するかどうか page or posts

		/*** ↓↓ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) ****/

		register_post_type(
			'event',
			array(
				'has_archive' => true,
				'public'      => true,
			)
		);
		register_taxonomy(
			'event_cat',
			'event',
			array(
				'label' => 'Event Category',
				'rewrite' => array( 'slug' => 'event_cat' ),
				'hierarchical' => true,
			)
		);

		// Create test category
		$catarr  = array(
			'cat_name' => 'test_category',
		);
		$cate_id = wp_insert_category( $catarr );

		$catarr  = array(
			'cat_name' => 'test_category_child',
			'category_parent' => $cate_id
		);
		$cate_child_id = wp_insert_category( $catarr );

		// Create test term
		$args  = array(
			'slug' => 'event_test',
		);
		$term_info = wp_insert_term( 'event_test', 'event_cat', $args );
		$term_id = $term_info['term_id'];

		// Create test post
		$post    = array(
			'post_title'    => 'test',
			'post_status'   => 'publish',
			'post_content'  => 'content',
			'post_category' => array( $cate_id ),
		);
		$post_id = wp_insert_post( $post );

		// Create test page
		$post           = array(
			'post_title'   => 'normal page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$normal_page_id = wp_insert_post( $post );

		$post           = array(
			'post_title'   => 'child page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
			'post_parent' => $normal_page_id,

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
			'post_title'   => 'event-test',
			'post_type'    => 'event',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$event_id = wp_insert_post( $post );
		// set event category to event post
		wp_set_object_terms( $event_id, 'event_test', 'event_cat' );
		// wp_set_object_terms( $event_id, 'event_test', 'event_cat' );

		update_option( 'page_on_front', $front_page_id ); // フロントに指定する固定ページ
		update_option( 'page_for_posts', $home_page_id ); // 投稿トップに指定する固定ページ
		update_option( 'show_on_front', 'page' ); // or posts

		/*** ↑↑ テスト用事前データ設定（ test_lightning_is_layout_onecolumn と test_lightning_is_subsection_display 共通 ) ****/

		/*
		 Test Array
		/*--------------------------------*/
		$test_array = array(

			// Front page //////////////////////////////////////////////////////
			
			// array(
			// 	'target_url'        => home_url( '/' ),
			// 	'correct'           => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="http://example.org/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a></li></ol></div></div></div><!-- [ /.breadSection ] -->.',
            // ),

            // 404ページ
			array(
				'target_url'        => home_url( '/?name=aaaaa' ),
				'correct'           => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="http://example.org/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a></li><li><span>Not found</span></li></ol></div></div></div><!-- [ /.breadSection ] -->',
            ),
            // 検索結果（検索キーワードなし）
			array(
				'target_url'        => home_url( '/?s=' ),
				'correct'           => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="http://example.org/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a></li><li><span>Search Results</span></li></ol></div></div></div><!-- [ /.breadSection ] -->',
            ),
            // 検索結果（検索キーワード:aaa）
			array(
				'target_url'        => home_url( '/?s=aaa' ),
				'correct'           => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="http://example.org/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a></li><li><span>Search Results for : aaa</span></li></ol></div></div></div><!-- [ /.breadSection ] -->',
			),


			// 投稿トップに固定ページ指定
            // HOME > 固定ページ名
			array(
				'options' => array(
					'page_for_posts' => $home_page_id,
				),
				'target_url'        => get_permalink( $home_page_id ),
				'correct'           => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="http://example.org/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a></li><li><span>post_top</span></li></ol></div></div></div><!-- [ /.breadSection ] -->',
            ),

            // 固定ページ
            // HOME > 固定ページ名
			array(
				'target_url'        => get_permalink( $normal_page_id ),
				'correct'           => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="http://example.org/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a></li><li><span>normal page</span></li></ol></div></div></div><!-- [ /.breadSection ] -->',
            ),

            // 固定ページの子ページ
			// HOME > 親ページ > 子ページ
			array(
				'target_url'        => get_permalink( $child_page_id ),
				'correct'           => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="http://example.org/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="'.get_permalink( $normal_page_id ).'"><span itemprop="name">normal page</span></a></li><li><span>child page</span></li></ol></div></div></div><!-- [ /.breadSection ] -->',
            ),

            // トップページに最新の投稿（投稿トップ未指定） / 子カテゴリー 
			// HOME > 親カテゴリー > 子カテゴリー
			// array(
			// 	'target_url'        => get_term_link( $cate_child_id, 'category' ),
			// 	'correct'           => '<!-- [ .breadSection ] --><div class="section breadSection"><div class="container"><div class="row"><ol class="breadcrumb" itemtype="http://schema.org/BreadcrumbList"><li id="panHome" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="http://example.org/"><span itemprop="name"><i class="fa fa-home"></i> HOME</span></a></li><li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem"><a itemprop="item" href="'.get_term_link( $cate_id, 'category' ).'"><span itemprop="name">test_category</span></a></li><li><span>test_category_child</span></li></ol></div></div></div><!-- [ /.breadSection ] -->',
            // ),


            // トップページに最新の投稿 / 投稿トップページ無指定 / 記事ページ
            // HOME > 親カテゴリー > 子カテゴリー > 記事タイトル      


            // トップページに固定ページ / 投稿トップに特定の固定ページ指定
            // HOME > 投稿トップの固定ページ名

            // トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 子カテゴリー 
			// HOME > 投稿トップの固定ページ名 > 親カテゴリー > 子カテゴリー
			

            // トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 年別アーカイブ
            // HOME > 投稿トップの固定ページ名 > アーカイブ名

            // トップページに固定ページ / 投稿トップに特定の固定ページ指定 / 記事ページ
            // HOME > 投稿トップの固定ページ名 > 親カテゴリー > 子カテゴリー > 記事タイトル


            // トップページに固定ページ / 投稿トップページ無指定 / 子カテゴリー 
			// HOME > 親カテゴリー > 子カテゴリー


            // トップページに固定ページ / 投稿トップページ無指定 / 年別アーカイブ 
            // HOME > アーカイブ名

            // トップページに固定ページ / 投稿トップページ無指定 / 記事ページ
            // HOME > 親カテゴリー > 子カテゴリー > 記事タイトル


            // カスタム投稿タイプトップ 
            // HOME > 投稿タイプ名

            // カスタム投稿タイプ / カスタム分類アーカイブ
            // HOME > 投稿タイプ名 > カスタム分類

            // カスタム投稿タイプ / 年別アーカイブ
            // HOME > 投稿タイプ名 > アーカイブ名
 
            // カスタム投稿タイプ / 記事詳細
            // HOME > 投稿タイプ名 > カスタム分類 > 記事タイトル

		);

		foreach ( $test_array as $value ) {
            if ( ! empty( $value['options'] ) ){
                $options = $value['options'];
                update_option( 'lightning_theme_options', $options );
            }

			// Move to test page
            $this->go_to( $value['target_url'] );
            $return = lightning_bread_crumb();

            global $wp_query;
            print '<pre style="text-align:left">';print_r($wp_query->query);print '</pre>';

            print PHP_EOL;
            print $value['target_url']. PHP_EOL;
            print 'return------------------------------------' . PHP_EOL;
            print $return. PHP_EOL;
            print 'correct------------------------------------' . PHP_EOL;
            print $value['correct']. PHP_EOL;
            print '------------------------------------' . PHP_EOL;

			$this->assertEquals( $value['correct'], $return );

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