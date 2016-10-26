[![Build Status](https://travis-ci.org/vektor-inc/Lightning.svg?branch=master)](https://travis-ci.org/vektor-inc/Lightning)


# WordPress Theme "Lightning"

Lightning is a very simple & easy to customize theme which is based on the Bootstrap. It is also very friendly with custom post types and custom taxonomies. When you add a new one, the breadcrumbs will be adjusted and posts will look beautifully without editing or adding a template files.

* [Official Web Site](http://lightning.vektor-inc.co.jp/)
* [wordpress.org](https://wordpress.org/themes/lightning/)

---

### デザインスキン機能について

Lightningにはデザインスキンを外部から切り替える機能があります。
追加でスキンを作成する場合は下記のファイルを参考にしてください。

https://github.com/kurudrive/lightning-skin-sample

--- 

### クラスの命名ルールについて

#### 単語の連結

* 複数の単語を小文字のまま直接連結しない。
* 単語の連結が - （ハイフン）であったり _ （アンダーバー）であったりする箇所があるが、WordPressがデフォルトで出力してきているもの及びBootstrapで用意されているclass名はハイフンで連結されており、Lightning独自で追加したclass部分についてはアンダーバーで連結している。(Bootstrapのcssファイルの影響を受ける部分かどうか判断しやすくするためなど)
