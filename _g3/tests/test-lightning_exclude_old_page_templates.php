<?php
class LightningExcludeOldPageTemplates extends WP_UnitTestCase {

	public static function setup_data() {
		// Create test page
		$post                   = array(
			'post_title'   => 'page',
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$data['page_id'] = wp_insert_post( $post );
		return $data;
	}

	public function test_lightning_exclude_old_page_templates() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_exclude_old_page_templates' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$data = self::setup_data();

		// 古いテンプレートのリスト
		$old_templates = array(
			'_g2/page-lp-builder.php' => __( 'Landing Page for Page Builder ( not recommended )', 'lightning' ),
			'_g2/page-lp.php' => __( 'Landing Page ( not recommended )', 'lightning' ),
			'_g2/page-onecolumn.php' => __( 'No sidebar ( not recommended )', 'lightning' ),
		);

		// テストする配列
		$test_array = array(
			// G3 かつデフォルトテンプレート
			array(
				'label'   => 'G3 かつデフォルトテンプレート',
				'post_id' => $data['page_id'],
				'options' => 'g3',
				'correct' => array(),
			),
			// G2 かつデフォルトテンプレート
			array(
				'label'   => 'G2 かつデフォルトテンプレート',
				'post_id' => $data['page_id'],
				'options' => 'g2',
				'correct' => $old_templates,
			),
			// G3 かつ page-lp-builder
			array(
				'label'             => 'G2 かつデフォルトテンプレート',
				'post_id'           => $data['page_id'],
				'_wp_page_template' => '_g2/page-lp-builder.php',
				'options'           => 'g3',
				'correct'           => $old_templates,
			),
			// G2 かつ page-lp-builder
			array(
				'label'             => 'G2 かつ page-lp-builder',
				'post_id'           => $data['page_id'],
				'_wp_page_template' => '_g2/page-lp-builder.php',
				'options'           => 'g2',
				'correct'           => $old_templates,
			),
			// G3 かつ page-lp
			array(
				'label'             => 'G3 かつ page-lp',
				'post_id'           => $data['page_id'],
				'_wp_page_template' => '_g2/page-lp.php',
				'options'           => 'g3',
				'correct'           => $old_templates,
			),
			// G2 かつ page-lp
			array(
				'label'             => 'G2 かつ page-lp',
				'post_id'           => $data['page_id'],
				'_wp_page_template' => '_g2/page-lp.php',
				'options'           => 'g2',
				'correct'           => $old_templates,
			),
			// G3 かつ page-onecolumn
			array(
				'label'             => 'G3 かつ page-onecolumn',
				'post_id'           => $data['page_id'],
				'_wp_page_template' =>'_g2/page-onecolumn.php',
				'options' => 'g3',
				'correct' => $old_templates,
			),
			// G2 かつ page-onecolumn
			array(
				'label'             => 'G2 かつ page-onecolumn',
				'post_id'           => $data['page_id'],
				'_wp_page_template' =>'_g2/page-onecolumn.php',
				'options' => 'g2',
				'correct' => $old_templates,
			),
		);

		// テスト開始
		foreach ( $test_array as $value ) {
			$options = $value['options'];
			update_option( 'lightning_theme_generation', $options );
			if ( isset( $value['_wp_page_template'] ) ) {
				update_post_meta( $value['post_id'], '_wp_page_template', $value['_wp_page_template'] );
			} else {
				delete_post_meta( $value['post_id'], '_wp_page_template' );
			}
			$theme = get_template();
			$post = get_post( $data['page_id'] );
			$return = lightning_exclude_old_page_templates( $old_templates, $theme, $post, 'page' );
			print $value['label'];
			print PHP_EOL;
			print PHP_EOL;
			print 'return  :';
			print PHP_EOL;
			var_dump( $return );
			print PHP_EOL;
			print 'correct  :';
			print PHP_EOL;
			var_dump( $value['correct'] );
			print PHP_EOL;
			$this->assertEquals( $value['correct'], $return );

		}
	}

}