;
(function($) {
  $(function() {
    run_menu_control();
    $('iframe').load(function() {
      iframe_responsive();
    });
    // addClass_dropdown();
  });
  $(document).ready(function() {
    iframe_responsive();
    // addClass_dropdown();
  });
  $(window).resize(function() {
    iframe_responsive();
    var wrap_width = $('body').width();
    if (wrap_width > 767) {
      menu_close();
    }
    // menu_close();
    // addClass_dropdown();
  });

  /*----------------------------------------------------------*/
  /*	scroll
  /*----------------------------------------------------------*/
  // Scroll function
  $(window).scroll(function() {
    var scroll = $(this).scrollTop();
    if ($(this).scrollTop() > 1) {
      $('body').addClass('scrolled');
    } else {
      $('body').removeClass('scrolled');
    }
  });

  /*----------------------------------------------------------*/
  /*	gMenu control
  /*----------------------------------------------------------*/
  function run_menu_control() {
    // jQuery('.menuBtn').each(function(){
    jQuery('.menuBtn').click(function() {
      if (!jQuery('.menuBtn').hasClass('menuOpen')) {
        jQuery('body').removeClass('headerMenuClose').addClass('headerMenuOpen'); // 今後廃止
        jQuery('body').removeClass('header-menu-close').addClass('header-menu-open');
        jQuery('.menuBtn').removeClass('menuClose').addClass('menuOpen');
        jQuery('#gMenu_outer').removeClass('itemClose').addClass('itemOpen');
        jQuery('#menuBtn i').removeClass('fa-bars').addClass('fa-times');
      } else {
        jQuery('body').removeClass('headerMenuOpen'); // 今後廃止
        jQuery('body').removeClass('header-menu-open');
        jQuery('.menuBtn').removeClass('menuOpen').addClass('menuClose');
        jQuery('#gMenu_outer').removeClass('itemOpen').addClass('itemClose');
        jQuery('#menuBtn i').removeClass('fa-times').addClass('fa-bars');
      }
    });
    // });
  }

  function menu_close() {
    jQuery('body').removeClass('headerMenuOpen');
    jQuery('.menuBtn').removeClass('menuOpen').addClass('menuClose');
    jQuery('#gMenu_outer').removeClass('itemOpen').addClass('itemClose');
    jQuery('#menuBtn i').removeClass('fa-times').addClass('fa-bars');
  }

  /*----------------------------------------------------------*/
  /*	Top slide control
  /*----------------------------------------------------------*/
  // add active class to first item
  jQuery(document).ready(function() {
    jQuery('#top__fullcarousel .carousel-indicators li:first-child').addClass("active");
    jQuery('#top__fullcarousel .item:first-child').addClass("active");
  });

  /*-------------------------------------------*/
  /*	iframeのレスポンシブ対応
  /*-------------------------------------------*/
  function iframe_responsive() {
    jQuery('iframe').each(function(i) {
      var iframeUrl = jQuery(this).attr("src");
      if (!iframeUrl) {
        return;
      }
      // iframeのURLの中に youtube か map が存在する位置を検索する
      // 見つからなかった場合には -1 が返される
      if (
        (iframeUrl.indexOf("youtube") != -1) ||
        (iframeUrl.indexOf("vimeo") != -1) ||
        (iframeUrl.indexOf("maps") != -1)
      ) {
        var iframeWidth = jQuery(this).attr("width");
        var iframeHeight = jQuery(this).attr("height");
        var iframeRate = iframeHeight / iframeWidth;
        var nowIframeWidth = jQuery(this).width();
        var newIframeHeight = nowIframeWidth * iframeRate;
        jQuery(this).css({
          "max-width": "100%",
          "height": newIframeHeight
        });
      }
    });
  }

  /*----------------------------------------------------------*/
  /*	add bootstrap class
  /*----------------------------------------------------------*/
  // ホバーしたら
  // 	focusクラスを付ける
  // 	focusクラスが付いている時にマウスアウトしたら
  // 		focusクラスを取る
  // タップしたら
  // 	focusクラスを付ける

  // focusクラスがついていなかったら
  // 	リンクを無効にする

  // focusクラスがついていたら
  // 	リンク出来るようにする

  /*----------------------------------------------------------*/
  /*	add bootstrap class
  /*----------------------------------------------------------*/
  jQuery(document).ready(function() {
    jQuery('textarea').addClass("form-control");
    jQuery('select').addClass("form-control");
    jQuery('input[type=text]').addClass("form-control");
    jQuery('input[type=email]').addClass("form-control");
    jQuery('input[type=tel]').addClass("form-control");
    jQuery('input[type=submit]').addClass("btn btn-primary");
    jQuery('#respond p').each(function(i) {
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
