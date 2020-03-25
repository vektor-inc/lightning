;
((window, document, $) => {
    let timer = false;
    window.addEventListener('DOMContentLoaded', () => {
        if(!document.body.classList.contains('headfix')) return;

        window.addEventListener('resize', ()=>{
            if (timer !== false){
                clearTimeout(timer)
            }
            timer = setTimeout(offset_header, 300)
        })
        offset_header()
    })

    /*----------------------------------------------------------*/
    /*  Offset header
    /*----------------------------------------------------------*/
    function offset_header(){
        let siteHeader = document.getElementsByClassName('siteHeader')[0]
        siteHeader.style.position = 'fixed'

        let headerHeight = siteHeader.clientHeight

        siteHeader.nextElementSibling.style.marginTop = headerHeight + 'px'

        if(document.body.classList.contains('admin-bar')){
            // Get adminbar height
            let adminBarHeight = document.getElementById('wpadminbar').clientHeight
            // Math hight of siteHeader + adminbar
            // var allHead_height = adminBarHeight + headerHeight;
            // Add padding
            siteHeader.style.top = adminBarHeight + 'px'
        }
    }

    /*-------------------------------------------*/
    /*  Header height changer
    /*-------------------------------------------*/
    window.addEventListener('DOMContentLoaded', () => {
        if(!document.body.classList.contains('header_height_changer')) return;

        var head_logo_image_defaultHeight = document.querySelector('.navbar-brand img').clientHeight
        var bodyWidth = document.body.clientWidth

        // When missed the get height
        if ( head_logo_image_defaultHeight < 38 ) {
            if ( bodyWidth >= 991 ) {
                head_logo_image_defaultHeight = 60;
            } else {
                head_logo_image_defaultHeight = 40;
            }
        }

        // Scroll function
        window.addEventListener('scroll', () => {
            var bodyWidth = document.body.clientWidth
            if ( bodyWidth >= 991 ) {
                var scroll = window.pageYOffset || document.documentElement.scrollTop
                if (scroll > 10) {
                    head_low( head_logo_image_defaultHeight );
                } else {
                    head_high( head_logo_image_defaultHeight );
                }
            }
        })
    });

    function head_low( head_logo_image_defaultHeight ){
        let changeHeight = head_logo_image_defaultHeight*0.8;
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

})(window, document, jQuery);
