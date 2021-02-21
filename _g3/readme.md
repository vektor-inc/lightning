
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