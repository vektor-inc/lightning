/* ************************************* */

/* **** Caution **** */

/*
This riginal file is following place.
https://github.com/vektor-inc/vektor-wp-libraries
If you want to change this file that, you have to change original file.
*/

/* ************************************* */

;
(function(window, document, $) {
	/*-------------------------------------*/
	/*
	/*-------------------------------------*/
	// メニューを閉じる
	function vk_mobile_nav_close(target){
		// ※ fix nav の方を押される事もあるので each 処理している
		$(target).each(function(){
			$(this).removeClass('menu-open');
		});
		// メニュー本体から .vk-mobile-nav-open を削除
		$('.vk-mobile-nav').removeClass('vk-mobile-nav-open');
		$('.mobile-fix-nav .vk-mobile-nav-menu-btn i').removeClass('fa-times');
		$('.mobile-fix-nav .vk-mobile-nav-menu-btn i').addClass('fa-bars');
	}

	// 実行関数
	function vk_mobile_nav_run(target){
		// クリックされた時
		$(target).click(function() {
			// メニューボタンに .menu-open クラスがついていたら
			if ($(this).hasClass('menu-open')) {
				vk_mobile_nav_close(target);
			} else {
				$(target).each(function(){
					$(this).addClass('menu-open');
				});
				$('.vk-mobile-nav').addClass('vk-mobile-nav-open');
				$('.mobile-fix-nav .vk-mobile-nav-menu-btn i').removeClass('fa-bars');
				$('.mobile-fix-nav .vk-mobile-nav-menu-btn i').addClass('fa-times');
			}
		});
	}

	// ページ内リンクをクリックされた時に閉じるための処理
	$('.vk-mobile-nav li > a').click(function() {
		var href = $(this).attr('href');
		var result = href.match(/.*(#).*/);

		// クリックされたリンク先がページ内リンクかどうか
		if ( $.isArray(result) && result[1] === '#' ){
			// 閉じる
			vk_mobile_nav_close('.vk-mobile-nav-menu-btn');
		} else {
			// 閉じない
			// ページ内リンク以外で閉じるとモバイルSafariにおいて
			// 閉じる動作の途中で画面遷移時に画面を停止させられるため
			// ページ内リンク以外では閉じないようにする
		}
	});

	// モバイルナビの実行
	$(document).ready(function() {
		/* ※ vk-mobile-menu の読み込みが遅いので document).ready(function() しないと動作しない */
		vk_mobile_nav_run('.vk-mobile-nav-menu-btn');
	});

})(window, document, jQuery);

/*-------------------------------------*/
/*	sub item accordion
/*-------------------------------------*/
;
(function(window, document, $) {

	function vk_menu_acc_run() {
		// var breakPoint = 767;
		var breakPoint = 5000;
		// var bodyWidth = jQuery(window).width();
		/*
		cssのメディアクエリがスクロールバーを含んだ幅になるので、
		js側もスクロールバーを含んだ幅にするため window.innerWidth を使用
		*/
		var bodyWidth = window.innerWidth;

		// ブレイクポイントより小さい場合
		if (bodyWidth <= breakPoint) {
			$.when(
				vk_menu_acc_clear()
			).done(function() {
				vk_menu_acc_init();
				vk_menu_acc_click();
			});
		} else {
			vk_menu_acc_clear();
		}
	} // function vk_menu_acc_run(){

	function vk_menu_acc_clear() {
		$('ul.vk-menu-acc').removeClass('vk-menu-acc-active');
		$('ul.vk-menu-acc li').removeClass('acc-parent-open');
		$('ul.vk-menu-acc li .acc-btn').remove();
		$('ul.vk-menu-acc li .acc-child-close').removeClass('acc-child-close');
		$('ul.vk-menu-acc li .acc-child-open').removeClass('acc-child-open');
	}

	function vk_menu_acc_init() {
		// アクティブクラスを付与
		$('ul.vk-menu-acc').addClass('vk-menu-acc-active');

		// 子階層毎の処理
		$('ul.vk-menu-acc li ul').each(function() {
			// 子階層の直前の要素（ <a> ）の後に「開くボタン」を設置
			$(this).prev().after('<span class="acc-btn acc-btn-open"></span>');
			// 下階層となるul要素には close クラス追加
			$(this).addClass("acc-child-close");
		});
	}

	function vk_menu_acc_click() {
		$('ul.vk-menu-acc li .acc-btn').click(function() {

			// クリックされたボタンが開くボタンだったら
			if ($(this).hasClass('acc-btn-open')) {

				// 親である li に open クラス追加
				$(this).parent().addClass('acc-parent-open');
				$(this).removeClass('acc-btn-open').addClass('acc-btn-close');
				$(this).next().removeClass('acc-child-close').addClass('acc-child-open');

				// 閉じるボタンがクリックされたら
			} else {
				// 親である li から open クラス除去
				$(this).parent().removeClass('acc-parent-open');
				// クリックされた閉じるボタンから close クラスを除去して open クラス追加
				$(this).removeClass('acc-btn-close').addClass('acc-btn-open');
				// 下階層となる ul 要素から open クラスを除去して close クラス追加
				$(this).next().removeClass('acc-child-open').addClass('acc-child-close');
			}
		});
	}

	function vk_menu_acc_resize() {
		var timer = false;
		// リサイズ前のウィンドウサイズ
		var before_window_size = $(window).width();
		// リサイズを作動させない幅
		var window_size_margin = 8;

		$(window).resize(function() {
			if (timer !== false) {
				clearTimeout(timer);
			}
			timer = setTimeout(function() {

				/*
				スマートフォンにおいてスライドしてスクロールバー表示された時、
				消える時でリサイズ判定されてしまうので、
				スクロールバー相当の幅以上の変化があった時のみ実行する
				*/

				// リサイズ後のウィンドウサイズ
				var after_window_size = $(window).width();

				// これより大きくなってたら実行するサイズ
				var max_change_size = before_window_size + window_size_margin;

				// これより小さくなってたら実行するサイズ
				var min_change_size = before_window_size - window_size_margin;

				if (after_window_size < min_change_size || max_change_size < after_window_size) {
					vk_menu_acc_run();
					// console.log( min_change_size + ' < ' + after_window_size + ' < ' + max_change_size);
					// console.log('Resize run');
					before_window_size = after_window_size;
					// console.log('before_window_size : ' + before_window_size);
				} else {
					// console.log( min_change_size + ' < ' + after_window_size + ' < ' + max_change_size);
					// console.log('Resize none');
				}
			}, 500);
		});
	}

	vk_menu_acc_run();
	vk_menu_acc_resize();

	$(document).ready(function() {
		vk_menu_acc_run();
	});
})(window, document, jQuery);
