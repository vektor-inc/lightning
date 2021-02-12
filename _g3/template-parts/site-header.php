<header class="<?php lightning_the_class_name( 'site-header' ); ?> site-header--center">
	<?php do_action( 'lightning_site-header_prepend' ); ?>
	<div class="site-header-container container">

		<?php
		if ( is_front_page() ) {
			$title_tag = 'h1';
		} else {
			$title_tag = 'div';
		}
		?>
		<<?php echo $title_tag; ?> class="<?php lightning_the_class_name( 'site-header-logo' ); ?>">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
			<span><?php lightning_print_headlogo(); ?></span>
		</a>
		</<?php echo $title_tag; ?>>

		<?php do_action( 'lightning_site-header-logo_after' ); ?>

		<?php
		wp_nav_menu( array(
			'theme_location' 	=> 'global-nav',
			'container'      	=> 'nav',
			'container_class'	=> 'global-nav',
			'container_id'		=> 'global-nav',
			'items_wrap'     	=> '<ul id="%1$s" class="%2$s vk-menu-acc global-nav-list nav">%3$s</ul>',
			'fallback_cb'    	=> '',
			'echo'           	=> true,
			'walker'         	=> new VK_Description_Walker(),
		) );
		?>
	</div>
	<?php do_action( 'lightning_site-header_append' ); ?>
</header>