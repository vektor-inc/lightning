/*
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

var VkMobileNav = VkMobileNav || {};

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
	const menuBtn = document.getElementById('vk-mobile-nav-menu-btn');
	if (menuBtn) {
		menuBtn.classList.add('menu-open');
	}

	// メニューに .vk-mobile-nav-open クラスを付与
	const menu = document.getElementById('vk-mobile-nav');
	if ( menu ){
		menu.classList.add('vk-mobile-nav-open');
	}

}

// メニューを閉じる
VkMobileNav.closeMenu = function() {

	// メニューボタンから .menu-open クラスを削除
	const menuBtn = document.getElementById('vk-mobile-nav-menu-btn');
	if (menuBtn) {
		// ※ fix nav の方を押される事もある
		menuBtn.classList.remove('menu-open');
	}

	// メニューから .vk-mobile-nav-open クラスを削除
	const menu = document.getElementById('vk-mobile-nav');
	if ( menu ){
		menu.classList.remove('vk-mobile-nav-open');
	}
}


// HTML要素の読み込みが完了してから実行
window.addEventListener('DOMContentLoaded', () => {

    // 初期設定
    const init = () => {
		// デバイスクラスの付与
		VkMobileNav.addDeviceClass();
    };

    init();

	/*
	モバイル固定ナビ利用時にアイコンフォントのタグを押されてしまうので
	addEventListener('click', (e) => からの e.target.classList などで取得しても
	fontawesome のクラス名が返ってきて誤動作してしまうため、buttn に一旦格納
	*/
	let button = document.getElementById('vk-mobile-nav-menu-btn');
	if (button) {
		// ボタンがクリックされた時の処理
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
});
