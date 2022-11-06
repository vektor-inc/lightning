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

### Font Awesome のファイル自体の読み込み

以下のように書いた設定ファイルを読み込む

```
<?php
/**
 * Font Awesome Load modules
 *
 * @package vektor-inc/lightning
 */

use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;
if ( method_exists( 'VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions', 'init' ) ) {
	VkFontAwesomeVersions::init();
}

// カスタマイズ画面でのパネルのプリフィックス（オプション）
global $vkfav_customize_panel_prefix;
$vkfav_customize_panel_prefix = 'Lightning ';

// カスタマイズ画面でのパネルの表示順（オプション）
global $vkfav_customize_panel_priority;
$vkfav_customize_panel_priority = 560;

// タグ関連のCSSの出力する場合、ひっかけるハンドル名（オプション）
global $vkfav_set_enqueue_handle_style;
$vkfav_set_enqueue_handle_style = 'lightning-design-style';
```

### 設定画面での例の表示

```
// ページの最初記記述
use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;

// 使用する場所に記述
if ( method_exists( 'VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions', 'ex_and_link' ) ) {
	$example_class_array            = array(
		'v4.7' => 'fa-envelope-o',
		'v5'   => 'far fa-envelope',
		'v6'   => 'fa-regular fa-envelope',
	);
	$icon_description = VkFontAwesomeVersions::ex_and_link( 'class', $example_class_array );
}
```

### アイコンの表示

```
// ページの最初記記述
use VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions;

// 使用する場所に記述
if ( method_exists( 'VektorInc\VK_Font_Awesome_Versions\VkFontAwesomeVersions', 'get_icon_tag' ) ) {
	$icon_description = VkFontAwesomeVersions::get_icon_tag( 'fa-regular fa-envelope' );
}
```

#### i タグに通常の Font Awesome のクラス以外のものを追加する場合

```
$icon_description = VkFontAwesomeVersions::get_icon_tag( 'fa-regular fa-envelope', 'example-class-name' );
```

---

0.4.1

- [ 不具合修正 ] WordPress.com でファイルパスが正しく取得できない不具合対応


0.4.0

- [ 機能追加 ] i タグに Font Awesome 以外のタグを追加できるように引数追加
- [ 機能追加 ] アイコンの例を書き換えられるように引数追加

  0.3.3

- [ 機能追加 ] クラス名だけでも i タグに変換して出力するメソッド追加
- [ 機能改善 ] ディレクトリ uri を グローバル変数で受け渡さなくても自動取得するように対応
- [ 仕様変更 ] グローバル変数が不適切だったため調整

  0.2.1

- [ 不具合修正 ] Font Awesome へのリンクの設定例表記が間違っていたので修正
