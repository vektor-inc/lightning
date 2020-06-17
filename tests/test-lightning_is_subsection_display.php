<?php

/*
cd /app
bash setup-phpunit.sh
source ~/.bashrc
cd $(wp theme path --dir lightning)
phpunit
*/

class LightningIsSubsectionDisplayTest extends WP_UnitTestCase {

	function test_lightning_is_subsection_display() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_is_subsection_display' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$before_option         = get_option( 'lightning_theme_options' );
		$before_page_for_posts = get_option( 'page_for_posts' ); // 投稿トップに指定するページ
		$before_page_on_front  = get_option( 'page_on_front' ); // フロントに指定する固定ページ
		$before_show_on_front  = get_option( 'show_on_front' ); // トップページ指定するかどうか page or posts

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
				'label' => __( 'Event Category' ),
				'rewrite' => array( 'slug' => 'event_cat' ),
				'hierarchical' => true,
			)
		);

		// Create test category
		$catarr  = array(
			'cat_name' => 'test_category',
		);
		$cate_id = wp_insert_category( $catarr );

		// Create test term
		$args  = array(
			'slug' => 'event_test',
		);
		$term_id = wp_insert_term( 'event_test', 'event_cat', $args );

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
		// set event category
		wp_set_object_terms( $event_id, 'event_test', 'event_cat' );

		update_option( 'page_on_front', $front_page_id ); // フロントに指定する固定ページ
		update_option( 'page_for_posts', $home_page_id ); // 投稿トップに指定する固定ページ
		update_option( 'show_on_front', 'page' ); // or posts

		/*
		 Test Array
		/*--------------------------------*/
		
		$test_array = array(

			// Front page //////////////////////////////////////////////////////

			array(
				'options'    => array(
					'layout' => array(
						'front-page' => 'col-one-no-subsection',
					),
				),
				'target_url' => home_url( '/' ),
				'correct'    => false,
			),
			// Front page _ old one column setting
			// * No subsection hidden
			array(
				'options'    => array(
					'layout'             => array(
						'front-page' => 'col-one', // auto convert to col-one-no-subsection
					),
					'top_sidebar_hidden' => true,
				),
				'target_url' => home_url( '/' ),
				'correct'    => false,
			),
			// Front page _ old one column setting
			// トップ１カラム指定が古い状態で万が一残ってたとしても新しい設定が2カラムなら2カラムにする
			array(
				'options'           => array(
					'top_sidebar_hidden' => true,
					'layout'             => array(
						'front-page' => 'col-two', // Top priority
					),
				),
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),

			// Front page _ トップ : 固定ページ指定
			// Front page _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// Front page _ カスタマイザー : 固定ページは2カラム指定
			// Front page _ 返り値 : 非表示
			array(
				'show_on_front'		=> 'page',
				'options'    => array(
					'layout' => array(
						'front-page' => 'col-one-no-subsection',
						'single-page' => 'col-two',
					),
				),
				'target_url' => home_url( '/' ),
				'correct'    => false,
			),

			// Front page _ トップ : 固定ページ指定
			// Front page _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// Front page _ 固定ページデザイン設定 : １カラム指定
			// Front page _ 返り値 : 表示
			array(
				'show_on_front'		=> 'page',
				'options'    => array(
					'layout' => array(
						'front-page' => 'col-one-no-subsection',
					),
				),
				'_lightning_design_setting' => array(
					'layout' => 'col-one',
				),
				'post_id'           => $front_page_id,
				'target_url' => home_url( '/' ),
				'correct'    => true,
			),

			// Front page _ トップ : 固定ページ指定
			// Front page _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// Front page _ 固定ページデザイン設定 : ２カラム指定
			// Front page _ 返り値 : 表示
			array(
				'show_on_front'		=> 'page',
				'options'    => array(
					'layout' => array(
						'front-page' => 'col-one-no-subsection',
					),
				),
				'_lightning_design_setting' => array(
					'layout' => 'col-two',
				),
				'post_id'			=> $front_page_id,
				'target_url' => home_url( '/' ),
				'correct'    => true,
			),

			// Archive //////////////////////////////////////////////////////

			// is_home _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// is_home _ 返り値 : 非表示
			array(
				'options'    => array(
					'layout' => array(
						'archive-post' => 'col-one-no-subsection',
					),
				),
				'target_url' => get_permalink( $home_page_id ),
				'correct'    => false,
			),

			// is_post_type_archive('post') _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// is_post_type_archive('post') _ 返り値 : 非表示
			array(
				'options'    => array(
					'layout' => array(
						'archive-post' => 'col-one-no-subsection',
					),
				),
				'target_url'        => get_post_type_archive_link( 'post' ),
				'correct'    => false,
			),

			// is_category _ カスタマイザー : サブセクション無し
			// is_category _ 返り値 : 非表示
			array(
				'options'           => array(
					'layout' => array(
						'archive-post' => 'col-one-no-subsection',
					),
				),
				'target_url'        => get_term_link( $cate_id ),
				'correct'           => false,
			),

			// is_post_type_archive('event') _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// is_post_type_archive('event') _ 返り値 : 非表示
			array(
				'options'    => array(
					'layout' => array(
						'archive-event' => 'col-one-no-subsection',
					),
				),
				'target_url'        => get_post_type_archive_link( 'event' ),
				'correct'    => false,
			),

			// is_tax( 'event_cat' ) _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// is_tax( 'event_cat' ) _ 返り値 : 非表示

			/******************************************/
			// array(
			// 	'options'           => array(
			// 		'layout' => array(
			// 			'archive-event' => 'col-one-no-subsection',
			// 		),
			// 	),
			// 	'target_url'        => get_term_link( $term_id ),
			// 	'correct'           => false,
			// ),
			/******************************************/


			// singular //////////////////////////////////////////////////////

			array(
				'options'    => array(
					'layout' => array(
						'single-post' => 'col-two',
					),
				),
				'target_url' => get_permalink( $post_id ),
				'correct'    => true,
			),
			// single _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// single _ 投稿ページデザイン設定 : ２カラム（サブ有り）
			// single _ 返り値 : 表示
			array(
				'options'    => array(
					'layout' => array(
						'single-post' => 'col-one-no-subsection',
					),
				),
				'_lightning_design_setting' => array(
					'layout' => 'col-two',
				),
				'post_id'    => $post_id,
				'target_url' => get_permalink( $post_id ),
				'correct'    => true,
			),
			// page _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// page _ 固定ページデザイン設定 : ２カラム（サブ有り）
			// page _ 返り値 : 表示
			array(
				'options'    => array(
					'layout' => array(
						'single-page' => 'col-one-no-subsection',
					),
				),
				'_lightning_design_setting' => array(
					'layout' => 'col-two',
				),
				'post_id'    => $normal_page_id,
				'target_url' => get_permalink( $normal_page_id ),
				'correct'    => true,
			),
			// page _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// page _ 固定ページ属性 : １カラムテンプレートが選択（サブ無し）
			// page _ 返り値 : 非表示
			array(
				'options'    => array(
					'layout' => array(
						'single-page' => 'col-one-no-subsection',
					),
				),
				'_wp_page_template' => 'page-onecolumn.php',
				'post_id'    => $normal_page_id,
				'target_url' => get_permalink( $normal_page_id ),
				'correct'    => false,
			),
			// page _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// page _ 固定ページ属性 : １カラムテンプレートが選択（サブ無し）
			// page _ 固定ページデザイン設定 : ２カラム（サブ有り）
			// page _ 返り値 : 表示
			array(
				'options'    => array(
					'layout' => array(
						'single-page' => 'col-one-no-subsection',
					),
				),
				'_wp_page_template' => 'page-onecolumn.php',
				'_lightning_design_setting' => array(
					'layout' => 'col-two',
				),
				'post_id'    => $normal_page_id,
				'target_url' => get_permalink( $normal_page_id ),
				'correct'    => true,
			),

			////////////////////////////////////////////////////////////////
			// Legacy fallback
			////////////////////////////////////////////////////////////////

			// is_home
			array(
				'options'    => array(
					'layout' => array(
						'archive' => 'col-one-no-subsection',
					),
				),
				'target_url' => get_permalink( $home_page_id ),
				'correct'    => false,
			),

			array(
				'options'    => array(
					'layout' => array(
						'single' => 'col-two',
					),
				),
				'target_url' => get_permalink( $post_id ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'layout' => array(
						'single' => 'col-one',
					),
				),
				'target_url' => get_permalink( $post_id ),
				'correct'    => true,
			),
			array(
				'options'    => array(
					'layout' => array(
						'single' => 'col-one-no-subsection',
					),
				),
				'target_url' => get_permalink( $post_id ),
				'correct'    => false,
			),
			// page _ カスタマイザー : 1カラムサブセクション無し（サブ無し）
			// page _ 固定ページ属性 : テンプレート指定なし（デフォルトテンプレートが選択）
			// page _ 固定ページデザイン設定 : ２カラム（サブ有り）
			// page _ 返り値 : 表示
			array(
				'options'    => array(
					'layout' => array(
						'page' => 'col-one-no-subsection',
					),
				),
				'_lightning_design_setting' => array(
					'layout' => 'col-two',
				),
				'post_id'    => $normal_page_id,
				'target_url' => get_permalink( $normal_page_id ),
				'correct'    => true,
			),
		);

		foreach ( $test_array as $value ) {
			$options = $value['options'];
			update_option( 'lightning_theme_options', $options );

			if ( isset( $value['_wp_page_template'] ) ) {
				update_post_meta( $value['post_id'], '_wp_page_template', $value['_wp_page_template'] );
			}
			if ( isset( $value['_lightning_design_setting'] ) ) {
				update_post_meta( $value['post_id'], '_lightning_design_setting', $value['_lightning_design_setting'] );
			}


			// 古いセッティング値のコンバート（実際にはfunctions-compatible.phpで after_setup_theme で実行されている）
			lightning_options_compatible();

			// Move to test page
			$this->go_to( $value['target_url'] );

			$return = lightning_is_subsection_display();
			print 'url     :' . $_SERVER['REQUEST_URI'] . PHP_EOL;
			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $return );
			if ( isset( $value['charck_key'] ) ){
				print 'charck_key :' .  $value['charck_key'] . PHP_EOL;
			}
			if ( isset( $value['_wp_page_template'] ) ) {
				delete_post_meta( $value['post_id'], '_wp_page_template', $value['_wp_page_template'] );
			}
			if ( isset( $value['_lightning_design_setting'] ) ) {
				delete_post_meta( $value['post_id'], '_lightning_design_setting', $value['_lightning_design_setting'] );
			}

		}

	}

}
