jQuery(window).resize(function(){
	menuClose();
});
/*-------------------------------------------*/
/*	メニューの開閉
/*	<div id="menu" onclick="showHide('menu');" class="itemOpen">MENU</div>
/*-------------------------------------------*/
var menu_width = '360px';
function showHide(targetID) {
	if( document.getElementById(targetID)) {
		var targetItem = '#' + targetID;
		if ( jQuery(targetItem).hasClass('itemOpen') ) {
			menuClose(targetID);
		} else {
			menuOpen(targetID);
		}
	}
}
function menuOpen(){

	jQuery('#gMenu_outer').removeClass('itemClose').addClass('itemOpen');
	jQuery('#menuBtn i').removeClass('fa-bars').addClass('fa-times');

	var wrap_width = jQuery('body').width();
	jQuery('#wrap').css({"width":wrap_width});

	jQuery('#gMenu_outer').appendTo('#navSection');

	jQuery('#wrap').stop().animate({
		"margin-left":menu_width,
	},200);
	jQuery('#navSection').css({"display":"block","width":menu_width,"left":"-"+menu_width }).stop().animate({
		"left":0,
	},200,function(){

	});
}
function menuClose(){
	// ウィンドウサイズが変更された時も実行するのでアニメーション終了を待たずにすぐ実行する
	var wrap_width = jQuery('body').width();
	jQuery('#wrap').css({"width":wrap_width});

	jQuery('#wrap').stop().animate({
		"margin-left":"0",
	},200);
	jQuery('#navSection').stop().animate({
		"left":"-"+menu_width,
	},200,function(){

		jQuery('#gMenu_outer').insertAfter('.navbar-header');

		jQuery('#gMenu_outer').removeClass('itemOpen').addClass('itemClose');
		jQuery('#menuBtn i').removeClass('fa-times').addClass('fa-bars');
	});
}