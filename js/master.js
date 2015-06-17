jQuery(function(){
	offset_header();
	// addClass_dropdown();
});
jQuery(document).ready(function(){
	offset_header();
	// addClass_dropdown();
});
jQuery(window).resize(function(){
	offset_header();
	// addClass_dropdown();
});

/*----------------------------------------------------------*/
/*	Top slide control
/*----------------------------------------------------------*/
// add active class to first item
jQuery('#top__fullcarousel .carousel-indicators li:first-child').addClass("active");
jQuery('#top__fullcarousel .item:first-child').addClass("active");
jQuery('.carousel').carousel({
  interval: 5000
})

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
		jQuery('.admin-bar .navbar-fixed-top').css("top",adminBarHeight+"px");
		
	}
}

/*-------------------------------------------*/
/*	Header height changer
/*-------------------------------------------*/
jQuery(document).ready(function(){
	jQuery(window).scroll(function () {
		var bodyWidth = jQuery(window).width();
		if ( bodyWidth >= 768 ) {
	    	var scroll = jQuery(this).scrollTop();
	        if (jQuery(this).scrollTop() > 10) {
	            head_low();
	        } else {
				head_high();
	        }
	    } else {
	    	head_low();
	    }
	});
});
function head_low(){
	jQuery('.siteHeader .container').stop().animate({
		"padding-top":"6px",
		"padding-bottom":"5px",
	},100);
	jQuery('.navbar-brand img').stop().animate({
		"max-height":"45px",
	},100);
}
function head_high(){
	jQuery('.siteHeader .container').stop().animate({
		"padding-top":"22px",
		"padding-bottom":"20px",
	},100);
	jQuery('.navbar-brand img').stop().animate({
		"max-height":"50px",
	},100);
}
/*----------------------------------------------------------*/
/*	add bootstrap class
/*----------------------------------------------------------*/
jQuery(document).ready(function(){
	jQuery('textarea').addClass("form-control");
	jQuery('select').addClass("form-control");
	jQuery('input[type=text]').addClass("form-control");
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