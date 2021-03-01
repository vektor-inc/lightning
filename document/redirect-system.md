### 読み込み階層の変更

自動的に階層変更ができているもの

* page.phpなどのテンプレートファイル
* template_directory_uri()

### フック自体が存在しなくて改変できない
* locate_template()
* load_template()

### 変更できていないもの

#### get_template_part()

フィルターフックなし。
※実行前後にアクションフックがある
locate_template( $templates, true, false );

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
