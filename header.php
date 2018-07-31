<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php
global $lightning_theme_options;
$lightning_theme_options = get_option( 'lightning_theme_options' );
?>
<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
<?php do_action( 'lightning_header_before' ); ?>
<header class="navbar siteHeader">
	<?php do_action( 'lightning_header_prepend' ); ?>
	<div class="container siteHeadContainer">
		<div class="navbar-header">
			<h1 class="navbar-brand siteHeader_logo">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><span>
			<?php lightning_print_headlogo(); ?>
			</span></a>
			</h1>
			<?php do_action( 'lightning_header_logo_after' ); ?>
			<?php
			$args  = array(
				'theme_location' => 'Header',
				'container'      => 'nav',
				'items_wrap'     => '<ul id="%1$s" class="%2$s nav gMenu">%3$s</ul>',
				'fallback_cb'    => '',
				'echo'           => false,
				'walker'         => new description_walker(),
			);
			$gMenu = wp_nav_menu( $args );
		?>
		</div>

		<?php
		if ( $gMenu ) {
			echo '<div id="gMenu_outer" class="gMenu_outer">';
			echo $gMenu;
			echo '</div>';
		}
		?>
	</div>
	<?php do_action( 'lightning_header_append' ); ?>
</header>
<?php do_action( 'lightning_header_after' ); ?>
