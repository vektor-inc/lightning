;
((window, document) => {
    let addClass = window.ltg.addClass
    let swap = window.ltg.swap
    /*----------------------------------------------------------*/
    /*  scroll
    /*----------------------------------------------------------*/
    // Scroll function
    window.addEventListener('scroll', () => {
        if(window.pageYOffset > 0){
            document.body.classList.add('scrolled')
        }else{
            document.body.classList.remove('scrolled')
        }
    })

    /*----------------------------------------------------------*/
    /* gMenu control
    /* もう使ってない気がする
    /*----------------------------------------------------------*/
    function run_menu_control() {
        if (!getElementsByClassName('menuBtn')[0].classList.contains('nemuOpen')) {
            document.body.classList.remove('headerMenuClose') // 今後廃止
            document.body.classList.add('headerMenuOpen') // 今後廃止
            document.body.classList.remove('header-menu-close')
            document.body.classList.add('header-menu-open')
            swap('.menuBtn', 'menuClose', 'menuOpen')
            swap('#gMenu_outer', 'itemClose', 'itemOpen')
            swap('#menuBtn i', 'fa-bars', 'fa-times')
        } else {
            document.body.classList.remove('headerMenuOpen') // 今後廃止
            document.body.classList.remove('header-menu-open')
            swap('.menuBtn', 'menuOpen', 'menuClose')
            swap('#gMenu_outer', 'itemkOpen', 'itemClose')
            swap('#menuBtn i', 'fa-times', 'fa-bars')
        }
    }
    window.addEventListener('DOMContentLoaded', () => {
        Array.prototype.forEach.call(document.getElementsByClassName('menuBtn'), (elem) => {
            elem.addEventListener('click', menu_control, false);
        })
    })

    function menu_close() {
        document.body.classList.remove('headerMenuOpen')
        swap('.menuBtn', 'menuOpen', 'menuClose')
        swap('#gMenu_outer', 'itemOpen', 'itemClose')
        swap('#menuBtn i', 'fa-times', 'fa-bars')
    }

    let timer_menu = false
    window.addEventListener('resize', ()=>{
        if (timer_menu) clearTimeout(timer_menu);
        timer_menu = setTimeout(()=>{
            if(document.body.offsetWidth > 767) menu_close()
        }, 200)
    })

    /*----------------------------------------------------------*/
    /*  Top slide control
    /*----------------------------------------------------------*/
    // add active class to first item
    window.addEventListener('DOMContentLoaded', () => {
        addClass('#top__fullcarousel .carousel-indicators li:first-child', "active")
        addClass('#top__fullcarousel .item:first-child', "active")
    })

    /*-------------------------------------------*/
    /*  iframeのレスポンシブ対応
    /*-------------------------------------------*/
    function iframe_responsive() {
        Array.prototype.forEach.call(
            document.getElementsByTagName('iframe'),
            (i) => {
                let iframeUrl = i.getAttribute('src')
                if(!iframeUrl){return}
                // iframeのURLの中に youtube か map が存在する位置を検索する
                // 見つからなかった場合には -1 が返される
                if (
                    (iframeUrl.indexOf("youtube") >= 0) ||
                    (iframeUrl.indexOf("vimeo") >= 0) ||
                    (iframeUrl.indexOf("maps") >= 0)
                ) {
                    var iframeWidth = i.getAttribute("width");
                    var iframeHeight = i.getAttribute("height");
                    var iframeRate = iframeHeight / iframeWidth;
                    var nowIframeWidth = i.offsetWidth
                    var newIframeHeight = nowIframeWidth * iframeRate;
                    i.style.maxWidth = '100%'
                    i.style.height = newIframeHeight + 'px'
                }
            }
        );
    }

    window.addEventListener('DOMContentLoaded',iframe_responsive)
    let timer = false;
    window.addEventListener('resize', ()=>{
        if (timer) clearTimeout(timer);
        timer = setTimeout(iframe_responsive, 200);
    })

    /*----------------------------------------------------------*/
    /*  add bootstrap class
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
        // $('#respond p').each(function(i) {
        //   console.log($(this).children('input'));
        //   $(this).children('input').appendTo($(this));
        // });
        addClass('form#searchform', 'form-inline')
        addClass('form#searchform input[type=text]', 'form-group')
    }, false);
})(window, document);
