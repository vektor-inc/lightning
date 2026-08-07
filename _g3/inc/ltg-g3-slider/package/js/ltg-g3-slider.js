/**
 * トップページスライダーの初期化と、自動再生の停止・再生ボタンの制御
 *
 * 設定値は PHP 側から wp_localize_script() で ltgG3SliderOpt として渡される。
 * 表示文言は PHP 側の .screen-reader-text に持たせているため、ここでは文言を扱わない。
 */
( function () {
	'use strict';

	var opt = window.ltgG3SliderOpt;

	// Swiper 本体か設定値が無い場合は何もしない.
	if ( ! opt || 'undefined' === typeof window.Swiper ) {
		return;
	}

	var sliderEl = document.querySelector( opt.selector );
	if ( ! sliderEl ) {
		return;
	}

	// Swiper を初期化.
	var swiper = new window.Swiper( sliderEl, opt.params );

	// 旧実装がグローバル変数として公開していたので、後方互換のため同じ名前で残す.
	if ( opt.instance ) {
		window[ opt.instance ] = swiper;
	}

	var toggle = sliderEl.querySelector( '.ltg-slide-autoplay-toggle' );

	// ボタンが無い（スライド1枚・フィルターで無効化）場合や autoplay モジュールが無い場合はここで終了.
	if ( ! toggle || ! swiper.autoplay ) {
		return;
	}

	var stopSet  = toggle.querySelector( '.ltg-slide-autoplay-toggle-stop' );
	var startSet = toggle.querySelector( '.ltg-slide-autoplay-toggle-start' );

	if ( ! stopSet || ! startSet ) {
		return;
	}

	/**
	 * 自動再生の状態に合わせてボタン内の2セットを入れ替える
	 *
	 * グリフは「押したら起こること」を示すため、再生中は停止用（縦棒）を表示する。
	 *
	 * @param {boolean} isPlaying 自動再生が動いているかどうか.
	 */
	function syncToggle( isPlaying ) {
		stopSet.hidden  = ! isPlaying;
		startSet.hidden = isPlaying;
	}

	/*
	 * OS の「動きを減らす」設定が有効な環境では、自動再生を止めた状態で表示する。
	 * 判定は初期状態のみで、OS 設定の途中変更には追従しない
	 * （閲覧者がボタンで再生を選んだあとに OS 設定で覆すのは明示操作の上書きになるため）。
	 */
	if ( window.matchMedia && window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		swiper.autoplay.stop();
	}

	// ボタンを表示する前に状態を確定させる（表示後に化けるちらつきを防ぐ）.
	syncToggle( !! swiper.autoplay.running );

	/*
	 * autoplay の開始・停止をボタンに反映する。
	 * これを購読しないと、スワイプなどで自動再生が止まったあともボタンが
	 * 「停止」と嘘の表示を続けることになる。
	 *
	 * autoplayPause / autoplayResume は購読しない。
	 * Swiper はスライドが切り替わるたびに（内部の待ち合わせのために）
	 * autoplayPause → autoplayResume を発火するため、購読すると
	 * 自動再生中なのに切り替えの間だけ「再生」表示になってちらつく。
	 * autoplay.paused も同じ理由で判定に使わない。
	 */
	swiper.on( 'autoplayStart', function () {
		syncToggle( true );
	} );
	swiper.on( 'autoplayStop', function () {
		syncToggle( false );
	} );

	toggle.addEventListener( 'click', function ( event ) {
		/*
		 * リンク付きスライドではスライド全体が <a> で包まれるため、
		 * ボタンのクリックがリンク遷移に化けないよう伝播を止める。
		 */
		event.preventDefault();
		event.stopPropagation();

		if ( swiper.autoplay.running ) {
			swiper.autoplay.stop();
		} else {
			// OS 設定で止まっている状態からでも、閲覧者の明示操作で再生を開始できる.
			swiper.autoplay.start();
		}
	} );

	/*
	 * 最後にボタンを表示する。
	 * これにより「ボタンが見えている ⇔ 自動再生が生きている」が常に成り立ち、
	 * JS が動かなかった環境で死んだボタンが表示されることもない。
	 */
	toggle.hidden = false;
} )();
