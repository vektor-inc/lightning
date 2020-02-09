<?php

/*
cd /app
bash setup-phpunit.sh
source ~/.bashrc
cd $(wp theme path --dir lightning)
phpunit
*/

class LightningIsSubsectionDisplayTest extends WP_UnitTestCase {

	function test_lightning_is_subsection_display(){

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
			'post_title'   => 'single-page',
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$post_id = wp_insert_post( $post );

		$test_array = array(
			array(
				'options'   => array(
					'layout'             => array(
						'front-page' => 'col-one',
					),
					'sidebar_display' => array(
						'front-page' => 'hidden',
					),
				),
				'target_url'  => home_url( '/' ),
				'correct'     => false,
			),
			// // Front page _ old one column setting
			array(
				'options'     => array(
					'layout'             => array(
						'front-page' => 'col-one',
					),
					'sidebar_display' => array(
						'front-page' => 'break', // If break...
					),
					'top_sidebar_hidden' => true,
				),
				'target_url'  => home_url( '/' ),
				'correct'     => true,
			),
			// Front page _ old one column setting
			// トップ１カラム指定が古い状態で万が一残ってたとしても新しい設定が2カラムなら2カラムにする
			array(
				'options'     => array(
					'sidebar_display' => array(
						'front-page' => 'break', // If break...
					),
					'top_sidebar_hidden' => true,
					'layout'             => array(
						'front-page' => 'col-two', // Top priority
					),
				),
				'_wp_page_template' => '',
				'target_url'  => home_url( '/' ),
				'correct'     => true,
			),
			// post single
			array(
				'options'   => array(
					'layout'             => array(
						'single' => 'col-one',
					),
					'sidebar_display' => array(
						'single' => 'hidden',
					),
				),
				'target_url'  => get_permalink( $post_id ),
				'correct'     => false,
			),
		);

		foreach ( $test_array as $value ) {
			$options = $value['options'];
			update_option( 'lightning_theme_options', $options );

			// if ( $value['_wp_page_template'] ) {
			// 	update_post_meta( $front_page_id , '_wp_page_template', $value['_wp_page_template'] );
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
