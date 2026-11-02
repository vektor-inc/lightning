;
((window, document) => {
    /*----------------------------------------------------------*/
    /*  Add scrolled class
    /*----------------------------------------------------------*/
    // Scroll function
	let bodyClass = () => {
		if(window.scrollY > 0){
			document.body.classList.add('scrolled')
		}else{
			document.body.classList.remove('scrolled')
		}
	}

	let initBodyClass = () => {
		if (document.readyState !== "loading") { // If the document is not loading
			bodyClass();
		} else { // If the document is still loading
			window.addEventListener("DOMContentLoaded", bodyClass, false);
		}
	}

	window.addEventListener('scroll', bodyClass, false)
	initBodyClass();

	/*----------------------------------------------------------*/
    /*  Add header_scrolled class
    /*----------------------------------------------------------*/
    // ヘッダー要素がない場合の判別
    const siteHeader = document.getElementById('site-header');

    if( lightningOpt.header_scroll && siteHeader ){

        // ヘッダーの元の高さを取得
        const siteHeaderContainerHeight = document.getElementById('site-header').offsetHeight;

        let body_class_timer = false;
        let body_class_lock = false;

		// 固定されたヘッダーの高さを CSS 変数 --vk-header-fixed-height に入れる処理
		// 位置: 張り付き（sticky）のブロックを、固定ヘッダーの下に位置させるために使う
		let set_header_fixed_height = ()=>{
			let siteHeader = document.getElementById('site-header');
			// ヘッダー要素が取得できない場合は何もしない（CSS 側の 0px のまま）
			if( ! siteHeader ) return;
			document.documentElement.style.setProperty('--vk-header-fixed-height', siteHeader.offsetHeight + 'px');
		}

		// 固定中にヘッダーの高さが変わった時（メニュー画像の遅延読み込みなど）に、CSS 変数を更新するための監視
		let header_height_observer = null;

		// ヘッダーの高さの監視を始める（固定中だけ監視する）
		let start_header_height_observe = ()=>{
			// ResizeObserver が使えない環境では何もしない（resize の時だけ高さを取得し直す）
			if( typeof ResizeObserver === 'undefined' ) return;
			// すでに監視中の場合は二重に observe しない
			if( header_height_observer ) return;
			let siteHeader = document.getElementById('site-header');
			if( ! siteHeader ) return;
			header_height_observer = new ResizeObserver(()=>{
				// header_scrolled が外れている間は高さを読まない
				if( ! document.body.classList.contains('header_scrolled') ) return;
				set_header_fixed_height();
			});
			// offsetHeight と同じ border-box の高さの変化を監視する
			header_height_observer.observe(siteHeader, { box: 'border-box' });
		}

		// ヘッダーの高さの監視をやめる（header_scrolled が外れる所で必ず呼ぶ）
		let stop_header_height_observe = ()=>{
			if( ! header_height_observer ) return;
			header_height_observer.disconnect();
			header_height_observer = null;
		}

		// ヘッダースクロール時に実行される処理
		let header_scroll_func = ()=>{

			// サイトヘッダーの要素を取得
			let siteHeader = document.getElementById('site-header');
			// サイトヘッダーの次の要素を取得
			let siteHeaderNext = siteHeader.nextElementSibling;

			// ボディのクラスがロックされていない場合、かつスクロール量がサイトヘッダーの高さを超えた場合
			if( ! body_class_lock && window.scrollY > siteHeaderContainerHeight ){
				// header_scrolled を新たに付ける時だけ判定する（スクロールのたびにヘッダーの高さを読まないため）
				let is_newly_scrolled = ! document.body.classList.contains('header_scrolled');
				// ヘッダースクロール識別用のclass追加
				document.body.classList.add('header_scrolled')
				if(lightningOpt.add_header_offset_margin){
					// コンテナ部分をfixedにするので、ガクンとならないように、ヘッダーの次の要素にヘッダーの高さ分余白を追加する
					siteHeaderNext.style.marginTop = siteHeaderContainerHeight + "px";
				}
				if( is_newly_scrolled ){
					// 固定になった後のヘッダーの高さを取得する
					// 余白を入れる前に高さを読むと、レイアウトが中途半端な状態（ヘッダーは固定で場所が空いたが余白がまだ無い）で確定し、
					// ブラウザがスクロール位置を補正してスクロールが押し戻されるため、余白を入れたあとに読む
					set_header_fixed_height();
					// 固定中にヘッダーの高さが変わっても追従できるように、高さの監視を始める
					start_header_height_observe();
				}
			} else {
				// ヘッダースクロール識別用のclass削除
				document.body.classList.remove('header_scrolled')
				stop_header_height_observe();
				if(lightningOpt.add_header_offset_margin){
					// ヘッダーの次の要素の余白を削除する
					siteHeaderNext.style.marginTop = null;
				}
			}
		}

		// ウィンドウ幅が変わるとヘッダーの高さも変わるため、固定中は高さを取得し直す
		window.addEventListener('resize', () => {
			if( document.body.classList.contains('header_scrolled') ){
				set_header_fixed_height();
			}
		}, false);

		// ページ内リンクの場合に固定ヘッダーが被ってしまうのでスクロール識別クラスを削除する //////////////////////////

		// ページ内で#で始まるページ内リンク（ドメイン名を含まない）をクリックされた場合にスクロール識別クラスを削除する
        let remove_header = (e) => {
            document.body.classList.remove('header_scrolled')
            stop_header_height_observe();
            window.removeEventListener('scroll', header_scroll_func)
            if (body_class_timer !== false) {
                clearTimeout(body_class_timer)
            }
            body_class_lock = true
            body_class_timer = setTimeout(()=>{
                window.addEventListener('scroll', header_scroll_func, false)
                body_class_lock = false
            }, 2000);
		}

		// ページ読み込み時に実行される処理
		document.addEventListener('readystatechange', () => {
			if (document.readyState === 'complete') {

				// サイトヘッダーの要素を取得
				let siteHeader = document.getElementById('site-header');
				// サイトヘッダーの次の要素を取得
				let siteHeaderNext = siteHeader.nextElementSibling;

				// ページ内リンクを持つaタグを取得
				Array.prototype.forEach.call(
					document.getElementsByTagName('a'),
					(elem) => {
						let href = elem.getAttribute('href')

						// ページトップをクリックされたときはページヘッダー上部に付与してある余白を削除する
						if ( '#top' === href ){
							elem.addEventListener('click', () => {
								siteHeaderNext.style.marginTop = null;
							})
						}

						// ページ内リンク以外のリンクを無視する
						if(!href || href.indexOf('#') === -1) return;
						// role属性があり、属性の値がtabである場合には処理しない
						// if (['tab', 'button']. のよう button を含めると、ボタンブロックのページ内リンクした時にリンク先の頭に固定ナビが上に被ってしまうので tab だけにしている
						if (['tab'].indexOf(elem.getAttribute('role')) > 0) return;
						// data-toggle属性を持つ場合は何もしない
						if (elem.getAttribute('data-toggle')) return;
						// carousel-control属性を持つ場合は何もしない
						if (elem.getAttribute('carousel-control')) return;

						// a タグ（ページ内リンク）をクリックされたらスクロール識別クラスを削除する
						elem.addEventListener('click', remove_header)
					}
				)
			}
		});

		// ページがロードされた時の処理
		document.addEventListener('DOMContentLoaded', () => {
			if (location.hash) {
				// URLに#が含まれる場合、scrollイベントリスナーを一時的に無効化
				window.removeEventListener('scroll', header_scroll_func, false);

				// 一定時間後に再度イベントリスナーを有効化
				setTimeout(() => {
					window.addEventListener('scroll', header_scroll_func, false);
				}, 500); // 例として500ms後に再度イベントリスナーを有効化する
			} else {
				window.addEventListener('scroll', header_scroll_func, false)
			}
		});

    }

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
					// iframeの読み込みが完了しているかどうかをチェックする
					if (i.contentWindow.document.readyState === 'complete') {
						var iframeWidth = i.getAttribute("width");
						var iframeHeight = i.getAttribute("height");
						var iframeRate = iframeHeight / iframeWidth;
						var nowIframeWidth = i.offsetWidth
						var newIframeHeight = nowIframeWidth * iframeRate;
						i.style.maxWidth = '100%'
						i.style.height = newIframeHeight + 'px'
					} else {
						// iframeの読み込みが完了するまで待つ
						i.contentWindow.document.addEventListener('DOMContentLoaded', () => {
							var iframeWidth = i.getAttribute("width");
							var iframeHeight = i.getAttribute("height");
							var iframeRate = iframeHeight / iframeWidth;
							var nowIframeWidth = i.offsetWidth
							var newIframeHeight = nowIframeWidth * iframeRate;
							i.style.maxWidth = '100%'
							i.style.height = newIframeHeight + 'px'
						});
					}
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


})(window, document);
