;
(function($) {
	/*-------------------------------------*/
	/*
	/*-------------------------------------*/
	jQuery(document).ready(function() {
		/* ※ vk-mobile-menu の読み込みが遅いので document).ready(function() しないと動作しない */
		// クリックされた時
		jQuery('.vk-mobile-nav-menu-btn').click(function() {
			// メニューボタンに .menu-open クラスがついていたら
			if (jQuery(this).hasClass('menu-open')) {
				// .menu-open を外す
				jQuery(this).removeClass('menu-open');
				// メニュー本体から .vk-mobile-nav-open を削除
				jQuery('.vk-mobile-nav').removeClass('vk-mobile-nav-open');
			} else {
				jQuery(this).addClass('menu-open');
				jQuery('.vk-mobile-nav').addClass('vk-mobile-nav-open');
			}
		});
	});

})(jQuery);

/*-------------------------------------*/
/*	sub item accordion
/*-------------------------------------*/
;
(function($) {

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
		jQuery('ul.vk-menu-acc').removeClass('vk-menu-acc-active');
		jQuery('ul.vk-menu-acc li').removeClass('acc-parent-open');
		jQuery('ul.vk-menu-acc li .acc-btn').remove();
		jQuery('ul.vk-menu-acc li .acc-child-close').removeClass('acc-child-close');
		jQuery('ul.vk-menu-acc li .acc-child-open').removeClass('acc-child-open');
	}

	function vk_menu_acc_init() {
		// アクティブクラスを付与
		jQuery('ul.vk-menu-acc').addClass('vk-menu-acc-active');

		// 子階層毎の処理
		jQuery('ul.vk-menu-acc li ul').each(function() {
			// 子階層の直前の要素（ <a> ）の後に「開くボタン」を設置
			jQuery(this).prev().after('<span class="acc-btn acc-btn-open"></span>');
			// 下階層となるul要素には close クラス追加
			jQuery(this).addClass("acc-child-close");
		});
	}

	function vk_menu_acc_click() {
		jQuery('ul.vk-menu-acc li .acc-btn').click(function() {

			// クリックされたボタンが開くボタンだったら
			if (jQuery(this).hasClass('acc-btn-open')) {

				// 親である li に open クラス追加
				jQuery(this).parent().addClass('acc-parent-open');
				jQuery(this).removeClass('acc-btn-open').addClass('acc-btn-close');
				jQuery(this).next().removeClass('acc-child-close').addClass('acc-child-open');

				// 閉じるボタンがクリックされたら
			} else {
				// 親である li から open クラス除去
				jQuery(this).parent().removeClass('acc-parent-open');
				// クリックされた閉じるボタンから close クラスを除去して open クラス追加
				jQuery(this).removeClass('acc-btn-close').addClass('acc-btn-open');
				// 下階層となる ul 要素から open クラスを除去して close クラス追加
				jQuery(this).next().removeClass('acc-child-open').addClass('acc-child-close');
			}
		});
	}

	function vk_menu_acc_resize() {
		var timer = false;
		// リサイズ前のウィンドウサイズ
		var before_window_size = jQuery(window).width();
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
				var after_window_size = jQuery(window).width();

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

	jQuery(document).ready(function() {
		vk_menu_acc_run();
	});

})(jQuery);


// jQuery(document).ready(function() {
// 	run_slide_menu_control();
// });
// jQuery(window).resize(function() {
// 	run_menuResize();
// });
// /*-------------------------------------------*/
// /*	メニューの開閉
// /*	<div id="menu" onclick="showHide('menu');" class="itemOpen">MENU</div>
// /*  * header.siteHeader を left で制御しているのは Safariでは
// /*  position:fixed しているとウィンドウにfixしてしまってwrapを横にずらしてもついて来ないため
// /*-------------------------------------------*/
//
// function run_slide_menu_control() {
// 	jQuery('.menuBtn').prependTo('#bodyInner');
// 	jQuery('.menuBtn').each(function() {
// 		jQuery(this).click(function() {
// 			if (jQuery(this).hasClass('menuBtn_left')) {
// 				var menuPosition = 'left';
// 			} else {
// 				var menuPosition = 'right';
// 			}
// 			// ※ この時点でLightning本体のmaster.jsによって既に menuOpenに新しく切り替わっている
// 			if (jQuery(this).hasClass('menuOpen')) {
// 				slide_menu_open(menuPosition);
// 			} else {
// 				slide_menu_close(menuPosition);
// 			}
// 		});
// 	});
// }
//
// function slide_menu_open(menuPosition) {
// 	var navSection_open_position = 'navSection_open_' + menuPosition;
// 	jQuery('#navSection').addClass(navSection_open_position);
//
// 	var wrap_width = jQuery('body').width();
// 	jQuery('#bodyInner').css({
// 		"width": wrap_width
// 	});
// 	jQuery('#wrap').css({
// 		"width": wrap_width
// 	});
//
// 	var menu_width = wrap_width - 60 + 'px';
//
// 	jQuery('#headerTop').appendTo('#navSection');
// 	jQuery('#gMenu_outer').appendTo('#navSection');
//
// 	if (menuPosition == 'right') {
// 		jQuery('#wrap').stop().animate({
// 			// 右にメニューを表示するために左に逃げる
// 			"margin-left": "-" + menu_width,
// 		}, 200);
// 		jQuery('header.siteHeader').stop().animate({
// 			"left": "-" + menu_width,
// 		}, 200);
// 		jQuery('#navSection').css({
// 			"display": "block",
// 			"width": menu_width,
// 			"right": "-" + menu_width
// 		}).stop().animate({
// 			"right": 0,
// 		}, 200);
//
// 	} else if (menuPosition == 'left') {
// 		jQuery('#wrap').stop().animate({
// 			"margin-left": menu_width,
// 		}, 200);
// 		jQuery('header.siteHeader').stop().animate({
// 			"left": menu_width,
// 		}, 200);
// 		jQuery('#navSection').css({
// 			"display": "block",
// 			"width": menu_width,
// 			"left": "-" + menu_width
// 		}).stop().animate({
// 			"left": 0,
// 		}, 200, function() {});
// 	}
// }
//
// function slide_menu_close(menuPosition) {
//
// 	if (!menuPosition) {
// 		if (jQuery('#navSection').hasClass('navSection_open_right')) {
// 			menuPosition = 'right';
// 		} else {
// 			menuPosition = 'left';
// 		}
// 	}
//
// 	var wrap_width = jQuery('body').width();
// 	jQuery('#bodyInner').css({
// 		"width": wrap_width
// 	});
// 	jQuery('#wrap').css({
// 		"width": wrap_width
// 	});
//
// 	var menu_width = wrap_width - 60 + 'px';
//
// 	jQuery('#wrap').stop().animate({
// 		"margin-left": "0"
// 	}, 200);
// 	jQuery('header.siteHeader').stop().animate({
// 		"left": "0"
// 	}, 200);
//
// 	if (menuPosition == 'right') {
// 		jQuery('header.siteHeader').stop().animate({
// 			"left": "0"
// 		}, 200);
// 		jQuery('#navSection').stop().animate({
// 			"right": "-" + menu_width
// 		}, 200, function() {
// 			menuClose_common();
// 		});
// 	} else if (menuPosition == 'left') {
// 		jQuery('#navSection').stop().animate({
// 			"left": "-" + menu_width
// 		}, 200, function() {
// 			menuClose_common();
// 		});
// 	}
// }
//
// function menuClose_common() {
// 	// アニメーションが終わってから実行
// 	jQuery('#navSection').removeClass('navSection_open_right');
// 	jQuery('#navSection').removeClass('navSection_open_left');
// 	jQuery('#navSection').css({
// 		"right": "",
// 		"left": "",
// 		"display": ""
// 	});
// 	jQuery('#headerTop').prependTo('header.siteHeader');
// 	jQuery('#bodyInner').css({
// 		"width": ""
// 	});
// 	jQuery('#wrap').css({
// 		"width": ""
// 	});
// 	// judge animate execution
// 	if (jQuery('#navSection').is(':animated')) {
// 		jQuery('#gMenu_outer').insertAfter('.navbar-header');
// 	}
// }
//
// function run_menuResize() {
// 	var wrap_width = jQuery('body').width();
// 	jQuery('#bodyInner').css({
// 		"width": wrap_width
// 	});
// 	// jQuery('#wrap').css({"width":wrap_width,"margin-left":"","margin-right":""});
// 	// menuClose_common();
// 	var headerHeight = jQuery('header.siteHeader').height;
// 	jQuery('#top__fullcarousel').css({
// 		"margin-top": headerHeight
// 	});
// 	if (wrap_width > 991) {
// 		slide_menu_close();
// 	}
// }