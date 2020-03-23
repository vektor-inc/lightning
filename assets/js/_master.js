;
((window, document, $) => {
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
    $('.menuBtn').click(function() {
      if (!$('.menuBtn').hasClass('menuOpen')) {
        $('body').removeClass('headerMenuClose').addClass('headerMenuOpen'); // 今後廃止
        $('body').removeClass('header-menu-close').addClass('header-menu-open');
        $('.menuBtn').removeClass('menuClose').addClass('menuOpen');
        $('#gMenu_outer').removeClass('itemClose').addClass('itemOpen');
        $('#menuBtn i').removeClass('fa-bars').addClass('fa-times');
      } else {
        $('body').removeClass('headerMenuOpen'); // 今後廃止
        $('body').removeClass('header-menu-open');
        $('.menuBtn').removeClass('menuOpen').addClass('menuClose');
        $('#gMenu_outer').removeClass('itemOpen').addClass('itemClose');
        $('#menuBtn i').removeClass('fa-times').addClass('fa-bars');
      }
    });
    // });
  }

  function menu_close() {
    $('body').removeClass('headerMenuOpen');
    $('.menuBtn').removeClass('menuOpen').addClass('menuClose');
    $('#gMenu_outer').removeClass('itemOpen').addClass('itemClose');
    $('#menuBtn i').removeClass('fa-times').addClass('fa-bars');
  }

  /*----------------------------------------------------------*/
  /*	Top slide control
  /*----------------------------------------------------------*/
  // add active class to first item
  window.addEventListener('DOMContentLoaded', () => {
    addClass('#top__fullcarousel .carousel-indicators li:first-child', "active")
    addClass('#top__fullcarousel .item:first-child', "active")
  })

  /*-------------------------------------------*/
  /*	iframeのレスポンシブ対応
  /*-------------------------------------------*/
  function iframe_responsive() {
    $('iframe').each(function(i) {
      var iframeUrl = $(this).attr("src");
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
        var iframeWidth = $(this).attr("width");
        var iframeHeight = $(this).attr("height");
        var iframeRate = iframeHeight / iframeWidth;
        var nowIframeWidth = $(this).width();
        var newIframeHeight = nowIframeWidth * iframeRate;
        $(this).css({
          "max-width": "100%",
          "height": newIframeHeight
        });
      }
    });
  }

  /*----------------------------------------------------------*/
  /*	add bootstrap class
  /*----------------------------------------------------------*/
  window.addEventListener('DOMContentLoaded', () => {
    addClass('textarea', 'form-control')
    addClass('select', 'form-control')
    addClass('input[type=text]', 'form-control')
    addClass('input[type=number]', 'form-control')
    addClass('input[type=search]', 'form-control')
    addClass('input[type=password]', 'form-control')
    addClass('input[type=email]', 'form-control')
    addClass('input[type=tel]', 'form-control')
    addClass('input[type=submit]', 'btn')
    addClass('input[type=submit]', 'btn-primary')
    $('#respond p').each(function(i) {
      console.log($(this).children('input'));
      $(this).children('input').appendTo($(this));
    });
    addClass('form#searchform', 'form-inline')
    addClass('form#searchform input[type=text]', 'form-group')
  }, false);


  // common functions
  function addClass(selector, cls) {
    Array.prototype.forEach.call(
      document.querySelectorAll(selector),
      (elem) => elem.classList.add(cls)
    );
  }

  function toggle(selector, remover, adder) {
    Array.prototype.forEach.call(
      document.querySelectorAll(selector),
      (elem) => {
        elem.classList.remove(remover)
        elem.classList.add(adder)
      }
    );
  }
})(window, document, jQuery);
