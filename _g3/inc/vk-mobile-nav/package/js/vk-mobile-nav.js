/* ************************************* */

/* **** Caution **** */

/*
This original file is following place.
https://github.com/vektor-inc/vektor-wp-libraries
If you want to change this file that, you have to change original file.
*/

/* ************************************* */

/*

@version 0.3.0

#vk-mobile-nav-menu-btn : メニューの開閉ボタン
.vk-mobile-nav-menu-btn : メニューの開閉ボタン
.menu-open : メニューが開いている時に .vk-mobile-nav-menu-btn に追加で付与されるクラス

#vk-mobile-nav : メニュー本体
.vk-mobile-nav : メニュー本体
.vk-mobile-nav-open : メニューが開いている時に .vk-mobile-nav に追加で付与されるクラス

.vk-menu-acc : 子階層をアコーディオンにする ul 要素
.vk-menu-acc-active : .vk-menu-acc がアコーディオン化された時に付与されるクラス

.acc-btn : 子階層の開閉ボタン
.acc-btn-open : 子階層が閉じている時に .acc-btn に追加で付与されるクラス
.acc-btn-close : 子階層が開いている時に .acc-btn に追加で付与されるクラス

.acc-child-open : 子階層の ul が開いている時に付与されるクラス
*/

(function() {
    var VkMobileNav = {};

	/*-------------------------------------*/
	/*  Functions
	/*-------------------------------------*/

	// モバイルデバイスの判定
	VkMobileNav.isMobileDevice = function() {
		return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
	}

	// デバイスクラスの付与
	VkMobileNav.addDeviceClass = function() {
		// モバイルデバイスの場合は body に device-mobile クラスを追加
		// モバイルデバイスでない場合は body に device-pc クラスを追加
		const deviceClass = VkMobileNav.isMobileDevice() ? 'device-mobile' : 'device-pc';
		// あらかじめ付与されているクラスを削除
		document.body.classList.remove('device-mobile', 'device-pc');
		// デバイスクラスを追加
		document.body.classList.add(deviceClass);
	}

	// メニューを開く
	VkMobileNav.openMenu = function() {

		// メニューボタンに .menu-open クラスを付与
		if (VkMobileNav.menuBtn) {
			VkMobileNav.menuBtn.classList.add('menu-open');
		}

		// メニューに .vk-mobile-nav-open クラスを付与
		if ( VkMobileNav.menu ){
			VkMobileNav.menu.classList.add('vk-mobile-nav-open');
		}

	}

	// メニューを閉じる
	VkMobileNav.closeMenu = function() {

		if (VkMobileNav.menuBtn) {
			// ※ fix nav の方を押される事もある
			VkMobileNav.menuBtn.classList.remove('menu-open');
		}

		// メニューから .vk-mobile-nav-open クラスを削除
		if ( VkMobileNav.menu ){
			VkMobileNav.menu.classList.remove('vk-mobile-nav-open');
		}
	}

	/*-------------------------------------*/
	/*  Run Functions
	/*-------------------------------------*/
	// HTML要素の読み込みが完了してから実行
	window.addEventListener('DOMContentLoaded', () => {

		// 初期設定
		const init = () => {
			// デバイスクラスの付与
			VkMobileNav.addDeviceClass();
			// メニューボタンの取得
			VkMobileNav.menuBtn = document.getElementById('vk-mobile-nav-menu-btn');
			// メニュー本体の取得
			VkMobileNav.menu = document.getElementById('vk-mobile-nav');

			// 要素が存在することを確認
			if (!VkMobileNav.menuBtn || !VkMobileNav.menu) {
				console.error('Required elements not found');
				return;
			}
		};

		init();

		// メニュー開閉ボタンがクリックされた時の処理 //////////////////////////////////////
		/*
		モバイル固定ナビ利用時にアイコンフォントのタグを押されてしまうので
		addEventListener('click', (e) => からの e.target.classList などで取得しても
		fontawesome のクラス名が返ってきて誤動作してしまうため、buttn に一旦格納している
		*/
		let button = document.getElementById('vk-mobile-nav-menu-btn');
		if (button) {
			button.addEventListener('click', () => {
				// メニューボタンと本体のクラスを切り替える
				if( button.classList.contains('menu-open') ){
					// 開いている場合 → 閉じる
					VkMobileNav.closeMenu();
				}else{
					// 閉じている場合 → 開く
					VkMobileNav.openMenu();
				}
			})
		}

		// ナビゲーションリンクがクリックされた時の処理 //////////////////////////////////////
		const navLinks = document.querySelectorAll('.vk-mobile-nav li > a');
		navLinks.forEach((link) => {
			link.addEventListener('click', (e) => {
				let me = e.target
				let href = me.getAttribute('href')

				// クリックされたリンク先がページ内リンクかどうか
				if(href.indexOf('#' == 0)) {
					// ページ内リンクの場合はメニューを閉じる
					VkMobileNav.closeMenu();
				}else{
					// ページ内リンク以外で閉じるとモバイルSafariにおいて
					// 閉じる動作の途中で画面遷移時に画面を停止させられるため
					// ページ内リンク以外では閉じないようにする
				}
			})
		})

	});

	/*-------------------------------------*/
	/*  sub item accordion
	/*-------------------------------------*/
	// 子階層のアコーディオンを有効にする
	VkMobileNav.runAcc = function() {

		// 子階層をアコーディオンにするメニュー（ul.vk-menu-acc）に対して、.vk-menu-acc-active クラスを付与
		const accMenus = document.querySelectorAll('ul.vk-menu-acc');

		// サブメニュー展開用のボタン要素 subMenuButton を span タグで定義して .acc-btn , .acc-btn-open クラスを付与
		const subMenuButton = document.createElement('span');
		subMenuButton.classList.add('acc-btn', 'acc-btn-open');
		
		// ul.vk-menu-acc ul.sub-menu がある場合（子階層をアコーディオンにするメニューの中に子階層がある場合）
		accMenus.forEach((elm) => {
			// ul.vk-menu-acc に .vk-menu-acc-active クラスを付与
			elm.classList.add('vk-menu-acc-active');
			// ul.vk-menu-acc ul.sub-menu をループ処理
			elm.querySelectorAll('ul.sub-menu').forEach((subMenu) => {
				// ul.vk-menu-acc ul.sub-menu の前に subMenuButton を追加
				subMenu.before(subMenuButton.cloneNode(true));
				// 該当の ul.sub-menu に acc-child-close クラスを付与
				subMenu.classList.add('acc-child-close');
				// 追加した subMenuButton（.acc-btn） がクリックされたら VkMobileNav.accAction に .acc と subMenu の要素を渡して実行
				subMenu.previousElementSibling.addEventListener('click', () => {
					VkMobileNav.accAction(subMenu);
				});

			});
		});
	}

	// 子階層のアコーディオン開閉ボタンがクリックされた時の処理
	VkMobileNav.accAction = function(subMenu) {
		// subMenu の前要素の .acc-btn を取得して accBtn に格納
		const accBtn = subMenu.previousElementSibling;

		// subMenu が acc-child-close クラスを持っている場合
		if (subMenu.classList.contains('acc-child-close')) {
			// subMenu に acc-child-open クラスを付与
			subMenu.classList.remove('acc-child-close');
			subMenu.classList.add('acc-child-open');
			accBtn.classList.remove('acc-btn-open');
			accBtn.classList.add('acc-btn-close');
			// subMenu の親要素の li 要素に .acc-parent-open クラスを付与
			subMenu.parentNode.classList.remove('acc-parent-close');
			subMenu.parentNode.classList.add('acc-parent-open');
		} else {
			// subMenu に acc-child-close クラスを付与
			subMenu.classList.remove('acc-child-open');
			subMenu.classList.add('acc-child-close');
			accBtn.classList.remove('acc-btn-close');
			accBtn.classList.add('acc-btn-open');
			// subMenu の親要素の li 要素に .acc-parent-open クラスを付与
			subMenu.parentNode.classList.remove('acc-parent-open');
			subMenu.parentNode.classList.add('acc-parent-close');
		}
	}

	// 子階層のアコーディオンクラスをリセット
	VkMobileNav.resetAccordion = function() {
		const accMenus = document.querySelectorAll('ul.vk-menu-acc');
		accMenus.forEach((elm) => {
			elm.classList.remove('vk-menu-acc-active');
		});

		const accLis = document.querySelectorAll('ul.vk-menu-acc li');
		accLis.forEach((elm) => {
			elm.classList.remove('acc-parent-open');
		});

		const accChildClose = document.querySelectorAll('ul.vk-menu-acc li .acc-child-close');
		accChildClose.forEach((elm) => {
			elm.classList.remove('acc-child-close');
		});

		const accChildOpen = document.querySelectorAll('ul.vk-menu-acc li .acc-child-open');
		accChildOpen.forEach((elm) => {
			elm.classList.remove('acc-child-open');
		});

	}

	window.addEventListener('DOMContentLoaded', () => {
		// アコーディオンを有効にする
		VkMobileNav.runAcc();
	});

})();
