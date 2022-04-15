# font-awesome
Font Awesome

Packagist
https://packagist.org/packages/vektor-inc/font-awesome-versions

```
composer require vektor-inc/font-awesome-versions
```

翻訳：

```
composer translate
```
からの Poedit で翻訳

## 使い方

以下のように書いた設定ファイルを読み込む

```
<?php
/**
 * Font Awesome Load modules
 *
 * @package vektor-inc/lightning
 */

use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;

new VkFontAwesomeVersions();
global $font_awesome_directory_uri;
// phpcs:ignore
global $font_awesome_directory_uri;
$template                   = 'lightning';
$theme_root_uri             = get_theme_root_uri( $template );
$font_awesome_directory_uri = "$theme_root_uri/$template/vendor/vektor-inc/font-awesome-versions/src/";

global $vk_font_awesome_version_prefix_customize_panel;
$vk_font_awesome_version_prefix_customize_panel = 'Lightning ';

global $set_enqueue_handle_style;
$set_enqueue_handle_style = 'lightning-design-style';

global $vk_font_awesome_version_priority;
$vk_font_awesome_version_priority = 560;
```


---

* [ 機能追加 ] クラス名だけでも i タグに変換して出力するメソッド追加

0.2.1
* [ 不具合修正 ] Font Awesome へのリンクの設定例表記が間違っていたので修正