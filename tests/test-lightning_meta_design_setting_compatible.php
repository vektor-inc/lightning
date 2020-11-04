<?php

/*
cd /app
bash setup-phpunit.sh
source ~/.bashrc
cd wp-content/themes/lightning
phpunit
*/

class LightningMetaDesiginSettingCompatibleTest extends WP_UnitTestCase {
	
	function test_lightning_pageheader_and_breadcrumb_compatible() {
		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'lightning_pageheader_and_breadcrumb_compatible' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// 
		delete_option('lightning_old_options');

		// Create test page
		$post           = array(
			'post_title'   => 'test page',
			'post_type'    => 'post',
			'post_status'  => 'publish',
			'post_content' => 'content',
		);
		$test_post_id = wp_insert_post( $post );
		$value['hidden_page_header_and_breadcrumb'] = true;
		update_post_meta( $test_post_id, '_lightning_design_setting', $value );

		// ジャッジが反応するかどうか
		$return = VK_Old_Options_Notice::option_judgment( 'judge' );
		$this->assertEquals( true, $return );

		VK_Old_Options_Notice::option_judgment( 'update' );
		$meta = get_post_meta( $test_post_id, '_lightning_design_setting', true );
		
		print '<pre style="text-align:left">';print_r($meta );print '</pre>';
		$this->assertEquals( true, $meta['hidden_page_header'] );
		$this->assertEquals( true, $meta['hidden_breadcrumb'] );

	}

}