;
(function($) {
	/*-------------------------------------*/
	/*
	/*-------------------------------------*/
	function vk_mobile_nav_run(target){
		// クリックされた時
		jQuery(target).click(function() {
			// メニューボタンに .menu-open クラスがついていたら
			if (jQuery(this).hasClass('menu-open')) {
				// .menu-open を外す
				// ※ fix nav の方を押される事もあるので each 処理している
				jQuery(target).each(function(){
					jQuery(this).removeClass('menu-open');
				});
				// メニュー本体から .vk-mobile-nav-open を削除
				jQuery('.vk-mobile-nav').removeClass('vk-mobile-nav-open');
				jQuery('.mobile-fix-nav .vk-mobile-nav-menu-btn i').removeClass('fa-times');
				jQuery('.mobile-fix-nav .vk-mobile-nav-menu-btn i').addClass('fa-bars');
			} else {
				jQuery(target).each(function(){
					jQuery(this).addClass('menu-open');
				});
				jQuery('.vk-mobile-nav').addClass('vk-mobile-nav-open');
				jQuery('.mobile-fix-nav .vk-mobile-nav-menu-btn i').removeClass('fa-bars');
				jQuery('.mobile-fix-nav .vk-mobile-nav-menu-btn i').addClass('fa-times');
			}
		});
	}
	jQuery(document).ready(function() {
		/* ※ vk-mobile-menu の読み込みが遅いので document).ready(function() しないと動作しない */
		vk_mobile_nav_run('.vk-mobile-nav-menu-btn');
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
