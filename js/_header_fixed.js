;(function($){
jQuery(function(){
	offset_header();
});
jQuery(document).ready(function(){
	offset_header();
});
jQuery(window).resize(function(){
	offset_header();
});

/*----------------------------------------------------------*/
/*	Offset header for admin bar
/*----------------------------------------------------------*/
function offset_header(){
	var headerHeight = jQuery('header.siteHeader').height();
	jQuery('body').css("padding-top",headerHeight+"px");
	if ( jQuery('body').hasClass('admin-bar') ){
		// Get adminbar height
		var adminBarHeight = jQuery('#wpadminbar').height();
		// Math hight of siteHeader + adminbar
		// var allHead_height = adminBarHeight + headerHeight;
		// Add padding
		jQuery('.admin-bar .siteHeader').css("top",adminBarHeight+"px");
	}
}

/*-------------------------------------------*/
/*	Header height changer
/*-------------------------------------------*/
jQuery(document).ready(function(){
  if(!$('body').hasClass('headfix')){ return; }
  jQuery('.siteHeader').css({"position":"fixed"});
	var head_logo_image_defaultHeight = jQuery('.navbar-brand img').height();
	var bodyWidth = jQuery(window).width();
	// When missed the get height
	if ( head_logo_image_defaultHeight < 38 ) {
		if ( bodyWidth >= 991 ) {
			head_logo_image_defaultHeight = 60;
		} else {
			head_logo_image_defaultHeight = 40;
		}
	}
	// Scroll function
	jQuery(window).scroll(function () {
		var bodyWidth = jQuery(window).width();
		if ( bodyWidth >= 991 ) {
	    	var scroll = jQuery(this).scrollTop();
	        if (jQuery(this).scrollTop() > 10) {
	            head_low( head_logo_image_defaultHeight );
	        } else {
				head_high( head_logo_image_defaultHeight );
	        }
	    }
	});
});
function head_low( head_logo_image_defaultHeight ){
	changeHeight = head_logo_image_defaultHeight*0.8;
	jQuery('body').addClass('scrolled');
	jQuery('.siteHeader .container').stop().animate({
		"padding-top":"5px",
		"padding-bottom":"0px",
	},100);
	jQuery('.navbar-brand img').stop().animate({
		"max-height":changeHeight+"px",
	},100);
}
function head_high( head_logo_image_defaultHeight ){
	jQuery('body').removeClass('scrolled');
	jQuery('.siteHeader .container').stop().animate({
		"padding-top":"20px",
		"padding-bottom":"18px",
	},100,function(){
		offset_header();
	});
	jQuery('.navbar-brand img').stop().animate({
		"max-height":head_logo_image_defaultHeight+"px",
	},100);
}

})(jQuery);
