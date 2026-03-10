<?php
/**
 * Test for LTG_G3_Slider preload and fetchpriority
 *
 * @package vektor-inc/lightning
 */

class LTG_G3_Slider_Preload_Test extends WP_UnitTestCase {

	/**
	 * Front page ID
	 *
	 * @var int
	 */
	private static $front_page_id;

	/**
	 * Set up test fixtures.
	 */
	public static function wpSetUpBeforeClass( $factory ) {
		self::$front_page_id = $factory->post->create(
			array(
				'post_type'   => 'page',
				'post_title'  => 'Front Page',
				'post_status' => 'publish',
			)
		);
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', self::$front_page_id );
	}

	/**
	 * Clean up after each test.
	 */
	public function tearDown(): void {
		delete_option( 'lightning_theme_options' );
		wp_cache_flush();
		parent::tearDown();
	}

	/**
	 * Helper to set slider options.
	 *
	 * @param array $overrides Options to merge with defaults.
	 */
	private function set_slider_options( $overrides = array() ) {
		$defaults = lightning_g3_slider_default_options();
		$options  = array_merge( $defaults, $overrides );
		update_option( 'lightning_theme_options', $options );
		wp_cache_flush();
	}

	/**
	 * Test get_first_slide_index()
	 */
	public function test_get_first_slide_index() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::get_first_slide_index()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// Slide 1 has image (default) -> returns 1.
		$this->set_slider_options();
		$return = LTG_G3_Slider::get_first_slide_index();
		print PHP_EOL . 'All slides set: return = ' . var_export( $return, true ) . ', correct = 1' . PHP_EOL;
		$this->assertSame( 1, $return );

		// Slide 1 empty, Slide 2 has image -> returns 2.
		$this->set_slider_options(
			array(
				'top_slide_image_1' => '',
				'top_slide_image_2' => 'http://example.com/image2.jpg',
			)
		);
		$return = LTG_G3_Slider::get_first_slide_index();
		print 'Slide 1 empty, Slide 2 set: return = ' . var_export( $return, true ) . ', correct = 2' . PHP_EOL;
		$this->assertSame( 2, $return );

		// Slide 1 & 2 empty, Slide 3 has image -> returns 3.
		$this->set_slider_options(
			array(
				'top_slide_image_1' => '',
				'top_slide_image_2' => '',
				'top_slide_image_3' => 'http://example.com/image3.jpg',
			)
		);
		$return = LTG_G3_Slider::get_first_slide_index();
		print 'Slide 1,2 empty, Slide 3 set: return = ' . var_export( $return, true ) . ', correct = 3' . PHP_EOL;
		$this->assertSame( 3, $return );

		// All slides empty -> returns false.
		$this->set_slider_options(
			array(
				'top_slide_image_1' => '',
				'top_slide_image_2' => '',
				'top_slide_image_3' => '',
			)
		);
		$return = LTG_G3_Slider::get_first_slide_index();
		print 'All slides empty: return = ' . var_export( $return, true ) . ', correct = false' . PHP_EOL;
		$this->assertFalse( $return );
	}

	/**
	 * Test preload_first_slide_image() outputs correct preload tags on front page.
	 */
	public function test_preload_first_slide_image_on_front_page() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::preload_first_slide_image() on front page' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->go_to( home_url( '/' ) );

		// PC image only (no mobile) -> single preload without media query.
		$this->set_slider_options(
			array(
				'top_slide_image_1'        => 'http://example.com/pc.jpg',
				'top_slide_image_mobile_1' => '',
			)
		);
		ob_start();
		LTG_G3_Slider::preload_first_slide_image();
		$output = ob_get_clean();
		print PHP_EOL . 'PC only: ' . trim( $output ) . PHP_EOL;
		$this->assertStringContainsString( 'href="http://example.com/pc.jpg"', $output );
		$this->assertStringContainsString( 'fetchpriority="high"', $output );
		$this->assertStringNotContainsString( 'media=', $output );

		// PC and mobile same -> single preload without media query.
		$this->set_slider_options(
			array(
				'top_slide_image_1'        => 'http://example.com/same.jpg',
				'top_slide_image_mobile_1' => 'http://example.com/same.jpg',
			)
		);
		ob_start();
		LTG_G3_Slider::preload_first_slide_image();
		$output = ob_get_clean();
		print 'PC = Mobile: ' . trim( $output ) . PHP_EOL;
		$this->assertStringContainsString( 'href="http://example.com/same.jpg"', $output );
		$this->assertStringNotContainsString( 'media=', $output );

		// PC and mobile differ -> two preload tags with media queries.
		$this->set_slider_options(
			array(
				'top_slide_image_1'        => 'http://example.com/pc.jpg',
				'top_slide_image_mobile_1' => 'http://example.com/mobile.jpg',
			)
		);
		ob_start();
		LTG_G3_Slider::preload_first_slide_image();
		$output = ob_get_clean();
		print 'PC != Mobile: ' . trim( $output ) . PHP_EOL;
		$this->assertStringContainsString( 'href="http://example.com/mobile.jpg"', $output );
		$this->assertStringContainsString( 'media="(max-width: 767px)"', $output );
		$this->assertStringContainsString( 'href="http://example.com/pc.jpg"', $output );
		$this->assertStringContainsString( 'media="(min-width: 768px)"', $output );
	}

	/**
	 * Test preload_first_slide_image() outputs nothing on non-front page.
	 */
	public function test_preload_not_output_on_non_front_page() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::preload_first_slide_image() on non-front page' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$post_id = self::factory()->post->create(
			array(
				'post_type'   => 'post',
				'post_status' => 'publish',
			)
		);
		$this->go_to( get_permalink( $post_id ) );

		$this->set_slider_options(
			array(
				'top_slide_image_1' => 'http://example.com/pc.jpg',
			)
		);
		ob_start();
		LTG_G3_Slider::preload_first_slide_image();
		$output = ob_get_clean();
		print PHP_EOL . 'Non-front page output: "' . trim( $output ) . '"' . PHP_EOL;
		$this->assertEmpty( $output );
	}

	/**
	 * Test preload_first_slide_image() outputs nothing when slider is hidden.
	 */
	public function test_preload_not_output_when_slider_hidden() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::preload_first_slide_image() when slider hidden' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->go_to( home_url( '/' ) );

		$this->set_slider_options(
			array(
				'top_slide_display' => 'hide',
				'top_slide_image_1' => 'http://example.com/pc.jpg',
			)
		);
		ob_start();
		LTG_G3_Slider::preload_first_slide_image();
		$output = ob_get_clean();
		print PHP_EOL . 'Hidden slider output: "' . trim( $output ) . '"' . PHP_EOL;
		$this->assertEmpty( $output );
	}

	/**
	 * Test preload uses first non-empty slide when slide 1 is empty.
	 */
	public function test_preload_uses_first_non_empty_slide() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::preload_first_slide_image() with gap' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$this->go_to( home_url( '/' ) );

		// Slide 1 empty, Slide 2 has image -> preload should use slide 2.
		$this->set_slider_options(
			array(
				'top_slide_image_1'        => '',
				'top_slide_image_2'        => 'http://example.com/slide2.jpg',
				'top_slide_image_mobile_2' => 'http://example.com/slide2_mobile.jpg',
			)
		);
		ob_start();
		LTG_G3_Slider::preload_first_slide_image();
		$output = ob_get_clean();
		print PHP_EOL . 'Gap (slide 1 empty): ' . trim( $output ) . PHP_EOL;
		$this->assertStringContainsString( 'href="http://example.com/slide2_mobile.jpg"', $output );
		$this->assertStringContainsString( 'href="http://example.com/slide2.jpg"', $output );
		$this->assertStringNotContainsString( 'slide1', $output );
	}

	/**
	 * Test get_slide_html() adds fetchpriority to the correct slide.
	 */
	public function test_fetchpriority_on_first_rendered_slide() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::get_slide_html() fetchpriority' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		// All slides present -> fetchpriority on slide 1 only.
		$this->set_slider_options(
			array(
				'top_slide_image_1' => 'http://example.com/slide1.jpg',
				'top_slide_image_2' => 'http://example.com/slide2.jpg',
			)
		);
		$html = LTG_G3_Slider::get_slide_html();
		print PHP_EOL . 'Slide 1 has fetchpriority: ' . ( preg_match( '/slide1\.jpg[^>]*fetchpriority/', $html ) ? 'yes' : 'no' ) . PHP_EOL;
		print 'Slide 2 has fetchpriority: ' . ( preg_match( '/slide2\.jpg[^>]*fetchpriority/', $html ) ? 'yes' : 'no' ) . PHP_EOL;
		$this->assertMatchesRegularExpression( '/slide1\.jpg[^>]*fetchpriority="high"/', $html );
		$this->assertDoesNotMatchRegularExpression( '/slide2\.jpg[^>]*fetchpriority/', $html );

		// Slide 1 empty, Slide 2 present -> fetchpriority on slide 2.
		$this->set_slider_options(
			array(
				'top_slide_image_1' => '',
				'top_slide_image_2' => 'http://example.com/slide2.jpg',
			)
		);
		$html = LTG_G3_Slider::get_slide_html();
		print 'Gap - Slide 2 has fetchpriority: ' . ( preg_match( '/slide2\.jpg[^>]*fetchpriority/', $html ) ? 'yes' : 'no' ) . PHP_EOL;
		$this->assertMatchesRegularExpression( '/slide2\.jpg[^>]*fetchpriority="high"/', $html );
	}
}
