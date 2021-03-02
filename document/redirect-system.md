# G３版と旧版の切り替えのためのリダイレクト処理について

### get_template_part()

g階層指定がない引数の場合g階層をつけた親階層を参照するように改変している。
・子テーマに複製したファイルg階層指定のないファイルを読み込もうとするため。

フィルターフックがないため、キレイに置換する事ができない。
実行前後にアクションフックがあるので、引数のファイルが存在しない場合にg階層を付与したファイルを参照するように改変。

g階層指定のない場合の優先順
1. 子テーマでg階層じゃないファイル
2. 子テーマでg階層のファイル
3. 親テーマでg階層のファイル

get_template_part() は標準でファイルがあれば出力されるので、
g階層をを優先読み込みにしても、標準階層にもファイルがある場合に二重出力となってしまうため
子テーマではg階層のないファイルを先に処理して、存在する場合は以下の処理を停止している。

locate_template( $templates, true, false );

get_template_part() で header.php などをなどを読み込んでしまうと、
g階層を読むようにフックしているので、子にg階層のがあったら読み込むが、
直下の header.php も読み込んでしまうので、
直下の header.php が g階層のファイルなど読み込むと２重表示になってしまう。

→ ★ テーマディレクトリ直下にファイルがあるケースの場合は get_template_part() は使用せず、
lightning_get_template_part() を使用する

### get_header() / get_footer() / get_sidebar()

基本使わない。

get_header() などはフィルターフックがなく、とは言え header.php などはディレクトリ上に置かなくてはいけないので、
g階層への直接参照ができない。

get_header() を使われた場合は、フィルターがなく改変できないため直下の header.php を読み込むため以下の処理とする。

* header.php で lightning_get_template_part() で g階層/header.php を読み込む
* 子テーマに複製されたファイルなどが読み込む事があるため、直下の header.php にはリダイレクトコードを書いておく
* get_header( post_type() ); などをなどをやられてる場合はそもそも header-{$post_type}.php を子テーマで作られてるので問題ない

という事で、get_header() などを使用した場合も実際には問題なく動作するのだが、
直下の header.php を無駄に経由しないといけなくなる。

→ lightning_get_template_part() を使用する。


### 読み込み階層の変更

自動的に階層変更ができているもの

* page.phpなどのテンプレートファイル
* template_directory_uri()

### フック自体が存在しなくて改変できない
* locate_template()
* load_template()

### 変更できていないもの






#### locate_template( $template_names, $load = false, $require_once = true )

get_template_part() の中で実行される。
load_template( $located, $require_once );

#### load_template(()

locate_template() の中で実行される。
最終的にファイルを読み込んでいる


#### get_header()

フィルターフックなし。
※実行前にアクションフックあり


#### get_stylesheet_directory()

'stylesheet_directory' フィルターフックがあるので改変可能
$stylesheet     = get_stylesheet(); // フックで改変は可能
$theme_root     = get_theme_root( $stylesheet ); // テーマディレクトリ自体（/wp-content/themes/）
apply_filters( 'stylesheet_directory', $stylesheet_dir, $stylesheet, $theme_root );

#### get_parent_theme_file_path()

フックはあるが get_template_directory() を基準にしているので、
もし get_template_directory() が改変されているなら継承する

#### get_template_directory() 

末尾に / なし
フックあり

$template     = get_template();
$theme_root   = get_theme_root( $template );
$template_dir = "$theme_root/$template";
return apply_filters( 'template_directory', $template_dir, $template, $theme_root );

#### get_template()

フックはあるが親子テーマから親テーマの参照などに影響しそうなので触らない方が良さげ
apply_filters( 'template', get_option( 'template' ) );



---

## 互換性について

### 子テーマでカスタマイズされている場合の対応

### 子テーマに複製したファイルが get_template_part() で古い階層のファイルを読んでいるケース

#### 例) get_template_part( 'template-parts/post/content.php' );

G3では 
親/template-parts/post/content.php は存在しないので
親/_g2/template-parts/post/content.php を読み込まないといけない。

→ 子テーマには存在する可能性がある。

子/template-parts/post/content.php があったら表示
なければ
親/_g2/template-parts/post/content.php

→ 子テーマの新しいディレクトリに存在する可能性がある

子/template-parts/post/content.php → 何もしない
子/_g2/template-parts/post/content.php
親/_g2/template-parts/post/content.php

#### 例) get_template_part( '_g2/template-parts/post/content.php' );

わざわざ新しいディレクトリが書いてある＝わかっているので特別な処理をする必要はない

### get_template_part() の改変処理

引数でうけとったパスが _g2 _ g3 を含んでいるか
含んでいなかったら {
    子テーマ直下に引数のファイルがあるか確認 {
        あれば標準で良いので return
    }
    子テーマに階層を付与してファイルが存在するか確認 {
        あれば読み込み
    } else {
        親テーマに階層を付与してあれば読み込み
    }
}


## 参考

```
function locate_template( $template_names, $load = false, $require_once = true ) {
	$located = '';
	foreach ( (array) $template_names as $template_name ) {
		if ( ! $template_name ) {
			continue;
		}
		if ( file_exists( STYLESHEETPATH . '/' . $template_name ) ) {
			$located = STYLESHEETPATH . '/' . $template_name;
			break;
		} elseif ( file_exists( TEMPLATEPATH . '/' . $template_name ) ) {
			$located = TEMPLATEPATH . '/' . $template_name;
			break;
		} elseif ( file_exists( ABSPATH . WPINC . '/theme-compat/' . $template_name ) ) {
			$located = ABSPATH . WPINC . '/theme-compat/' . $template_name;
			break;
		}
	}

	if ( $load && '' != $located ) {
		load_template( $located, $require_once );
	}

	return $located;
}
```
