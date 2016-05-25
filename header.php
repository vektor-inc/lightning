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
<div id="bodyInner">
<?php
$args = array(
    'theme_location' => 'Header',
    'container'      => 'nav',
    'items_wrap'     => '<ul id="%1$s" class="%2$s nav gMenu">%3$s</ul>',
    'fallback_cb'    => '',
    'echo'           => false,
    'walker'         => new description_walker()
);
$gMenu = wp_nav_menu( $args ) ;
// メニューがセットされていたら実行
if ( $gMenu ) { ?>
    <a href="#" class="btn btn-default menuBtn menuBtn_left" id="menuBtn" onClick="ga('send', 'event', 'menu_btn', 'tap_left', 'label', 1, {'nonInteraction': 1});"><i class="fa fa-bars" aria-hidden="true"></i></a>
    <a href="#" class="btn btn-default menuBtn menuBtn_right" id="menuBtn" onClick="ga('send', 'event', 'menu_btn', 'tap_right', 'label', 1, {'nonInteraction': 1});"><i class="fa fa-bars" aria-hidden="true"></i></a>
    <section id="navSection" class="navSection">
    <?php get_search_form(); ?>
    </section>
<?php } ?>
<div id="wrap">
<?php do_action('lightning_header_before'); ?>
<header class="navbar siteHeader">
    <?php do_action('lightning_header_prepend'); ?>
    <div class="container">
        <div class="navbar-header">
            <h1 class="navbar-brand siteHeader_logo">
            <a href="<?php echo esc_url(home_url('/')); ?>"><span>
            <?php lightning_print_headlogo(); ?>
            </span></a>
            </h1>
        </div>
        <?php
        // メニューがセットされていたら実行
        if ( $gMenu ) { ?>
            <?php 
            echo '<div id="gMenu_outer" class="gMenu_outer">';
            echo $gMenu;
            echo '</div>';
        } ?>
    </div>
    <?php do_action('lightning_header_append'); ?>
</header>
<?php do_action('lightning_header_after'); ?>