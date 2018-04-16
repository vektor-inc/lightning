;(function($,document,window){

	var timer = false;
	$(document).ready(function(){
		offset_header();
	});
	$(window).resize(function(){
		if (timer !== false){
			clearTimeout(timer);
		}
		timer = setTimeout(offset_header, 300);
	});

	/*----------------------------------------------------------*/
	/*	Offset header
	/*----------------------------------------------------------*/
	function offset_header(){

		if(!$('body').hasClass('headfix')){ return; }

		$('.siteHeader').css({"position":"fixed"});

		var headerHeight = $('header.siteHeader').height();
		$('header.siteHeader').next().css("margin-top",headerHeight+"px");

		if ( $('body').hasClass('admin-bar') ){
			// Get adminbar height
			var adminBarHeight = $('#wpadminbar').height();
			// Math hight of siteHeader + adminbar
			// var allHead_height = adminBarHeight + headerHeight;
			// Add padding
			$('.admin-bar .siteHeader').css("top",adminBarHeight+"px");
		}
	}

	/*-------------------------------------------*/
	/*	Header height changer
	/*-------------------------------------------*/
	$(document).ready(function(){

		if( !$('body').hasClass('header_height_changer') ){ return; }

		var head_logo_image_defaultHeight = $('.navbar-brand img').height();
		var bodyWidth = $(window).width();
		// When missed the get height
		if ( head_logo_image_defaultHeight < 38 ) {
			if ( bodyWidth >= 991 ) {
				head_logo_image_defaultHeight = 60;
			} else {
				head_logo_image_defaultHeight = 40;
			}
		}
		// Scroll function
		$(window).scroll(function () {
			var bodyWidth = $(window).width();
			if ( bodyWidth >= 991 ) {
				var scroll = $(this).scrollTop();
				if ($(this).scrollTop() > 10) {
					head_low( head_logo_image_defaultHeight );
				} else {
					head_high( head_logo_image_defaultHeight );
				}
			}
		});
	});
	function head_low( head_logo_image_defaultHeight ){
		changeHeight = head_logo_image_defaultHeight*0.8;
		$('.siteHeader .siteHeadContainer').stop().animate({
			"padding-top":"5px",
			"padding-bottom":"0px",
		},100);
		$('.navbar-brand img').stop().animate({
			"max-height":changeHeight+"px",
		},100);
	}
	function head_high( head_logo_image_defaultHeight ){
		$('.siteHeader .siteHeadContainer').stop().animate({
			"padding-top":"20px",
			"padding-bottom":"18px",
		},100,function(){
			offset_header();
		});
		$('.navbar-brand img').stop().animate({
			"max-height":head_logo_image_defaultHeight+"px",
		},100);
	}

})(jQuery,document,window);
