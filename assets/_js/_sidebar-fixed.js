/*-------------------------------------------*/
/* スクロール時のサイドバー位置固定処理
/*-------------------------------------------*/

/*

// 除外処理 ///////////////////////////////////////

* ウィンドウサイズがタブレット以下の時
* コンテンツエリアよりもサイドバーの高さが高い場合
* １カラム（サブセクションありの場合）

// 基本概念 ///////////////////////////////////////

* A:下端優先 sidebar-fix-primary-bottom
 	サイドバーの下端まで表示されたらそこで固定する ( sidebar-fix_window-bottom )
			コンテンツエリア下端がサイドバー下端よりも上の位置にスクロールしたら
				コンテンツエリア下端とサイドバー下端の位置を揃える ( sidebar-fix_content-bottom )
* B:上端優先（未実装）sidebar-fix-primary-top
 	サイドバー上端が画面上部にきたら一旦固定 ( sidebar-fix_window-top )
		コンテンツエリア下端までスクロールされたら
			コンテンツエリア下端とサイドバー下端の位置を揃える ( sidebar-fix_content-bottom )
*/ 


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
        // Array.prototype.forEach.call(
        //     document.getElementsByClassName('sideSection'),
        //     (elem) => {
        //         elem.style.marginTop = ''
        //     }
		// )
		let sideSection = document.getElementsByClassName('sideSection')[0]
		sideSection.classList.remove("sidebar-fix_window-top");
		sideSection.classList.remove("sidebar-fix_window-bottom");
		sideSection.classList.remove("sidebar-fix_content-bottom");
		sideSection.style.position = null;
		sideSection.style.bottom = null;
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

            // コンテンツエリア上端の位置を取得
			let content_position_top = document.getElementsByClassName('mainSection')[0].getBoundingClientRect().top + window.pageYOffset;


            // サイドバーの高さを取得
            let sidebar_height = sideSection.offsetHeight
            // メインコンテンツの高さを取得
			let content_height = mainSection.offsetHeight
			
			// コンテンツエリア下端の位置を取得 = 上端 + 要素の高さ
			let content_position_bottom = content_position_top + content_height

            // メインコンテンツとサイドバーの高さの差
            let height_difference = content_height - sidebar_height;

            // サイドバー下端までの距離（サイドバー上部とコンテンツエリア上部が同じ位置の場合のみ有効） = コンテンツエリアの位置（高さ）+ サイドバーの高さ
            let sidebar_position_bottom_default = content_position_top + sidebar_height;

			// サイドバー下端を表示するまでスクロールしないといけない距離 = サイドバー下端までの距離 - ウィンドウサイズ
			let sidebar_bottom_position_to_scroll = sidebar_position_bottom_default - window_height;

			// サイドバー下端が表示されたかどうか
			let is_sidebar_bottom_display = false;
			if ( sidebar_bottom_position_to_scroll < window.pageYOffset ){
				is_sidebar_bottom_display = true;
			}

			// コンテンツエリア下端を表示するまでスクロールしないといけない距離 = コンテンツエリア下端までの距離 - ウィンドウサイズ
			let content_bottom_position_to_scroll = content_position_bottom - window_height;

			// コンテンツエリア下端が表示されたかどうか
			let is_content_bottom_display = false;
			if ( content_bottom_position_to_scroll < window.pageYOffset ){
				is_content_bottom_display = true;
			}

			if ( is_sidebar_bottom_display ){
				// sideSection.classList.add("sidebar-fix_window-bottom");
				sideSection.style.position = "fixed";
				sideSection.style.bottom = "30px";
				if ( is_content_bottom_display ){
					sideSection.style.position = "absolute";
					sideSection.style.bottom = "0";
					// sideSection.classList.remove("sidebar-fix_window-bottom");
					// sideSection.classList.add("sidebar-fix_content-bottom");

				}
			}



            /*
             * 初期状態で画面からサイドバーがはみだしている場合（ 画面の高さ < サイドバー下端までの距離 ）
            */
            if ( window_height < sidebar_position_bottom_default ) {

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

            } // if ( sideFix.window_height < sidebar_position_bottom_default ) {

            // ↓ これやるとサイドバーが長い時にサイドバーがなかなかスクロールしない
            // if ( $('body').hasClass('bootstrap4') ){
            //     // スクロール時の固定ナビゲーションでサイドバー上部が隠れないように
            //     var global_nav_fix_height = $('.gMenu_outer').outerHeight();
            //     move_position_start = content_position_top - global_nav_fix_height - 20;
            //     move_position_end = height_difference + content_position_top - global_nav_fix_height - 20 ;
            // }

            /* スクロール動作条件
            /*-------------------------------------------*/
            //  サイドバーがメインコンテンツよりも高い場合は処理しない
            if ( sidebar_height > content_height ){ return; }

            // スクロール量を取得
			var scrollHeight = window.pageYOffset;

            if ( scrollHeight < move_position_start ){
                //**　スクロール量がサイドバーの移動開始スクロール値より少ない場合
                // リセット処理
                sideFix_reset();

            } else {

            	//** スクロール量がサイドバーの移動開始スクロール値より大きい場合

				/* 余白を付与
				/*-------------------------------------------*/
                let gmenu = document.getElementById('gMenu_outer')
				let gmenuHeight = gmenu? gmenu.offsetHeight: 0;
				
                // スクロールの高さより サイドバーの移動終了スクロール値 が大きい場合のみ処理する
                if ( scrollHeight < move_position_end ){ // これがないと延々とスクロールする

                    // 移動開始
                    // サイドバー上部に追加する余白 = スクロール量 - サイドバーの移動開始スクロール値
                    var yohaku = scrollHeight - move_position_start + gmenuHeight;
                    // サイドバー上部に余白を追加
                    // sideSection.style.marginTop = yohaku + 'px'

                } else {
                    // スクロール量が終了ポイントを過ぎた時、下端が揃わないので強制的に揃える
                    var yohaku = content_height - sidebar_height;
                    // sideSection.style.marginTop = yohaku + 'px'
                }
			}
			console.log( 'スクロール : ' + scrollHeight);
			console.log( 'content_position_top : ' + content_position_top);
			console.log( 'move position start : ' + move_position_start);
			console.log( '追加する余白 : ' + yohaku);
			console.log( 'is_sidebar_bottom_display : ' + is_sidebar_bottom_display);
			console.log( 'is_content_bottom_display : ' + is_content_bottom_display);
			console.log( 'content_position_bottom : ' + content_position_bottom);
			console.log( 'content_bottom_position_to_scroll : ' + content_bottom_position_to_scroll);
        }
    }
})(window, document);
