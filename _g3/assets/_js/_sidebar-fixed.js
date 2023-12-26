/*-------------------------------------------*/
/* スクロール時のサイドバー位置固定処理
/*-------------------------------------------*/

/*

// 除外処理 ///////////////////////////////////////

* ウィンドウサイズがタブレット以下の時
* コンテンツエリアよりもサイドバーの高さが高い場合
* １カラム（サブセクションありの場合）

// 基本概念 ///////////////////////////////////////

* A:上端優先
	 サイドバー上端が画面上部にきたら一旦固定
	 
	 	サイドバーが表示エリアよりも高い時 {
			コンテンツエリアとサイドバーの下端が揃う位置までスクロールされたら {
				コンテンツエリア下端とサイドバー下端の位置を揃える
			}
		} else {
			上部固定状態でのサイドバーの下端 より スクロールしてきたコンテンツエリアの下端が上になったら
				コンテンツエリア下端とサイドバー下端の位置を揃える
		}


* B:下端優先
	サイドバーの表示エリアよりもサイドバーの高さが低い場合 {
		上端優先で固定する（固定したサイドバーの上側に余白ができた状態で固定されてしまうため）
	} else {
		サイドバーの下端まで表示されたらそこで固定する
				コンテンツエリア下端がサイドバー下端よりも上の位置にスクロールしたら
					コンテンツエリア下端とサイドバー下端の位置を揃える
	}
*/

;((window, document) => {

    /* 読み込み / リサイズ時の処理
    /*-------------------------------------------*/
    window.addEventListener('scroll', ()=>{

        if(!document.body.classList.contains('sidebar-fix')) return;
        // サイドバーがなかったら処理中止
		if(document.getElementsByClassName('sub-section').length < 1) return;
		
		sideFix_scroll();
	});

    window.addEventListener('resize', ()=>{

        if(!document.body.classList.contains('sidebar-fix')) return;
        // サイドバーがなかったら処理中止
		if(document.getElementsByClassName('sub-section').length < 1) return;
		
		sideFix_scroll();
    });


    /* リセット処理
    /*-------------------------------------------*/
    function sideFix_reset(){
		let sideSection = document.getElementsByClassName('sub-section')[0]
		sideSection.style.position = null;
		sideSection.style.top = null;
		sideSection.style.bottom = null;
		sideSection.style.left = null;
		sideSection.style.right = null;
    }

	function sidebar_top_margin(){
		let margin = 0;

		// 通常のスクロールナビの場合 ///////////
		let gmenu = document.getElementById('global-nav')
		let gmenuHeight = gmenu ? gmenu.getBoundingClientRect().bottom: 0;

		// ヘッダーが固定じゃないと gmenuHeight が - になるので 0 に補正
		if ( gmenuHeight < 0 ){
			gmenuHeight = 0;
		}

		// 固定部分の下に追加する余白
		margin = gmenuHeight + 40;

		// JPNの固定ナビの場合 ////////////////
		return margin;
	}


    /* スクロール時の処理
    /*-------------------------------------------*/
    function sideFix_scroll(){

		let fix_priority = "top";
		// Bodyタグのクラスから上端優先か下端優先かを取得
		if( document.body.classList.contains('sidebar-fix-priority-top') == true ){
			fix_priority = "top";
		} else if ( document.body.classList.contains('sidebar-fix-priority-bottom') == true ){
			fix_priority = "bottom";
		}

        // 画面の幅を取得
        let wrap_width = document.body.offsetWidth
        // 画面の高さを取得
        let window_height = document.documentElement.clientHeight;

        if ( wrap_width < 992 ) {
            //** 画面幅が狭い（1カラム）の場合
            // リセット処理
            sideFix_reset()
        } else {
            //** 画面幅が広い（２カラム）の場合

            let mainSection = document.getElementsByClassName('main-section')[0]
			let sideSection = document.getElementsByClassName('sub-section')[0]
			let parentSection = sideSection.parentNode;

            // コンテンツエリア上端の位置を取得
			let content_position_top = mainSection.getBoundingClientRect().top + window.pageYOffset;
            // コンテンツエリアの高さを取得
			let content_height = mainSection.offsetHeight

			// サイドバーの高さ
			let sidebar_height = sideSection.offsetHeight

			// サイドバーの幅（サイドバーの幅は横幅によって可変するので本来jsで指定しない方が良い）
			// let sidebar_width = sideSection.offsetWidth

            // サイドバー下端までの距離 = コンテンツエリア開始位置 + サイドバーの高さ
			let sidebar_position_bottom_default = content_position_top + sidebar_height;
			
			// サイドバー左端の位置（ Position:fixed の時に必要 ）
			// 一旦positionをリセットしないとウィンドウサイズを変更した時にもサイドバーの左右の位置がおかしくなる
			sideSection.style.position = null;
			sideSection.style.left = null;
			let sidebar_position_left_default = sideSection.getBoundingClientRect().left  + window.pageXOffset;

			// サイドバーのウィンドウ内での表示領域 = ウィンドウ高さ - ヘッダー固定要素の高さ + 余白
			let sidebar_area_height = window_height - sidebar_top_margin();

			// 下端優先だったとしても...
			if ( fix_priority === "bottom" ){
				// ウィンドウ内のサイドバー表示領域がサイドバーよりも高い場合
				if ( sidebar_area_height > sidebar_height ){
					// 上端優先処理
					fix_priority = "top";
				}
			}

			// コンテンツエリア下端の位置を取得 = 上端 + 要素の高さ
			let content_position_bottom = content_position_top + content_height
			// コンテンツエリア下端を表示するまでスクロールしないといけない距離 = コンテンツエリア下端までの距離 - ウィンドウサイズ
			let content_position_bottom_to_scroll = content_position_bottom - window_height;

			// コンテンツエリアとサイドバーの高さの差
			let diff_content_and_sidebar_bottom = content_height - sidebar_height;

			// サイドバー下端を表示するまでスクロールしないといけない距離 = サイドバー下端までの距離 - ウィンドウサイズ
			let to_scroll_sidebar_bottom = sidebar_position_bottom_default - window_height;
			// サイドバー上端が画面上部にくるまでにスクロールしないといけない距離 = サイドバーの開始位置 - サイドバー上部に確保したい余白
			let to_scroll_sidebar_top_stop = content_position_top - sidebar_top_margin();

			// コンテンツエリア下端とウィンドウ下端の距離（スクロール量に応じて可変）
			let content_position_bottom_from_window_bottom = window_height - ( content_position_bottom - window.pageYOffset );

            //  サイドバーがメインコンテンツよりも高い場合は処理しない
            if ( sidebar_height > content_height ){ return; }


			/*-------------------------------------------*/
			// 上端優先 
			/*-------------------------------------------*/

			// サイドバー上端が画面上部までスクロールしたかどうか
			/*-------------------------------------------*/
			let is_sidebar_top_stop = false;
			if ( to_scroll_sidebar_top_stop < window.pageYOffset ){
				is_sidebar_top_stop = true;
			}

			// トップ固定をリリースするタイミングかどうか
			/*-------------------------------------------*/
			let is_sidebar_top_stop_release = false;
			
			// 上部固定状態でのサイドバーの下端 より スクロールしてきたコンテンツエリアの下端が上になったら
			// ※サイドバーの高さが表示エリア内かどうかは関係ない
			if ( sidebar_top_margin() + sidebar_height > mainSection.getBoundingClientRect().bottom ){
				// コンテンツエリア下端とサイドバー下端の位置を揃える
				is_sidebar_top_stop_release = true;
			}

			/*-------------------------------------------*/
			// 下端優先
			/*-------------------------------------------*/

			// サイドバー下端が表示されたかどうか
			let is_sidebar_bottom_display = false;
			if ( to_scroll_sidebar_bottom < window.pageYOffset ){
				is_sidebar_bottom_display = true;
			}

			// コンテンツエリア下端が表示されたかどうか
			let is_content_bottom_display = false;
			if ( content_position_bottom_to_scroll < window.pageYOffset ){
				is_content_bottom_display = true;
			}

			/*-------------------------------------------*/
			// DOM操作（上端優先）
			/*-------------------------------------------*/
			if ( fix_priority === "top" ) {
				// 上部固定するタイミングになったら
				if ( is_sidebar_top_stop ) {
					sideSection.style.position = "fixed";
					sideSection.style.top = sidebar_top_margin() + "px";
					sideSection.style.left = sidebar_position_left_default + "px";

					// 固定解除
					if ( is_sidebar_top_stop_release ){
						sideSection.style.position = null;
						sideSection.style.left = null;

						// 固定解除したときににサイドバー上部に余白を付与
						sideSection.style.top = diff_content_and_sidebar_bottom + "px";
					}
				} else {
					sideFix_reset();
				}

			/*-------------------------------------------*/
			// DOM操作（下端優先）
			/*-------------------------------------------*/
			} else {

				// サイドバー下端が表示されたら
				if ( is_sidebar_bottom_display ){
					sideSection.style.position = "fixed";
					sideSection.style.bottom = "5px";
					sideSection.style.left = sidebar_position_left_default + "px";
					
					// コンテンツエリア下端が表示されたら
					if ( is_content_bottom_display ){
						sideSection.style.top = null;
						// ウィンドウ下端からコンテンツエリア下端までの距離をサイドバーの bottom として指定
						// ※下端が表示されるまで下部に 5px 余白が付与されているので、その分を引き続き付与
						sideSection.style.bottom = content_position_bottom_from_window_bottom + 5 + "px";
					}
				} else {
					sideFix_reset();
				}
			}

			// console.log( 'スクロール : ' + window.pageYOffset);
			// console.log( 'content_position_top : ' + content_position_top);
			// console.log( 'sidebar_height : ' + sidebar_height);
			// console.log( 'sidebar_area_height : ' + sidebar_area_height);
			// console.log( 'is_sidebar_bottom_display : ' + is_sidebar_bottom_display);
			// console.log( 'is_content_bottom_display : ' + is_content_bottom_display);
			// console.log( 'content_position_bottom : ' + content_position_bottom);
			// console.log( 'content_position_bottom_to_scroll : ' + content_position_bottom_to_scroll);
			// console.log( 'content_position_bottom_from_window_bottom : ' + content_position_bottom_from_window_bottom);
			// console.log( 'sidebar_position_left_default : ' + sidebar_position_left_default);

        }
    }
})(window, document);
