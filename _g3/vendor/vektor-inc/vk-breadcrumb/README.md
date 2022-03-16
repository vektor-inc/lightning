# VK Bread Crumb

```
composer require vektor-inc/vk-breadcrumb
```

load autoload
```
require_once dirname( __FILE__ ) . '/vendor/autoload.php';
```

Display bread crumb
```
use VektorInc\VK_Breadcrumb\VkBreadcrumb;
$vk_breadcrumb      = new VkBreadcrumb();
$breadcrumb_options = array(
	'id_outer'        => 'breadcrumb',
	'class_outer'     => 'breadcrumb',
	'class_inner'     => 'container',
	'class_list'      => 'breadcrumb-list',
	'class_list_item' => 'breadcrumb-list__item',
);
$vk_breadcrumb->the_breadcrumb( $breadcrumb_options );
```

## Change log

0.1.0
[ Spacification Change ] Add get_breadcrumb() method

0.0.5 
[ Bug fix ] id が class_outer になってしまっていたので修正
