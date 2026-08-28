<?php
/**
 * ブレイクポイント
 *
 * 画面幅でレイアウトを切り替える境目の値を、テーマ内の一箇所で管理する。
 * 各値はその画面区分に含まれる「最大値」。上限側は width <= 、下限側は 値 < で比較する。
 * 例) スマホ: ( width <= $xs-max ) / タブレット: ( $xs-max < width <= $md-max ) / PC: ( $md-max < width )
 *
 * SCSS 側は assets/_scss/_variables_breakpoint.scss に同じ値を持っている。
 * 変更する場合は両方を揃えること。
 *
 * @package Lightning
 */

if ( ! function_exists( 'lightning_get_breakpoints' ) ) {
	/**
	 * ブレイクポイントの一覧を返す
	 *
	 * @return array キーと CSS の値（単位付き）の連想配列.
	 */
	function lightning_get_breakpoints() {
		$breakpoints = array(
			'xs-max' => '576px',
			'sm-max' => '768px',
			'md-max' => '992px',
			'lg-max' => '1200px',
			'xl-max' => '1400px',
		);
		return apply_filters( 'lightning_breakpoints', $breakpoints );
	}
}

if ( ! function_exists( 'lightning_get_breakpoint' ) ) {
	/**
	 * ブレイクポイントの値を返す
	 *
	 * @param string $key xs-max / sm-max / md-max / lg-max / xl-max.
	 * @return string 単位付きの値（例: '768px'）。未定義のキーの場合は空文字.
	 */
	function lightning_get_breakpoint( $key ) {
		$breakpoints = lightning_get_breakpoints();

		if ( ! isset( $breakpoints[ $key ] ) ) {
			// 空文字を返すと @media (  < width ) のような不正なメディアクエリになるため、
			// 呼び出し側の間違いに気づけるよう通知する.
			_doing_it_wrong(
				__FUNCTION__,
				esc_html( sprintf( 'Undefined breakpoint key: %s', $key ) ),
				'15.38.4'
			);
			return '';
		}

		return $breakpoints[ $key ];
	}
}

if ( ! function_exists( 'lightning_the_breakpoint' ) ) {
	/**
	 * ブレイクポイントの値を出力する
	 *
	 * @param string $key xs-max / sm-max / md-max / lg-max / xl-max.
	 * @return void
	 */
	function lightning_the_breakpoint( $key ) {
		echo esc_html( lightning_get_breakpoint( $key ) );
	}
}
