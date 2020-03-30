/*-------------------------------------------*/
/* スクロール時のサイドバー位置固定処理
/*-------------------------------------------*/

;((window, document) => {
    /* 読み込み / リサイズ時の処理
    /*-------------------------------------------*/
    window.addEventListener('scroll', ()=>{
        if(!document.body.classList.contains('sidebar-fix')) return;
        // サイドバーがなかったら処理中止
        if(document.getElementsByClassName('sideSection').length < 1) return;
        sideFix_scroll();
    });


    /* リセット処理
    /*-------------------------------------------*/
    function sideFix_reset(){
        // サイドバー上部の余白をリセット
        Array.prototype.forEach.call(
            document.getElementsByClassName('sideSection'),
            (elem) => {
                elem.style.marginTop = ''
            }
        )
        // $('.sideSection').css({ "margin-top" : "" });
    }

    /* スクロール時の処理
    /*-------------------------------------------*/
    function sideFix_scroll(){
        // 画面の幅を取得
        let wrap_width = document.body.offsetWidth
        // 画面の高を取得
        let window_height = document.documentElement.clientHeight;

        if ( wrap_width < 992 ) {
            //** 画面幅が狭い（1カラム）の場合
            // リセット処理
            sideFix_reset()
        } else {
            //** 画面幅が広い（２カラム）の場合

            let mainSection = document.getElementsByClassName('mainSection')[0]
            let sideSection = document.getElementsByClassName('sideSection')[0]

            // サイドバーの位置を取得
            let sidebar_position_top = sideSection.offsetTop
            // コンテンツエリアの位置を取得
            let content_position_top = document.getElementsByClassName('mainSection')[0].offsetTop

            // サイドバーの高さを取得
            let sidebar_height = sideSection.offsetHeight
            // メインコンテンツの高さを取得
            let content_height = mainSection.offsetHeight

            // メインコンテンツとサイドバーの高さの差
            var height_difference = content_height - sidebar_height;

            // サイドバー下端までの距離（サイドバー上部とコンテンツエリア上部が同じ位置の場合のみ有効） = コンテンツエリアの位置（高さ）+ サイドバーの高さ
            var sidebar_bottom_position_default = content_position_top + sidebar_height;
            // コンテンツアリア下端までの距離 = サイトバーの位置（高さ）+ コンテントエリアの高さ
            var content_bottom_position_default = content_position_top + content_height;

            // サイドバー下端までの距離 = サイトバーの位置（高さ）+ サイドバーの高さ
            var sidebar_bottom_position_now = sidebar_position_top + sidebar_height;
            // コンテンツアリア下端までの距離 = サイトバーの位置（高さ）+ コンテントエリアの高さ
            var content_bottom_position_now = content_position_top + content_height;


            /*
             * 初期状態で画面からサイドバーがはみだしている場合（ 画面の高さ < サイドバー下端までの距離 ）
            */
            if ( window_height < sidebar_bottom_position_default ) {

                // 画面からはみ出しているサイドバーの高さ
                var sidebar_over_size = sidebar_bottom_position_default - window_height;

                // スクロール開始位置の状態ではみ出す量 = サイドバーの高さ - ウィンドウの高さ
                var sidebar_over_size_start = sidebar_height - window_height;

                /*
                 * 移動開始位置でもサイドバーが画面内からはみ出している場合
                */
                if ( sidebar_height > window_height ) {

                    // スクロール開始位置の時点でサイドバーが画面からはみ出している量 = 移動開始スクロール値 + 移動開始時にはみ出している量 + 余白
                    var move_position_start = content_position_top + sidebar_over_size_start + 30;

                    // ★ サイドバーの移動終了スクロール値 = コンテンツエリアまでの高さ + はみ出していたコンテンツの高さ + 余白
                    var move_position_end = height_difference + content_position_top + sidebar_over_size_start + 30;

                    /*
                     * 移動開始位置ではサイドバーが画面内に収まっている場合
                    */
                } else {

                    // ★ サイドバーの移動開始スクロール値 = コンテンツエリアまでの高さ（サイドバー上部とコンテンツエリア上部が同じ位置の場合のみ有効）
                    var move_position_start = content_position_top;
                    // ★ サイドバーの移動終了スクロール値 = メインコンテンツとサイドバーの高さの差 + コンテンツエリアまでの高さ（サイドバー上部とコンテンツエリア上部が同じ位置の場合のみ有効）
                    var move_position_end = height_difference + content_position_top;
                }

                /*
                 * 初期状態で画面からはみだしていない場合（ 画面の高さ > サイドバー下端までの距離 ）
                */
            } else {

                // ★ サイドバーの移動開始スクロール値 = コンテンツエリアまでの高さ（サイドバー上部とコンテンツエリア上部が同じ位置の場合のみ有効）
                var move_position_start = content_position_top;
                // ★ サイドバーの移動終了スクロール値 = メインコンテンツとサイドバーの高さの差 + コンテンツエリアまでの高さ（サイドバー上部とコンテンツエリア上部が同じ位置の場合のみ有効）
                var move_position_end = height_difference + content_position_top;

            } // if ( sideFix.window_height < sidebar_bottom_position_default ) {

            // ↓ これやるとサイドバーが長い時にサイドバーがなかなかスクロールしない
            // if ( $('body').hasClass('bootstrap4') ){
            //     // スクロール時の固定ナビゲーションでサイドバー上部が隠れないように
            //     var global_nav_fix_height = $('.gMenu_outer').outerHeight();
            //     move_position_start = content_position_top - global_nav_fix_height - 20;
            //     move_position_end = height_difference + content_position_top - global_nav_fix_height - 20 ;
            // }

            /* スクロール動作条件
            /*-------------------------------------------*/
            //	サイドバーがメインコンテンツよりも高い場合は処理しない
            if ( sidebar_height > content_height ){ return; }

            // スクロール量を取得
            var scrollHeight = window.pageYOffset;
            if ( scrollHeight < move_position_start ){
                //**　スクロール量がサイドバーの移動開始スクロール値より少ない場合
                // リセット処理
                sideFix_reset();

            } else {
                //** スクロール量がサイドバーの移動開始スクロール値より大きい場合

                let gmenuHeight = document.getElementById('gMenu_outer').offsetHeight
                // スクロールの高さより サイドバーの移動終了スクロール値 が大きい場合のみ処理する
                // if ( content_bottom_position_now > sidebar_bottom_position_now ){ // これがないと延々とスクロールする
                if ( scrollHeight < move_position_end ){ // これがないと延々とスクロールする

                    // 移動開始
                    // サイドバー上部に追加する余白 = スクロール量 - サイドバーの移動開始スクロール値
                    var yohaku = scrollHeight - move_position_start + gmenuHeight;
                    // サイドバー上部に余白を追加
                    sideSection.style.marginTop = yohaku + 'px'

                } else {
                    // スクロール量が終了ポイントを過ぎた時、下端が揃わないので強制的に揃える
                    var yohaku = content_height - sidebar_height;
                    sideSection.style.marginTop = yohaku + 'px'
                }
            }
        }
    }
})(window, document);
