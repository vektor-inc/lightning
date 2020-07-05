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
		let sideSection = document.getElementsByClassName('sideSection')[0]
		sideSection.style.position = null;
		sideSection.style.bottom = null;
		sideSection.style.left = null;
		sideSection.style.right = null;
		sideSection.style.width = null;
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
			let parentSection = sideSection.parentNode;
			
			// サイドバーの高さ
			let sidebar_height = sideSection.offsetHeight
			// サイドバーの幅
			let sidebar_width = sideSection.offsetWidth

            // コンテンツエリア上端の位置を取得
			let content_position_top = document.getElementsByClassName('mainSection')[0].getBoundingClientRect().top + window.pageYOffset;
            // コンテンツエリアの高さを取得
			let content_height = mainSection.offsetHeight
			// コンテンツエリア下端の位置を取得 = 上端 + 要素の高さ
			let content_position_bottom = content_position_top + content_height
			// コンテンツエリア下端を表示するまでスクロールしないといけない距離 = コンテンツエリア下端までの距離 - ウィンドウサイズ
			let content_position_bottom_to_scroll = content_position_bottom - window_height;

            // サイドバー下端までの距離 = コンテンツエリア開始位置 + サイドバーの高さ
            let sidebar_position_bottom_default = content_position_top + sidebar_height;

			// サイドバー下端を表示するまでスクロールしないといけない距離 = サイドバー下端までの距離 - ウィンドウサイズ
			let sidebar_position_bottom_to_scroll = sidebar_position_bottom_default - window_height;

			// サイドバー左端の位置
			let sidebar_position_left_default = sideSection.getBoundingClientRect().left  + window.pageXOffset;

            //  サイドバーがメインコンテンツよりも高い場合は処理しない
            if ( sidebar_height > content_height ){ return; }

			// サイドバー下端が表示されたかどうか
			let is_sidebar_bottom_display = false;
			if ( sidebar_position_bottom_to_scroll < window.pageYOffset ){
				is_sidebar_bottom_display = true;
			}

			// コンテンツエリア下端が表示されたかどうか
			let is_content_bottom_display = false;
			if ( content_position_bottom_to_scroll < window.pageYOffset ){
				is_content_bottom_display = true;
			}

			/* DOM操作
			/*-------------------------------------------*/

			// サイドバー下端が表示されたら
			if ( is_sidebar_bottom_display ){
				sideSection.style.position = "fixed";
				sideSection.style.bottom = "30px";
				sideSection.style.left = sidebar_position_left_default + "px";
				sideSection.style.width = sidebar_width + "px";
				
				// コンテンツエリア下端が表示されたら
				if ( is_content_bottom_display ){
					sideSection.style.left = null;
					parentSection.style.position = "relative";
					sideSection.style.position = "absolute";
					sideSection.style.bottom = 0;
					sideSection.style.right = 0;
				}
			} else {
				sideFix_reset();
			}

			console.log( 'スクロール : ' + scrollHeight);
			console.log( 'content_position_top : ' + content_position_top);
			console.log( 'move position start : ' + move_position_start);
			console.log( '追加する余白 : ' + yohaku);
			console.log( 'is_sidebar_bottom_display : ' + is_sidebar_bottom_display);
			console.log( 'is_content_bottom_display : ' + is_content_bottom_display);
			console.log( 'content_position_bottom : ' + content_position_bottom);
			console.log( 'content_position_bottom_to_scroll : ' + content_position_bottom_to_scroll);
			console.log( 'sidebar_position_left_default : ' + sidebar_position_left_default);
        }
    }
})(window, document);
