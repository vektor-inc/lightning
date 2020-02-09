<?php

/*
cd /app
bash setup-phpunit.sh
source ~/.bashrc
cd $(wp theme path --dir lightning)
phpunit
*/

class LightningIsLayoutOnecolmunTest extends WP_UnitTestCase {

	function test_lightning_is_layout_onecolumn() {
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_is_layout_onecolumn' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$before_option         = get_option( 'lightning_theme_options' );
		$before_page_for_posts = get_option( 'page_for_posts' ); // 投稿トップに指定するページ
		$before_page_on_front  = get_option( 'page_on_front' ); // フロントに指定する固定ページ
		$before_show_on_front  = get_option( 'show_on_front' ); // トップページ指定するかどうか page or posts

		// Create test category
		$catarr  = array(
			'cat_name' => 'test_category',
		);
		$cate_id = wp_insert_category( $catarr );

		// Create test post
		$post    = array(
			'post_title'    => 'test',
			'post_status'   => 'publish',
			'post_content'  => 'content',
			'post_category' => array( $cate_id ),
		);
		$post_id = wp_insert_post( $post );

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
		update_option( 'page_on_front', $front_page_id ); // フロントに指定する固定ページ
		update_option( 'page_for_posts', $home_page_id ); // 投稿トップに指定する固定ページ
		update_option( 'show_on_front', 'page' ); // or posts

		/*
		 Test Array
		/*--------------------------------*/
		$test_array = array(
			// Front page
			array(
				'options'     => array(
					'layout' => array(
						'front-page' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'  => home_url( '/' ),
				'correct'     => true,
			),
			// Front page _ old one column setting
			array(
				'options'     => array(
					'top_sidebar_hidden' => true,
				),
				'_wp_page_template' => '',
				'target_url'  => home_url( '/' ),
				'correct'     => true,
			),
			// Front page _ old one column setting
			// トップ１カラム指定が古い状態で万が一残ってたとしても新しい設定が2カラムなら2カラムにする
			array(
				'options'     => array(
					'top_sidebar_hidden' => true,
					'layout'             => array(
						'front-page' => 'col-two',
					),
				),
				'_wp_page_template' => '',
				'target_url'  => home_url( '/' ),
				'correct'     => false,
			),
			// Front page _ カスタマイザー未指定 / 固定ページで１カラムテンプレートが選択（非推奨）
			array(
				'options'     => array(
					// 'top_sidebar_hidden' => true,
					// 'layout'             => array(
					// 	'front-page' => 'col-two',
					// ),
				),
				'_wp_page_template' => 'page-onecolumn.php',
				'target_url'  => home_url( '/' ),
				'correct'     => true,
			),
			// Front page _ カスタマイザーで２カラムが選択 / 固定ページ側で１カラムテンプレートが選択（非推奨）
			// 個別のページからの指定を優先させる
			array(
				'options'     => array(
					'layout'             => array(
						'front-page' => 'col-two',
					),
				),
				'_wp_page_template' => 'page-onecolumn.php',
				'target_url'  => home_url( '/' ),
				'correct'     => true,
			),
			// Search
			array(
				'options'     => array(
					'layout' => array(
						'search' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'  => home_url( '/' ) . '?s=aaa',
				'correct'     => true,
			),
			// 404
			array(
				'options'     => array(
					'layout' => array(
						'error404' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'  => home_url( '/' ) . '?name=abcdefg',
				'correct'     => true,
			),
			// Category
			array(
				'options'     => array(
					'layout' => array(
						'archive' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'  => get_term_link( $cate_id ),
				'correct'     => true,
			),
			// Post home
			array(
				'page_type'   => 'home',
				'options'     => array(
					'layout' => array(
						'archive' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'  => get_permalink( get_option( 'page_for_posts' ) ),
				'correct'     => true,
			),
			// Single
			array(
				'page_type'   => 'single',
				'options'     => array(
					'layout' => array(
						'single' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'  => get_permalink( $post_id ),
				'correct'     => true,
			),
		);

		foreach ( $test_array as $value ) {
			$options = $value['options'];
			update_option( 'lightning_theme_options', $options );

			if ( $value['_wp_page_template'] ) {
				update_post_meta( $front_page_id , '_wp_page_template', $value['_wp_page_template'] );
			}

			// 古いセッティング値のコンバート（実際にはfunctions-compatible.phpで after_setup_theme で実行されている）
			lightning_options_compatible();

			// Move to test page
			$this->go_to( $value['target_url'] );

			$return = lightning_is_layout_onecolumn();
			print 'url     :' . $_SERVER['REQUEST_URI'] . PHP_EOL;
			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
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
