<header class="<?php lightning_the_class_name( 'site-header' ); ?>">
	<?php do_action( 'lightning_site-header_prepend' ); ?>
	<div class="site-header__container container">

		<?php
		if ( is_front_page() ) {
			$title_tag = 'h1';
		} else {
			$title_tag = 'p';
		}
		?>
		<<?php echo $title_tag; ?> class="<?php lightning_the_class_name( 'site-header__logo' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span><?php lightning_print_headlogo(); ?></span>
		</a>
		</<?php echo $title_tag; ?>>

		<?php do_action( 'lightning_site-header_logo_after' ); ?>

		<?php
		wp_nav_menu( array(
			'theme_location' 	=> 'global-nav',
			'container'      	=> 'nav',
			'container_class'	=> 'global-nav',
			'items_wrap'     	=> '<ul id="%1$s" class="%2$s ' . lightning_get_class_name( 'global-nav__list' ) . '">%3$s</ul>',
			'fallback_cb'    	=> '',
			'echo'           	=> true,
			'walker'         	=> new VK_Description_Walker(),
		) );
		?>
	</div>
	<?php do_action( 'lightning_site-header_append' ); ?>
</header>