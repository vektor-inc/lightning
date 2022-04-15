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

VkFontAwesomeVersions::init();

global $vkfav_customize_panel_prefix;
$vkfav_customize_panel_prefix = 'Lightning ';

global $vkfav_customize_panel_priority;
$vkfav_customize_panel_priority = 560;

global $vkfav_set_enqueue_handle_style;
$vkfav_set_enqueue_handle_style = 'lightning-design-style';
```


---

0.3.0
* [ 機能追加 ] クラス名だけでも i タグに変換して出力するメソッド追加
* [ 機能改善 ] ディレクトリ uri を グローバル変数で受け渡さなくても自動取得するように対応
* [ 仕様変更 ] グローバル変数が不適切だったため調整

0.2.1
* [ 不具合修正 ] Font Awesome へのリンクの設定例表記が間違っていたので修正