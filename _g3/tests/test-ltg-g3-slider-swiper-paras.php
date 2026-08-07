<?php
/**
 * Test for LTG_G3_Slider::swiper_paras_json()
 *
 * @package vektor-inc/lightning
 */

class LTG_G3_Slider_Swiper_Paras_Test extends WP_UnitTestCase {

	/**
	 * a11y の既定値。
	 *
	 * テスト環境のロケールは en_US で翻訳カタログも無いため、__() は msgid をそのまま返す。
	 * よって期待値は class-ltg-g3-slider.php に書かれた原文と一致する。
	 * キーの順序まで含めて検証したいので assertSame で比較する前提の並び順で持つ。
	 *
	 * @var array
	 */
	private static $default_a11y = array(
		'prevSlideMessage'        => 'Previous slide',
		'nextSlideMessage'        => 'Next slide',
		'paginationBulletMessage' => 'Go to slide {{index}}',
		'slideLabelMessage'       => '{{index}} / {{slidesLength}}',
	);

	/**
	 * 引数を渡さないケースを表すための番兵。
	 *
	 * 'paras' に null や '' を入れると「引数として渡した」ケースと区別できないため、
	 * 専用の文字列で「引数なしで呼ぶ」ことを示す。
	 *
	 * @var string
	 */
	private const NO_ARGS = '__no_args__';

	/**
	 * Test swiper_paras_json()
	 */
	public function test_swiper_paras_json() {

		print PHP_EOL;
		print '------------------------------------' . PHP_EOL;
		print 'LTG_G3_Slider::swiper_paras_json()' . PHP_EOL;
		print '------------------------------------' . PHP_EOL;

		$test_cases = array(
			array(
				// 正常系: 既定値だけで a11y の 4 件が揃うこと（キーの順序も含めて検証）。
				'test_condition_name' => '引数なしの場合 => a11y に 4 件の読み上げ用メッセージが既定値のまま入る',
				'paras'               => self::NO_ARGS,
				'gettext'             => array(),
				'expected_a11y'       => self::$default_a11y,
			),
			array(
				// 正常系: 実際の呼び出し側 add_slide_script() が組み立てる形と同じ引数。
				// wp_parse_args() は浅いマージだが a11y キーを渡していないので既定値が残る。
				'test_condition_name' => 'add_slide_script() と同形の引数（autoplay / effect / speed）を渡した場合 => a11y の既定値は消えずに残る',
				'paras'               => array(
					'autoplay' => array( 'delay' => 4000 ),
					'effect'   => 'slide',
					'speed'    => 800,
				),
				'gettext'             => array(),
				'expected_a11y'       => self::$default_a11y,
			),
			array(
				// 境界値: wp_parse_args() が第一階層のみの浅いマージであることの確認。
				// a11y を部分的に渡すと既定値の中身が丸ごと置き換わる（マージされない）。
				// この挙動は PHPDoc に注意書きとして明記してあり、崩れたら気づけるようにしておく。
				'test_condition_name' => 'a11y を 1 件だけ指定した場合 => 浅いマージのため既定値が丸ごと置き換わり指定した 1 件だけになる',
				'paras'               => array(
					'a11y' => array( 'nextSlideMessage' => 'X' ),
				),
				'gettext'             => array(),
				'expected_a11y'       => array( 'nextSlideMessage' => 'X' ),
			),
			array(
				// 異常系: 訳者が翻訳文に < > を入れた状態を想定する。
				// この JSON はインラインスクリプトとして出力されるため、
				// JSON_HEX_TAG により生の < > が JSON に現れてはいけない。
				'test_condition_name' => '翻訳文に </script> が混入した場合 => JSON_HEX_TAG でエスケープされ生の < > が JSON に現れない',
				'paras'               => self::NO_ARGS,
				'gettext'             => array(
					'Next slide' => 'Next</script>slide',
				),
				'expected_a11y'       => array(
					'prevSlideMessage'        => 'Previous slide',
					'nextSlideMessage'        => 'Next</script>slide',
					'paginationBulletMessage' => 'Go to slide {{index}}',
					'slideLabelMessage'       => '{{index}} / {{slidesLength}}',
				),
			),
		);

		foreach ( $test_cases as $case ) {

			// 翻訳文の差し替えが指定されている場合は gettext フィルターで注入する。
			$filter_callback = null;
			if ( ! empty( $case['gettext'] ) ) {
				$replacements    = $case['gettext'];
				$filter_callback = function ( $translation, $text, $domain ) use ( $replacements ) {
					if ( 'lightning' === $domain && isset( $replacements[ $text ] ) ) {
						return $replacements[ $text ];
					}
					return $translation;
				};
				add_filter( 'gettext', $filter_callback, 10, 3 );
			}

			// テスト対象メソッド実行。引数なしのケースは引数を渡さずに呼ぶ。
			if ( self::NO_ARGS === $case['paras'] ) {
				$json = LTG_G3_Slider::swiper_paras_json();
			} else {
				$json = LTG_G3_Slider::swiper_paras_json( $case['paras'] );
			}

			// フィルターは後続ケースに影響しないよう即座に外す。
			if ( $filter_callback ) {
				remove_filter( 'gettext', $filter_callback, 10 );
			}

			print PHP_EOL . $case['test_condition_name'] . PHP_EOL;
			print '  json = ' . $json . PHP_EOL;

			// JSON として妥当で、a11y キーを持つこと。
			$decoded = json_decode( $json, true );
			$this->assertIsArray( $decoded, $case['test_condition_name'] );
			$this->assertArrayHasKey( 'a11y', $decoded, $case['test_condition_name'] );

			// a11y の中身をキーの順序まで含めて検証する。
			$this->assertSame( $case['expected_a11y'], $decoded['a11y'], $case['test_condition_name'] );

			// 全ケース共通: インラインスクリプトへ出力するため生の < > が JSON に現れないこと。
			$this->assertStringNotContainsString( '<', $json, $case['test_condition_name'] );
			$this->assertStringNotContainsString( '>', $json, $case['test_condition_name'] );
		}
	}
}
