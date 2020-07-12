/* ************************************* */

/* **** Caution **** */

/*
This riginal file is following place.
https://github.com/vektor-inc/vektor-wp-libraries
If you want to change this file that, you have to change original file.
*/

/* ************************************* */

((window, document) => {
	function action(query, fn) {
		Array.prototype.forEach.call(
			document.querySelectorAll(query),
			fn
		)
	}

	function removeClass(query, className) {
		action(query, (elem)=>elem.classList.remove(className))
	}

	function addClass(query, className) {
		action(query, (elem)=>elem.classList.add(className))
	}


	(function(window, document, target) {
		/*-------------------------------------*/
		/*
		/*-------------------------------------*/
		// メニューを閉じる
		function vk_mobile_nav_close(target){
			// ※ fix nav の方を押される事もある
			document.getElementById('vk-mobile-nav-menu-btn').classList.remove('menu-open')
			// メニュー本体から .vk-mobile-nav-open を削除
			document.getElementById('vk-mobile-nav').classList.remove('vk-mobile-nav-open')
		}

		// 実行関数
		function vk_mobile_nav_run(target){
			/*
			モバイル固定ナビ利用時にアイコンフォントのタグを押されてしまうので
			addEventListener('click', (e) => からの e.target.classList などで取得しても
			fontawesome のクラス名が返ってきて誤動作してしまうため、buttn に一旦格納
			*/
			let button = document.getElementById('vk-mobile-nav-menu-btn');
			button.addEventListener('click', () => {
				if( button.classList.contains('menu-open') ){
					vk_mobile_nav_close(target);
				}else{
					addClass(target, 'menu-open')
					document.getElementById('vk-mobile-nav').classList.add('vk-mobile-nav-open')
				}
			})

			action('.vk-mobile-nav li > a', (elm) => {
				elm.addEventListener('click', (e) => {
					let me = e.target
					let href = me.getAttribute('href')

					// クリックされたリンク先がページ内リンクかどうか
					if(href.indexOf('#' == 0)) {
						vk_mobile_nav_close('.vk-mobile-nav-menu-btn');
					}else{
						// 閉じない
						// ページ内リンク以外で閉じるとモバイルSafariにおいて
						// 閉じる動作の途中で画面遷移時に画面を停止させられるため
						// ページ内リンク以外では閉じないようにする
					}
				})
			})
		}

		// モバイルナビの実行
		window.addEventListener('DOMContentLoaded', () => {
			vk_mobile_nav_run(target);
		});

	})(window, document, '.vk-mobile-nav-menu-btn');

	/*-------------------------------------*/
	/*  sub item accordion
	/*-------------------------------------*/
	;
	(function(breakPoint) {
		function vk_menu_acc_run() {
			// var breakPoint = 767;
			// var bodyWidth = jQuery(window).width();
			/*
			cssのメディアクエリがスクロールバーを含んだ幅になるので、
			js側もスクロールバーを含んだ幅にするため window.innerWidth を使用
			*/
			var bodyWidth = window.innerWidth;

			// ブレイクポイントより小さい場合
			if (bodyWidth <= breakPoint) {
				vk_menu_acc_clear()
				vk_menu_acc_init();
			} else {
				vk_menu_acc_clear();
			}
		}

		function vk_menu_acc_clear() {
			removeClass('ul.vk-menu-acc', 'vk-menu-acc-active')
			removeClass('ul.vk-menu-acc li', 'acc-parent-open')
			action('ul.vk-menu-acc li .acc-btn', (elm) => elm.remove())
			removeClass('ul.vk-menu-acc li .acc-child-close', 'acc-child-close')
			removeClass('ul.vk-menu-acc li .acc-child-open', 'acc-child-open')
		}

		function vk_menu_acc_init() {
			// アクティブクラスを付与
			addClass('ul.vk-menu-acc', 'vk-menu-acc-active')

			action('ul.vk-menu-acc ul.sub-menu', (elm) => {
				let button = document.createElement('span')
				button.classList.add('acc-btn','acc-btn-open')
				button.addEventListener('click', acc_click_action)

				elm.parentNode.insertBefore(button, elm)
				elm.classList.add('acc-child-close')
			})
		}

		function acc_click_action(event) {
			let self = event.target,
			parent = self.parentNode,
			next = self.nextSibling

			if (self.classList.contains('acc-btn-open')) {
				// 親である li に open クラス追加
				parent.classList.add('acc-parent-open')
				self.classList.remove('acc-btn-open')
				self.classList.add('acc-btn-close')

				next.classList.remove('acc-child-close')
				next.classList.add('acc-child-open')
			}else{
				// 閉じるボタンがクリックされたら

				// 親である li から open クラス除去
				parent.classList.remove('acc-parent-open')
				// クリックされた閉じるボタンから close クラスを除去して open クラス追加
				self.classList.remove('acc-btn-close')
				self.classList.add('acc-btn-open')
				// $(self).removeClass('acc-btn-close').addClass('acc-btn-open');
				// 下階層となる ul 要素から open クラスを除去して close クラス追加
				next.classList.remove('acc-child-open')
				next.classList.add('acc-child-close')
			}
		}

		function vk_menu_acc_resize() {
			let timer = false,
			// リサイズ前のウィンドウサイズ
			before_window_size = document.body.offsetWidth,
			// リサイズを作動させない幅
			window_size_margin = 8,

			action = () => {
				/*
				スマートフォンにおいてスライドしてスクロールバー表示された時、
				消える時でリサイズ判定されてしまうので、
				スクロールバー相当の幅以上の変化があった時のみ実行する
				*/

				// リサイズ後のウィンドウサイズ
				let after_window_size = document.body.offsetWidth

				// これより大きくなってたら実行するサイズ
				let max_change_size = before_window_size + window_size_margin

				// これより小さくなってたら実行するサイズ
				let min_change_size = before_window_size - window_size_margin

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
			}

			window.addEventListener('resize', () => {
				if (timer !== false) {
					clearTimeout(timer);
				}
				timer = setTimeout(action, 500)
			})
		}

		vk_menu_acc_resize()

		document.addEventListener('DOMContentLoaded', vk_menu_acc_run)
	})(5000);
})(window, document);

const mobile = require('is-mobile');
((document)=>{
	window.addEventListener('DOMContentLoaded', ()=>{
		const isMobile = mobile.isMobile({tablet:true})
		;['device-mobile', 'device-pc'].forEach((m)=>document.body.classList.remove(m))
		document.body.classList.add(isMobile? 'device-mobile': 'device-pc')
	})
})(document)
