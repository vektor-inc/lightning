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
| wp_enqueue_scripts | | lightning-common-style | 全スキン共通CSS |
| wp_enqueue_scripts | | lightning-theme-style [旧] | 子テーマでカスタマイズされるのでなるべく後ろである必要がある |
| wp_enqueue_scripts | | vk-font-awesome-css [旧] |  
| wp_footer | | vkExUnit_common_style-inline-css [高] | |
| wp_footer | | vk-font-awesome-css [高]  | |
| wp_footer | | lightning-late-load-style [高] | ExUnitなどが後読み込みになっても上書きするためのファイル |
| wp_footer | 20 |  | カスタマイザで出力される色など |
| wp_footer | | lightning-theme-style [高] | 子テーマでカスタマイズされるのでなるべく後ろである必要がある |
