;
((window, document) => {


    /*----------------------------------------------------------*/
    /* gMenu control
    /* もう使ってない気がする
    /*----------------------------------------------------------*/
    // function run_menu_control() {
    //     if (!getElementsByClassName('menuBtn')[0].classList.contains('nemuOpen')) {
    //         document.body.classList.remove('headerMenuClose') // 今後廃止
    //         document.body.classList.add('headerMenuOpen') // 今後廃止
    //         document.body.classList.remove('header-menu-close')
    //         document.body.classList.add('header-menu-open')
    //         swap('.menuBtn', 'menuClose', 'menuOpen')
    //         swap('#gMenu_outer', 'itemClose', 'itemOpen')
    //         swap('#menuBtn i', 'fa-bars', 'fa-times')
    //     } else {
    //         document.body.classList.remove('headerMenuOpen') // 今後廃止
    //         document.body.classList.remove('header-menu-open')
    //         swap('.menuBtn', 'menuOpen', 'menuClose')
    //         swap('#gMenu_outer', 'itemkOpen', 'itemClose')
    //         swap('#menuBtn i', 'fa-times', 'fa-bars')
    //     }
    // }
    // window.addEventListener('DOMContentLoaded', () => {
    //     Array.prototype.forEach.call(document.getElementsByClassName('menuBtn'), (elem) => {
    //         elem.addEventListener('click', menu_control, false);
    //     })
    // })

    // function menu_close() {
    //     document.body.classList.remove('headerMenuOpen')
    //     swap('.menuBtn', 'menuOpen', 'menuClose')
    //     swap('#gMenu_outer', 'itemOpen', 'itemClose')
    //     swap('#menuBtn i', 'fa-times', 'fa-bars')
    // }

    // let timer_menu = false
    // window.addEventListener('resize', ()=>{
    //     if (timer_menu) clearTimeout(timer_menu);
    //     timer_menu = setTimeout(()=>{
    //         if(document.body.offsetWidth > 767) menu_close()
    //     }, 200)
    // })

    /*----------------------------------------------------------*/
    /*  Top slide control
    /*----------------------------------------------------------*/
    let addClass = window.ltg.addClass
    let swap = window.ltg.swap
    // add active class to first item
    window.addEventListener('DOMContentLoaded', () => {
        addClass('#top__fullcarousel .carousel-indicators li:first-child', "active")
        addClass('#top__fullcarousel .item:first-child', "active")
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
