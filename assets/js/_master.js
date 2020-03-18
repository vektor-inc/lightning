((window, document, $) => {
  $(function() {
    run_menu_control();
    $('iframe').load(function() {
      iframe_responsive();
    });
    // addClass_dropdown();
  });

  document.addEventListener('DOMContentLoaded', iframe_responsive);
  document.addEventListener('resize', () => {
    iframe_responsive();
    let wrap_width =  document.defaultView.getComputedStyle(document.body, null).width;
    if (wrap_width > 767) {
      menu_close();
    }
  }, false);

  /*----------------------------------------------------------*/
  /*	scroll
  /*----------------------------------------------------------*/
  // Scroll function
  let scrolled = 'scrolled'
  window.addEventListener('scroll', () => {
    if(window.pageYOffset>0){
      document.body.classList.add(scrolled)
    }else{
      document.body.classList.remove(scrolled)
    }
  },false);

  /*----------------------------------------------------------*/
  /*	gMenu control
  /*----------------------------------------------------------*/
  let toggle = (selector, remover, adder) => {
    Array.prototype.forEach.call(
      document.querySelectorAll(selector),
      (elem) => {
        elem.classList.remove(remover)
        elem.classList.add(adder)
      }
    );
  }
  function run_menu_control() {
    let menu_control = () => {
      if (!$('.menuBtn').hasClass('menuOpen')) {
        document.body.classList.remove('headerMenuClose') // 今後廃止
        document.body.classList.add('headerMenuOpen') // 今後廃止
        document.body.classList.remove('header-menu-close')
        document.body.classList.add('header-menu-open')
        toggle('.menuBtn', 'menuClose', 'menuOpen')
        toggle('#gMenu_outer', 'itemClose', 'itemOpen')
        toggle('#menuBtn i', 'fa-bars', 'fa-times')
      } else {
        document.body.classList.remove('headerMenuOpen') // 今後廃止
        document.body.classList.remove('header-menu-open')
        toggle('.menuBtn', 'menuOpen', 'menuClose')
        toggle('#gMenu_outer', 'itemkOpen', 'itemClose')
        toggle('#menuBtn i', 'fa-times', 'fa-bars')
      }
    }
    Array.prototype.forEach.call(document.getElementsByClassName('menuBtn'), (elem) => {
      elem.addEventListener('click', menu_control, false);
    });
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
  let addClass = (selector, cls) => {
    Array.prototype.forEach.call(
      document.querySelectorAll(selector),
      (elem) => elem.classList.add(cls)
    );
  }
  // add active class to first item
  document.addEventListener('DOMContentLoaded', () => {
    addClass('#top__fullcarousel .carousel-indicators li:first-child', "active")
    addClass('#top__fullcarousel .item:first-child', "active")
  })

  /*-------------------------------------------*/
  /*	iframeのレスポンシブ対応
  /*-------------------------------------------*/
  function iframe_responsive() {
    new Array(document.getElementsByTagName('iframe'))
      .each((i) => {
        let iframeUrl = i.getAttribute('class')
        if(!iframeUrl){return}
        // iframeのURLの中に youtube か map が存在する位置を検索する
        // 見つからなかった場合には -1 が返される
        if (
          (iframeUrl.indexOf("youtube") != -1) ||
          (iframeUrl.indexOf("vimeo") != -1) ||
          (iframeUrl.indexOf("maps") != -1)
        ) {
          var iframeWidth = i.getAttribute("width");
          var iframeHeight = i.getAttribute("height");
          var iframeRate = iframeHeight / iframeWidth;
          var nowIframeWidth =  document.defaultView.getComputedStyle(i, null).width
          var newIframeHeight = nowIframeWidth * iframeRate;
          $(this).css({
            "max-width": "100%",
            "height": newIframeHeight
          });
        }
      }
    )
  }

  /*----------------------------------------------------------*/
  /*	add bootstrap class
  /*----------------------------------------------------------*/
  document.addEventListener('DOMContentLoaded', () => {
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
      $(this).children('input').appendTo($(this));
    });
    addClass('form#searchform', 'form-inline')
    addClass('form#searchform input[type=text]', 'form-group')
  }, false)
})(window, document, jQuery)

