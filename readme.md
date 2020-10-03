![Build Check](https://github.com/vektor-inc/Lightning/workflows/Build%20Check/badge.svg)

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
| wp_enqueue_scripts | | vkExUnit_common_style-css | |
| wp_enqueue_scripts | | vkExUnit_common_style-inline-css | |
| wp_enqueue_scripts | | Bootstrap | |
| wp_enqueue_scripts | | lightning-common-style | 全スキン共通CSS |
| wp_enqueue_scripts | | lightning-design-style | デザインスキン |
| wp_enqueue_scripts | | lightning-design-style wp_add_inline_style | デザインスキン |
| wp_enqueue_scripts | | lightning-theme-style | 子テーマでカスタマイズされるのでなるべく後ろである必要がある |
| wp_enqueue_scripts | | vk-font-awesome-css |
| wp_head | 50 | HeaderColorManagerでカスタマイズから指定された色 |
| wp_head | 200 | ExUnit CSSカスタマイズ 共通 |
| wp_head | 201 | ExUnit CSSカスタマイズ 投稿 |


lightning-late-load-style : 以下のハンドルに依存

* lightning-design-style
* lightning-common-style
* vk-font-awesome
* vk-blocks-build-css

---

## Customize panel priority

```
$wp_customize->add_section(
```

* 400 | License key
* 450 | 機能設定
* 501 | デザイン設定
* 502 | フォント設定
* 503 | レイアウト設定
* 510 | ヘッダー上部設定
* 511 | ヘッダー設定
* 513 | キャンペーンテキスト設定
* 520 | トップページスライドショー設定
* 521 | トップページPR BLock設定
* 530 | ページヘッダー設定
* 535 | アーカイブページ設定
* 536 | アーカイブページレイアウト
* 540 | フッター設定
* 543 | コピーライト設定
* 550 | モバイル固定ナビ
* 555 | ウィジェットエリア設定（フッター設定に統合したい）
* 556 | Googleタグマネージャー
* 560 | Font Awesome

---

## UnitTest
このテーマにはPHP Unit Testを用意しています。
下記コマンドで動作してください。

※ Macの場合は[docker-sync](https://github.com/EugenMayer/docker-sync)を使うといいです

```shell

$ docker-compose run wp
```

### UnitTest on wp-env
1. あらかじめ Docker をインストールしておきます。
2. 下記を実行して npm scripts をインストールします。
	```npm install```
3. 下記を実行して composer 関連のファイルをインストールします。
	- Windows の場合
	```npm run composer:install:win```
	- Mac の場合
	```npm run composer:install:win```
4. 下記を実行して Unit Test を開始します。
	- Windows の場合
	```npm run phpunit:win```
	- Mac の場合
	```npm run phpunit:mac```



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

#### プリリクの内容をざっくりで良いので書いてください

* 何の目的でどういう変更をしたのか？
* 作業時のコマンドなど変更になる場合などは記載よろしくお願いいたします。

#### テストの手順を書く

ざっくりで良いので、確認の際はどの画面でどう設定したらどうなるかを確認するのかなど記載してください。

#### テストコードは書いたか？

いろんな設定条件の組み合わせが存在する場合、  
そもそもどういう動作（どの組み合わせだとどうなるのかという）が正しいのか、テスターはもちろん本人も実装から日にちが経つとわからなくなります。
PHPUnitでテストが書けるものはテストを書くようにしてください。  

#### UIは使いやすいか？ 同じような入力内容なのに違うUIになっていないか？

* 自分が使ってみて本当のそのUIがベストか改めて考えてみてください。
* ドキュメント読まずに表示されている画面だけでわかるように説明なども記載してください。
* また、同じような入力内容の場合はUIを揃えるようによろしくお願いいたします。

#### 既存のユーザーに影響がでないか？

* アップデートして既存サイトの表示に変更が出ないか十分注意し、必要に応じて互換処理を入れてください。

#### 共通化できるコードはないか？

同じような記述を複数箇所に書いていませんか？
違うコードが一部分だけなどの場合はその箇所を引数などで渡してclassや関数にするなど共通化しましょう。

#### 関数名 / 変数名は適切か？

第三者が見てどういう処理・内容なのかがわかりやすい名称を心がけてください。

#### HTMLのclass名は適切か？

テーマやプラグインによって一定の命名規則が存在します。
概ね readme.md などに書いてあるとは思いますが、
書いてない場合は他のクラス名など参照の上、前後関係や意味の整合性のとれる名前になっているか今一度考えてみましょう。

#### ライブラリファイルの修正はライブラリの親の修正を先にする

通常ライブラリから各プロジェクトに複製してプロジェクト毎にテキストドメイン置換などを行うので、Lightningなど利用先側で変更コミットしてもライブラリに戻すのが面倒です。  
親のライブラリで編集したいライブラリのgulpのwatchを走らせながら作業して、親のライブラリを先にコミットするようにしてください。  
そうでないと子を修正しても親からの複製で先祖帰りするため。  
