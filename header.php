<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php global $lightning_theme_options;
$lightning_theme_options = get_option('lightning_theme_options'); ?>
<?php wp_head();?>

</head>
<body <?php body_class(); ?>>
<?php do_action('lightning_header_before'); ?>
<header class="navbar navbar-fixed-top siteHeader">
    <?php do_action('lightning_header_prepend'); ?>
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#navbar-ex-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <h1 class="navbar-brand siteHeader_logo">
            <a href="<?php echo home_url(); ?>"><span>
            <?php lightning_print_headlogo(); ?>
            </span></a>
            </h1>
        </div>

        <div class="collapse navbar-collapse" id="navbar-ex-collapse">
        <?php wp_nav_menu( array(
            'theme_location'    => 'Header',
            'container'         => 'nav',
            'items_wrap'        => '<ul id="%1$s" class="%2$s nav navbar-nav gMenu">%3$s</ul>',
            'fallback_cb'       => '',
            'walker' => new description_walker()
        ) ); ?>
        </div>
    </div>
    <?php do_action('lightning_header_append'); ?>
</header>
<?php do_action('lightning_header_after'); ?>
