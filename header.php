<?php
/**
 * Header Template of Lightning
 *
 * @package Lightning
 */

global $lightning_theme_options;
$lightning_theme_options = get_option( 'lightning_theme_options' );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>

</head>
<body <?php body_class(); ?>>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to the content', 'lightning' ); ?></a>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to the Navigation', 'lightning' ); ?></a>
<?php
if ( function_exists( 'wp_body_open' ) ) {
	wp_body_open();
} else {
	do_action( 'wp_body_open' );
}
do_action( 'lightning_header_before' );
?>
<header class="<?php lightning_the_class_name( 'header' ); ?>">
	<?php do_action( 'lightning_header_prepend' ); ?>
	<div class="container siteHeadContainer">
		<div class="navbar-header">
			<?php
			if ( is_front_page() ) {
				$title_tag = 'h1';
			} else {
				$title_tag = 'p';
			}
			?>
			<<?php echo esc_html( $title_tag ); ?> class="<?php lightning_the_class_name( 'header_logo' ); ?>">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<span><?php lightning_print_headlogo(); ?></span>
			</a>
			</<?php echo esc_html( $title_tag ); ?>>
			<?php do_action( 'lightning_header_logo_after' ); ?>
			<?php
			$args        = array(
				'theme_location' => 'Header',
				'container'      => 'nav',
				'items_wrap'     => '<ul id="%1$s" class="%2$s ' . lightning_get_the_class_name( 'nav_menu_header' ) . '">%3$s</ul>',
				'fallback_cb'    => '',
				'echo'           => false,
				'walker'         => new description_walker(),
			);
			$global_menu = wp_nav_menu( $args );
			?>
		</div>

		<?php
		if ( $global_menu ) {
			echo '<div id="gMenu_outer" class="gMenu_outer">';
			echo wp_kses_post( $global_menu );
			echo '</div>';
		}
		?>
	</div>
	<?php do_action( 'lightning_header_append' ); ?>
</header>
<?php do_action( 'lightning_header_after' ); ?>
