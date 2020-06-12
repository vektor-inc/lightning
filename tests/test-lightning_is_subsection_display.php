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

		// Create test single page
		$post    = array(
			'post_title'   => 'single-page',
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$post_id = wp_insert_post( $post );

		update_option( 'page_on_front', $front_page_id ); // フロントに指定する固定ページ
		update_option( 'page_for_posts', $home_page_id ); // 投稿トップに指定する固定ページ
		update_option( 'show_on_front', 'page' ); // or posts

		$test_array = array(
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
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),

			// post archive
			array(
				'options'    => array(
					'layout' => array(
						'archive' => 'col-one-no-subsection',
					),
				),
				'target_url' => get_permalink( $home_page_id ),
				'correct'    => false,
			),
			// post archive
			array(
				'options'    => array(
					'layout' => array(
						'archive-post' => 'col-one-no-subsection',
					),
				),
				'target_url' => get_permalink( $home_page_id ),
				'correct'    => false,
			),
			// post single
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
		);

		foreach ( $test_array as $value ) {
			$options = $value['options'];
			update_option( 'lightning_theme_options', $options );

			// if ( $value['_wp_page_template'] ) {
			// update_post_meta( $front_page_id , '_wp_page_template', $value['_wp_page_template'] );
			// }

			// 古いセッティング値のコンバート（実際にはfunctions-compatible.phpで after_setup_theme で実行されている）
			lightning_options_compatible();

			// Move to test page
			$this->go_to( $value['target_url'] );

			$return = lightning_is_subsection_display();
			print 'url     :' . $_SERVER['REQUEST_URI'] . PHP_EOL;
			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			$this->assertEquals( $value['correct'], $return );
		}

	}

}
