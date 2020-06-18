[![Build Status](https://travis-ci.org/vektor-inc/Lightning.svg?branch=master)](https://travis-ci.org/vektor-inc/Lightning)


# WordPress Theme "Lightning"

Lightning is a very simple & easy to customize theme which is based on the Bootstrap. It is also very friendly with custom post types and custom taxonomies. When you add a new one, the breadcrumbs will be adjusted and posts will look beautifully without editing or adding a template files.

* [Official Web Site](http://lightning.nagoya/)
* [wordpress.org](https://wordpress.org/themes/lightning/)

---

### デザインスキン機能について

Lightningにはデザインスキンを外部から切り替える機能があります。
追加でスキンを作成する場合は下記のファイルを参考にしてください。

https://github.com/kurudrive/lightning-skin-sample


### CSS 読み込み順（正確ではない覚書）

|  読み込みポイント  | Priority |  読み込みファイル  | 備考 |
| ---- | ---- | ---- | ---- |
| wp_enqueue_scripts | | vkExUnit_common_style-css [旧] | |
| wp_enqueue_scripts | | vkExUnit_common_style-inline-css [旧] | |
| wp_enqueue_scripts | | Bootstrap | |
| wp_enqueue_scripts | | lightning-design-style | デザインスキン |
| wp_enqueue_scripts | | lightning-design-style wp_add_inline_style | デザインスキン |
| wp_enqueue_scripts | | lightning-common-style [旧] | 全スキン共通CSS |
| wp_enqueue_scripts | | lightning-theme-style [旧] | 子テーマでカスタマイズされるのでなるべく後ろである必要がある |
| wp_enqueue_scripts | | vk-font-awesome-css [旧] |
| wp_head | 50 | HeaderColorManagerでカスタマイズから指定された色 [旧] |
| wp_head | 200 | ExUnit CSSカスタマイズ 共通 [旧] |
| wp_head | 201 | ExUnit CSSカスタマイズ 投稿 [旧] |
| wp_footer | | vkExUnit_common_style-inline-css [高] | |
| wp_footer | | vk-font-awesome-css [高]  | |
| wp_footer(enqueue_style) | | lightning-common-style [高] | 全スキン共通CSS |
| wp_footer(enqueue_style) | | lightning-theme-style [高] | 子テーマでカスタマイズされるのでなるべく後ろである必要がある |
| wp_footer(enqueue_style) | lightning-common-style / vk-font-awesome / vk-blocks-build-css | lightning-late-load-style [高] | ExUnitなどが後読み込みになっても上書きするためのファイル |
| wp_footer | 20 |  | カスタマイザで出力される色など |
| wp_footer | 25 |  | スキン固有のカスタマイザで出力される色など [高] |
| wp_footer | 26 | HeaderColorManagerでカスタマイズから指定された色 [高] |
| wp_footer | 200 | ExUnit CSSカスタマイズ 共通 [高] |
| wp_footer | 201 | ExUnit CSSカスタマイズ 投稿 [高] |

[旧] 通常設定時
[高] 高速化設定時

## UnitTest
このテーマにはPHP Unit Testを用意しています。
下記コマンドで動作してください。

※ Macの場合は[docker-sync](https://github.com/EugenMayer/docker-sync)を使うといいです

```shell

$ docker-compose run wp
```

## 各種コマンド

### ビルド

js,scss,テキストドメインのビルドをすべて行う
```
$ npm run build
```

#### jsビルド
```
$ npm run bulid:script
```

#### scssビルド
```
$ npm run build:style
```

#### テキストドメイン書き換え
```
$ npm run build:text-domain
```

### 開発モード

#### js
```
$ npm run watch:script
```

#### scss
```
$ npm run watch:style
```

### dist作成

```
$ npm run build
$ npm run dist
```

`dist/`内に管理画面でのインポート用zipと、転送用のテーマディレクトリが作成されます。



## プルリクエストを送る際の確認事項

#### 複数の内容を含まない

複数の趣旨の変更内容（機能の不具合修正とまったく別のアクションフック追加などの仕様変更など）を一つのプルリクエストで送ると確認・マージが非常ににやりにくくいので、内容別で送るようにしてください。

#### ライブラリファイルの修正はライブラリの親の修正を先にする

通常ライブラリから各プロジェクトに複製してプロジェクト毎にテキストドメイン置換などを行うので、Lightningなど利用先側で変更コミットしてもライブラリに戻すのが面倒です。  
親のライブラリで編集したいライブラリのgulpのwatchを走らせながら作業して、親のライブラリを先にコミットするようにしてください。  
そうでないと子を修正しても親からの複製で先祖帰りするため。  

#### プリリクの内容をざっくりで良いので書いてください

何の目的でどういう変更をしたのかや、作業時のコマンドなど変更になる注意などある場合は記載よろしくお願いいたします。
