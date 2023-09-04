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

    if( lightningOpt.header_scrool && siteHeader ){

        // ヘッダーの元の高さを取得
        const siteHeaderContainerHeight = document.getElementById('site-header').offsetHeight;

        let body_class_timer = false;
        let body_class_lock = false;

		// ヘッダースクロール時に実行される処理
		let header_scrool_func = ()=>{

			// サイトヘッダーの要素を取得
			let siteHeader = document.getElementById('site-header');
			// サイトヘッダーの次の要素を取得
			let siteHeaderNext = siteHeader.nextElementSibling;

			// ボディのクラスがロックされていない場合、かつスクロール量がサイトヘッダーの高さを超えた場合
			if( ! body_class_lock && window.scrollY > siteHeaderContainerHeight ){
				// ヘッダースクロール識別用のclass追加
				document.body.classList.add('header_scrolled')
				if(lightningOpt.add_header_offset_margin){
					// コンテナ部分をfixedにするので、ガクンとならないように、ヘッダーの次の要素にヘッダーの高さ分余白を追加する 
					siteHeaderNext.style.marginTop = siteHeaderContainerHeight + "px";
				}
			} else {
				// ヘッダースクロール識別用のclass削除
				document.body.classList.remove('header_scrolled')
				if(lightningOpt.add_header_offset_margin){
					// ヘッダーの次の要素の余白を削除する
					siteHeaderNext.style.marginTop = null;
				}
			}
		}

		// ページ内リンクの場合に固定ヘッダーが被ってしまうのでスクロール識別クラスを削除する //////////////////////////

		// ページ内で#で始まるページ内リンク（ドメイン名を含まない）をクリックされた場合にスクロール識別クラスを削除する
        let remove_header = (e) => {
            document.body.classList.remove('header_scrolled')
            window.removeEventListener('scroll', header_scrool_func)
            if (body_class_timer !== false) {
                clearTimeout(body_class_timer)
            }
            body_class_lock = true
            body_class_timer = setTimeout(()=>{
                window.addEventListener('scroll', header_scrool_func, true)
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
				window.removeEventListener('scroll', header_scrool_func, false);

				// 一定時間後に再度イベントリスナーを有効化
				setTimeout(() => {
					window.addEventListener('scroll', header_scrool_func, false);
				}, 500); // 例として500ms後に再度イベントリスナーを有効化する
			} else {
				window.addEventListener('scroll', header_scrool_func, false)
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
