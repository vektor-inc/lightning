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
	 * ボタンから以外の経路（disableOnInteraction を有効にした場合の操作による停止など）で
	 * 自動再生が止まったときも、これでボタンの表示が追随する。
	 *
	 * autoplayPause / autoplayResume は購読しない。
	 * Swiper はスライドが切り替わるたびに（内部のトランジション待ち合わせのために）
	 * autoplayPause → autoplayResume を発火するため、購読すると
	 * 自動再生中なのに切り替えの間だけ「再生」表示になってちらつく。
	 * これらは内部のゲートであって閲覧者から見た再生状態ではないので、
	 * ボタンが表すべきは autoplay.running。autoplay.paused も判定に使わない。
	 *
	 * ただし pauseOnMouseEnter を有効にした場合は、マウスを載せている間
	 * 「自動再生中の表示のまま実際には止まっている」状態になる。
	 * その値を有効化するときは autoplayPause / autoplayResume の扱いを再設計すること
	 * （既定は false なので現状は実害なし）。
	 */
	swiper.on( 'autoplayStart', function () {
		syncToggle( true );
	} );
	swiper.on( 'autoplayStop', function () {
		syncToggle( false );
	} );

	toggle.addEventListener( 'click', function ( event ) {
		/*
		 * ボタンは swiper.el の内側にあるため、クリックが Swiper 側の
		 * クリック処理に拾われないよう伝播を止める。
		 * ドラッグ判定は pointerdown / touchstart なのでここでは止められない。
		 * そちらはボタンに付けた swiper-no-swiping クラス（noSwipingClass の既定値）で防ぐ。
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
	 * これにより「ボタンが見えている ⇒ JS が生きていて操作が効く」が保証される。
	 * 逆向きは成り立たない（prefers-reduced-motion の環境ではボタンが見えていて停止中）。
	 * ここまでの途中で例外が出た場合はこの行に到達しないので、
	 * 押しても何も起きない死んだボタンが表示されることはない。
	 */
	toggle.hidden = false;
} )();
