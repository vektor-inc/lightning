;(function($){
	jQuery(function(){
		run_menu_control();
		youtube_responsive();
		// addClass_dropdown();
	});
	jQuery(document).ready(function(){
		youtube_responsive();
		// addClass_dropdown();
	});
	jQuery(window).resize(function(){
		youtube_responsive();
		var wrap_width = jQuery('body').width();
		if ( wrap_width > 767 ) {
			menu_close();
		}
		// menu_close();
		// addClass_dropdown();
	});

	/*----------------------------------------------------------*/
	/*	gMenu control
	/*----------------------------------------------------------*/
	function run_menu_control(){
		// jQuery('.menuBtn').each(function(){
			jQuery('.menuBtn').click(function(){
				if ( !jQuery('.menuBtn').hasClass('menuOpen') ) {
					jQuery('.menuBtn').removeClass('menuClose').addClass('menuOpen');
					jQuery('#gMenu_outer').removeClass('itemClose').addClass('itemOpen');
					jQuery('#menuBtn i').removeClass('fa-bars').addClass('fa-times');
				} else {
					jQuery('.menuBtn').removeClass('menuOpen').addClass('menuClose');
					jQuery('#gMenu_outer').removeClass('itemOpen').addClass('itemClose');
					jQuery('#menuBtn i').removeClass('fa-times').addClass('fa-bars');
				}
			});
		// });
	}
	function menu_close(){
		jQuery('.menuBtn').removeClass('menuOpen').addClass('menuClose');
		jQuery('#gMenu_outer').removeClass('itemOpen').addClass('itemClose');
		jQuery('#menuBtn i').removeClass('fa-times').addClass('fa-bars');
	}

	/*----------------------------------------------------------*/
	/*	Top slide control
	/*----------------------------------------------------------*/
	// add active class to first item
	jQuery(document).ready(function(){
		jQuery('#top__fullcarousel .carousel-indicators li:first-child').addClass("active");
		jQuery('#top__fullcarousel .item:first-child').addClass("active");
		jQuery('.carousel').carousel({
		  interval: 4000
		});
	});

	/*-------------------------------------------*/
	/*	YOUTUBEのレスポンシブ対応
	/*-------------------------------------------*/
	function youtube_responsive(){
		jQuery('iframe').each(function(i){
			var iframeUrl = jQuery(this).attr("src");
			if(!iframeUrl){return;}
			// iframeのURLの中に youtube が存在する位置を検索する
			idx = iframeUrl.indexOf("youtube");
			// 見つからなかった場合には -1 が返される
			if(idx != -1) {
				// youtube が含まれていたらそのクラスを返す
				jQuery(this).addClass('iframeYoutube').css({"max-width":"100%"});
				var iframeWidth = jQuery(this).attr("width");
				var iframeHeight = jQuery(this).attr("height");
				var iframeRate = iframeHeight / iframeWidth;
				var nowIframeWidth = jQuery(this).width();
				var newIframeHeight = nowIframeWidth * iframeRate;
				jQuery(this).css({"max-width":"100%","height":newIframeHeight});
			}
		});
	}
	/*----------------------------------------------------------*/
	/*	add bootstrap class
	/*----------------------------------------------------------*/
	jQuery(document).ready(function(){
		jQuery('textarea').addClass("form-control");
		jQuery('select').addClass("form-control");
		jQuery('input[type=text]').addClass("form-control");
		jQuery('input[type=email]').addClass("form-control");
		jQuery('input[type=tel]').addClass("form-control");
		jQuery('input[type=submit]').addClass("btn btn-primary");
		jQuery('#respond p').each(function(i){
			jQuery(this).children('input').appendTo(jQuery(this));
			});
		jQuery('form#searchform').addClass('form-inline');
		jQuery('form#searchform input[type=text]').addClass('form-group');
	});
	// jQuery('#respond p label').prependTo()

	// function addClass_dropdown(){
	// 	jQuery('.navbar-collapse ul.sub-menu').parent().addClass('dropdown');
	// 	jQuery('.navbar-collapse ul.sub-menu').parent().append('<i class="fa fa-home dropdown-toggle" data-doggle="dropdown"></i>');
	// 	jQuery('.navbar-collapse ul.sub-menu').addClass('dropdown-menu');
	// }

})(jQuery);
