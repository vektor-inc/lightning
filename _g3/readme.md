
### 読み込み階層の変更

自動的に階層変更ができているもの

* page.phpなどのテンプレートファイル
* template_directory_uri()

## CSS命名規則

基本形式

コンポーネント名（あるいはプロジェクト名）-要素名--属性名--属性値

例)
site-header-logo--float--left

* キャメルケース・アンダーバーは使用しない（ExUnitなど他のDOM要素の上書きは仕方ない）
* 英単語の連結及びコンポーネント名と要素名の連結はハイフン（-）
* FLOCSSのように要素の前に l- p- c- をつけるのは開発者にある程度のノウハウが必要とされるので不採用とした
* VK Blocks などではDOMの階層をほぼすべて含めていたが、DOMの階層変更が入ると整合性がとれなくなるので親のコンポーネント名さえ記載すれば良いものとする
* コンポーネント名と要素名は本当は__等でも良かったが、迷ったり間違ったりすると整合性がとれなくなるので、深く考えなくても良いように-とした
* ハイフンだけでは 単語の区切りか DOM の階層かわからんやんけ！と思われるかもしれないが、実際問題階層は省略できる部分は省略した方がCSSが短くできる＆必要なのは概ねコンポーネント名と要素名だけなので問題にならない。

### クラスの書き換え例

```
add_filter( "lightning_get_class_names", 'lightning_add_class_names_site_header_origin3', 10, 2 );
function lightning_add_class_names_site_header_origin3( $class_names, $position ){
    if ( $position === 'site-header' ){
        $class_names['site-header'] .= ' site-header--layout--nav-float';
    }
    return $class_names;
}

add_filter( "lightning_get_the_class_name", 'lightning_add_class_name_site_header_origin3', 10, 2 );
function lightning_add_class_name_site_header_origin3( $class_name, $position ){
    if ( $position === 'site-header' ){
        $class_name .= ' site-header--layout--nav-float';
    }
    return $class_name;
}
```