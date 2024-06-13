/*
.vk-mobile-nav-menu-btn : メニューの開閉ボタン
.menu-open : メニューが開いている時に .vk-mobile-nav-menu-btn に追加で付与されるクラス

.vk-mobile-nav : メニュー本体
.vk-mobile-nav-open : メニューが開いている時に .vk-mobile-nav に追加で付与されるクラス

.vk-menu-acc : 子階層をアコーディオンにする ul 要素
.vk-menu-acc-active : .vk-menu-acc がアコーディオン化された時に付与されるクラス

.acc-btn : 子階層の開閉ボタン
.acc-btn-open : 子階層が閉じている時に .acc-btn に追加で付与されるクラス
.acc-btn-close : 子階層が開いている時に .acc-btn に追加で付与されるクラス

.acc-child-open : 子階層の ul が開いている時に付与されるクラス
*/

// モバイルデバイスの判定
function isMobileDevice() {
	return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
}

// HTML要素の読み込みが完了してから実行
window.addEventListener('DOMContentLoaded', () => {

    // 初期設定
    const init = () => {

		// デバイスクラスの付与 //////////////////////////////////////////////////////

        // モバイルデバイスの場合は body に device-mobile クラスを追加
        // モバイルデバイスでない場合は body に device-pc クラスを追加
        const deviceClass = isMobileDevice() ? 'device-mobile' : 'device-pc';
        // あらかじめ付与されているクラスを削除
        document.body.classList.remove('device-mobile', 'device-pc');
        // デバイスクラスを追加
        document.body.classList.add(deviceClass);
    };

    init();
});
