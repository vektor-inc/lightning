<?php
/**
 * Test for vk_blocks_styles_after_lightning
 *
 * @package VK_Blocks_Styles_After_Lightning
 */

 class VKBlocksStylesAfterLightning extends WP_UnitTestCase {

	/**
	 * Set up the test environment before each test
	 */
	public function setUp(): void {
		parent::setUp();

		// Reset enqueued styles before each test
		wp_dequeue_style( 'vk-blocks-build-css' );
		wp_dequeue_style( 'lightning-common-style' );
	}

	/**
	 * Test when VK Blocks is installed and vk-blocks-build-css is enqueued
	 *
	 * @return void
	 */
	public function test_vk_blocks_installed() {
		global $vk_blocks_dir_url;
		$vk_blocks_dir_url = 'https://example.com/wp-content/plugins/vk-blocks/';

		// Enqueue styles for test
		wp_enqueue_style( 'vk-blocks-build-css', $vk_blocks_dir_url . 'build/block-build.css' );

		// Call the function to reorder styles
		vk_blocks_styles_after_lightning();

		// Check if vk-blocks-build-css is dequeued and enqueued again
		$this->assertTrue( wp_style_is( 'vk-blocks-build-css', 'enqueued' ) );
	}

	/**
	 * Test when VK Blocks is not installed
	 *
	 * @return void
	 */
	public function test_vk_blocks_not_installed() {
		global $vk_blocks_dir_url;
		$vk_blocks_dir_url = null; // VK Blocks is not installed

		// Call the function to reorder styles
		vk_blocks_styles_after_lightning();

		// Check if vk-blocks-build-css is not enqueued
		$this->assertFalse( wp_style_is( 'vk-blocks-build-css', 'enqueued' ), 'VK Blocks should not be enqueued when VK_BLOCKS_DIR_URL is not defined.' );
	}

	/**
	 * Test if theme styles are loaded before VK Blocks styles
	 *
	 * @return void
	 */
	public function test_style_order() {
		global $vk_blocks_dir_url;
		$vk_blocks_dir_url = 'https://example.com/wp-content/plugins/vk-blocks/';

		// Enqueue theme styles first
		wp_enqueue_style( 'lightning-common-style', 'https://example.com/wp-content/themes/lightning/_g3/assets/css/style-theme-json.css' );

		// Call the function to reorder vk-blocks styles
		vk_blocks_styles_after_lightning();

		// Ensure vk-blocks styles are loaded after theme styles
		$this->assertGreaterThan(
			wp_styles()->registered['lightning-common-style']->src,
			wp_styles()->registered['vk-blocks-build-css']->src,
			'VK Blocks styles should be enqueued after theme styles.'
		);
	}
}
