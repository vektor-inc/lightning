# 【重要】 G３版と旧版の切り替えのためのリダイレクト処理について

## 改変している関数

### get_template_directory_uri()

g階層のURLを返すように改変済

### get_parent_theme_file_path( $file )

g階層のパスを返すように改変済

※ get_template_directory() を基準にしているので、
もし get_template_directory() が改変する事になったら注意

### get_template_part()

★ header.php / footer.php / sidebar.php は読み込むと二重表示されるので、このファイルに対しては使わない。

g階層指定がない引数の場合g階層をつけた親階層を参照するように改変している。
・子テーマに複製したファイルg階層指定のないファイルを読み込もうとするため。

フィルターフックがないため、キレイに置換する事が難しい。
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

* header.php 内で lightning_get_template_part() を使って g階層/header.php を読み込む
* 子テーマに複製されたテンプレートファイルが get_header()を使っている事があるため、直下の header.php にはlightning_get_template_part() を使ってリダイレクトさせる。
* get_header( post_type() ); などをなどをやられてる場合はそもそも header-{$post_type}.php を子テーマで作られてるので問題ない

という事で、get_header() などを使用した場合も実際には問題なく動作するのだが、
直下の header.php を無駄に経由しないといけなくなるため lightning_get_template_part() の使用を推奨する。

→ lightning_get_template_part() を使用する。


### 読み込み階層の変更

自動的に階層変更ができているもの

* page.phpなどのテンプレートファイル
* get_template_directory_uri()


---

## 変更していないもの

#### get_template_directory() 

親テーマのディレクトリパスを返す。
g階層無しのファイルを参照する処理もあるため、この関数は改変しない。

get_parent_theme_file_path() の内部でも get_template_directory() を使用している。

末尾に / なし
フックあり

```
$template     = get_template();
$theme_root   = get_theme_root( $template );
$template_dir = "$theme_root/$template";
return apply_filters( 'template_directory', $template_dir, $template, $theme_root );
```

### get_stylesheet_directory()

`stylesheet_directory` フィルターフックがあるので改変は可能

```
$stylesheet     = get_stylesheet(); // フックで改変は可能
$theme_root     = get_theme_root( $stylesheet ); // テーマディレクトリ自体（/wp-content/themes/）
apply_filters( 'stylesheet_directory', $stylesheet_dir, $stylesheet, $theme_root );
```

#### get_template()

テンプレート名だけを返す。
フックはあるが改変してしまうとテーマ判定などで困るので改変しない。

return apply_filters( 'template', get_option( 'template' ) );

---

## 変更できないもの

### locate_template( $template_names, $load = false, $require_once = true )

フックなし
get_template_part() の中で実行される。
load_template( $located, $require_once );

### load_template()

フックなし
locate_template() の中で実行される。
最終的にファイルを読み込んでいる


