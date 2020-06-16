<?php

/*
cd /app
bash setup-phpunit.sh
source ~/.bashrc
cd $(wp theme path --dir lightning)
phpunit
*/

class LightningIsLayoutOnecolmunTest extends WP_UnitTestCase {
	
	/**
	 * is_front_page を定義してみる
	 * 出典： https://core.trac.wordpress.org/browser/tags/5.4/src/wp-includes/class-wp-query.php#L3891
	 */
	public function is_front_page() {
        // Most likely case.
        if ( 'posts' == get_option( 'show_on_front' ) && is_home() ) {
                return true;
        } elseif ( 'page' == get_option( 'show_on_front' ) && get_option( 'page_on_front' ) && is_page( get_option( 'page_on_front' ) ) ) {
                return true;
        } else {
                return false;
        }
	}

	function test_lightning_is_layout_onecolumn() {
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_is_layout_onecolumn' . PHP_EOL;
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

		update_option( 'page_on_front', $front_page_id ); // フロントに指定する固定ページ
		update_option( 'page_for_posts', $home_page_id ); // 投稿トップに指定する固定ページ
		update_option( 'show_on_front', 'page' ); // or posts

		/*
		 Test Array
		/*--------------------------------*/
		$test_array = array(

			// Front page //////////////////////////////////////////////////////
			
			// Front page _ カスタマイザの設定のみ
			array(
				'options'           => array(
					'layout' => array(
						'front-page' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),
			array(
				'options'           => array(
					'layout' => array(
						'front-page' => 'col-one-no-subsection',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),

			// Front page _ トップ : 固定ページ指定
			// Front page _ カスタマイザー : 未指定
			// Front page _ 固定ページ属性 : １カラムテンプレートが選択（非推奨）
			array(
				'options'           => array(),
				'_wp_page_template' => 'page-onecolumn.php',
				'show_on_front'		=> 'page',
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),
			// Front page _ トップ : 固定ページ指定
			// Front page _ カスタマイザー : ２カラム指定
			// Front page _ 固定ページ属性 : １カラムテンプレートが選択（非推奨）
			array(
				'options'           => array(
					'layout' => array(
						'front-page' => 'col-two',
					),
				),
				'_wp_page_template' => 'page-onecolumn.php',
				'show_on_front'		=> 'page',
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),
			// Front page _ トップ : 固定ページ指定
			// Front page _ カスタマイザー : 1カラム指定
			// Front page _ 固定ページ属性 : デフォルトテンプレートが選択
			array(
				'options'           => array(
					'layout' => array(
						'front-page' => 'col-one',
					),
				),
				'_wp_page_template' => 'page.php',
				'post_id'           => $front_page_id,
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),
			// Front page _ トップ : 固定ページ指定
			// Front page _ カスタマイザー : ２カラム指定
			// Front page _ 固定ページ属性 : デフォルトテンプレートが選択
			// Front page _ 固定ページデザイン設定 : １カラム指定
			array(
				'options'           => array(
					'layout' => array(
						'front-page' => 'col-two',
					),
				),
				'_wp_page_template' => 'page.php',
				'_lightning_design_setting' => array(
					'layout' => 'col-one',
				),
				'post_id'           => $front_page_id,
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),
			// Front page _ トップ : 最近の投稿指定
			// Front page _ カスタマイザー : トップ１カラムが選択 / 投稿アーカイブ : ２カラムが選択
			array(
				'options'           => array(
					'layout' => array(
						'front-page' => 'col-one',
						'archive-post' => 'col-two'
					),
				),
				'_wp_page_template' => '',
				'show_on_front'		=> 'posts',
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),

			// Post index //////////////////////////////////////////////////////

			// Post index _ カスタマイザー : 1カラム
			array(
				'options'           => array(
					'layout' => array(
						'archive-post' => 'col-one',
					),
				),
				'post_id'           => $home_page_id,
				'target_url'        => get_permalink( get_option( 'page_for_posts' ) ),
				'correct'           => true,
			),

			// Post index _ カスタマイザー : 1カラム
			// Post index _ 固定ページデザイン設定 : １カラム指定
			/*
			他の設定の仕様にあわせるなら投稿トップ（is_home）に指定した固定ページのレイアウト設定が効くべきではあるが、
			4系以前の仕様ではその制御が未指定で個別ページからのレイアウト指定が効いてないので、
			４系以前のロジックを引き継ぐとカスタマイザの指定指定優先としている
			*/
			array(
				'options'           => array(
					'layout' => array(
						'archive-post' => 'col-two',
					),
				),
				'_lightning_design_setting' => array(
					'layout' => 'col-one',
				),
				'post_id'           => $home_page_id,
				'target_url'        => get_permalink( get_option( 'page_for_posts' ) ),
				'correct'           => false,
			),

			// Search //////////////////////////////////////////////////////
			array(
				'options'           => array(
					'layout' => array(
						'search' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ) . '?s=aaa',
				'correct'           => true,
			),

			// 404 //////////////////////////////////////////////////////
			array(
				'options'           => array(
					'layout' => array(
						'error404' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ) . '?name=abcdefg',
				'correct'           => true,
			),

			// Archive //////////////////////////////////////////////////////

			// Post type archive
			array(
				'options'           => array(
					'layout' => array(
						'archive-post' => 'col-one',
					),
				),
				'target_url'        => home_url( '/' ) . '?post_type=post',
				'correct'           => true,
			),
			array(
				'options'           => array(
					'layout' => array(
						'archive-post' => 'col-one-no-subsection',
					),
				),
				'target_url'        => home_url( '/' ) . '?post_type=post',
				'correct'           => true,
			),
			array(
				'options'           => array(
					'layout' => array(
						'archive-event' => 'col-one',
					),
				),
				'target_url'        => home_url( '/' ) . '?post_type=event',
				'correct'           => true,
			),
			// Category
			array(
				'options'           => array(
					'layout' => array(
						'archive' => 'col-one',
					),
				),
				'target_url'        => get_term_link( $cate_id ),
				'correct'           => true,
			),

			// Singular //////////////////////////////////////////////////////

			// Page
			array(
				'options'           => array(
					'layout' => array(
						'single-page' => 'default',
					),
				),
				'_wp_page_template' => 'page-onecolumn.php',
				'_lightning_design_setting' => array(
					'layout' => 'col-one',
				),
				'target_url'        => get_permalink( $normal_page_id ),
				'post_id'           => $normal_page_id,
				'correct'           => true,
			),

			// Single
			array(
				'options'           => array(
					'layout' => array(
						'single-event' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => get_permalink( $event_id ),
				'post_id'           => $event_id,
				'correct'           => true,
			),
			// Single
			array(
				'options'           => array(
					'layout' => array(
						'single-post' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => get_permalink( $post_id ),
				'post_id'           => $post_id,
				'correct'           => true,
			),
			// Single
			array(
				'options'                   => array(
					'layout' => array(
						'single' => 'col-one',
					),
				),
				'_wp_page_template'         => '',
				'_lightning_design_setting' => array(
					'layout' => 'col-two',
				),
				'target_url'                => get_permalink( $post_id ),
				'post_id'                   => $post_id,
				'correct'                   => false,
			),
			array(
				'options'                   => array(
					'layout' => array(
						'single' => 'col-two',
					),
				),
				'_wp_page_template'         => '',
				'_lightning_design_setting' => array(
					'layout' => 'col-one',
				),
				'target_url'                => get_permalink( $post_id ),
				'post_id'                   => $post_id,
				'correct'                   => true,
			),

			////////////////////////////////////////////////////////////////
			// Legacy fallback
			////////////////////////////////////////////////////////////////

			// Front page _ old one column setting
			array(
				'options'           => array(
					'top_sidebar_hidden' => true,
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ),
				'correct'           => true,
			),
			// Front page _ old one column setting
			// トップ１カラム指定が古い状態で万が一残ってたとしても新しい設定が2カラムなら2カラムにする
			array(
				'options'           => array(
					'top_sidebar_hidden' => true,
					'layout'             => array(
						'front-page' => 'col-two',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ),
				'correct'           => false,
			),

			// Post type archive（ Old specification parameter ）
			array(
				'options'           => array(
					'layout' => array(
						'archive' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ) . '?post_type=post',
				'correct'           => true,
			),
			array(
				'options'           => array(
					'layout' => array(
						'archive' => 'col-one-no-subsection',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ) . '?post_type=post',
				'correct'           => true,
			),
			// Post type archive（ Old specification parameter ）
			array(
				'options'           => array(
					'layout' => array(
						'archive' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ) . '?post_type=event',
				'correct'           => true,
			),
			// Post type archive（ New parameter with Old specification parameter ）
			array(
				'options'           => array(
					'layout' => array(
						'archive' => 'col-two',
						'archive-event' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => home_url( '/' ) . '?post_type=event',
				'correct'           => true,
			),

			// Page（ Old specification parameter ）
			array(
				'options'           => array(
					'layout' => array(
						'page' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => get_permalink( $normal_page_id ),
				'post_id'           => $normal_page_id,
				'correct'           => true,
			),
			// Page（ Old specification parameter with post_meta ）
			array(
				'options'                   => array(
					'layout' => array(
						'page' => 'col-one',
					),
				),
				'_wp_page_template'         => '',
				'_lightning_design_setting' => array(
					'layout' => 'col-two',
				),
				'target_url'                => get_permalink( $normal_page_id ),
				'post_id'                   => $normal_page_id,
				'correct'                   => false,
			),
			// Single（ Old specification parameter ）
			array(
				'options'           => array(
					'layout' => array(
						'single' => 'col-one',
					),
				),
				'_wp_page_template' => '',
				'target_url'        => get_permalink( $post_id ),
				'post_id'           => $post_id,
				'correct'           => true,
			),
		);

		foreach ( $test_array as $value ) {
			$options = $value['options'];
			update_option( 'lightning_theme_options', $options );

			if ( isset( $value['_wp_page_template'] ) ) {
				update_post_meta( $front_page_id, '_wp_page_template', $value['_wp_page_template'] );
			}
			if ( isset( $value['_lightning_design_setting'] ) ) {
				update_post_meta( $value['post_id'], '_lightning_design_setting', $value['_lightning_design_setting'] );
			}
			if ( isset( $value['show_on_front'] ) && $value['show_on_front'] === 'posts') {
				delete_option( 'show_on_front', 'posts' );
			} else {
				update_option( 'show_on_front', 'page' );
			}

			// 古いセッティング値のコンバート（実際にはfunctions-compatible.phpで after_setup_theme で実行されている）
			lightning_options_compatible();

			// Move to test page
			$this->go_to( $value['target_url'] );

			$return = lightning_is_layout_onecolumn();
			print 'url     :' . $_SERVER['REQUEST_URI'] . PHP_EOL;
			print 'return  :' . $return . PHP_EOL;
			print 'correct :' . $value['correct'] . PHP_EOL;
			print 'is_front :' . self::is_front_page() . PHP_EOL;
			print 'is_home :' . is_home() . PHP_EOL;
			print 'is_archive :' . is_archive() . PHP_EOL;
			if ( isset( $value['charck_key'] ) ){
				print 'charck_key :' .  $value['charck_key'] . PHP_EOL;
			}
			$this->assertEquals( $value['correct'], $return );

			if ( !empty( $value['_wp_page_template'] ) ) {
				delete_post_meta( $front_page_id, '_wp_page_template' );
			}
			if ( isset( $value['_lightning_design_setting'] ) ) {
				delete_post_meta( $value['post_id'], '_lightning_design_setting' );
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