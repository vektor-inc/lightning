<footer class="<?php lightning_the_class_name( 'site-footer' ); ?>">

	<?php if ( has_nav_menu( 'footer-nav' ) ) : ?>
		<?php
		wp_nav_menu(
			array(
				'theme_location'  => 'footer-nav',
				'container'       => 'nav',
				'container_class' => 'footer-nav',
				'items_wrap'      => '<div class="container"><ul id="%1$s" class="%2$s ' . lightning_get_the_class_name( 'footer-nav-list' ) . ' nav nav--line">%3$s</ul></div>',
				'fallback_cb'     => '',
				'depth'           => 1,
			)
		);
		?>
	<?php endif; ?>
	
	<?php
	$footer_widget_area_count = apply_filters( 'lightning_footer_widget_area_count', 3 );
	$footer_widget_exists     = false;
	for ( $i = 1; $i <= $footer_widget_area_count; $i++ ) {
		if ( is_active_sidebar( 'footer-widget-' . $i ) ) {
			$footer_widget_exists = true;
		}
	}
	?>
	<?php if ( true === $footer_widget_exists ) : ?>
		<div class="container site-footer-content">
		<?php do_action( 'lightning_site_footer_content_prepend' ); ?>
			<div class="row">
				<?php
				// Area setting
				$footer_widget_area = array(
					// Use 1 widget area
					1 => array( 'class' => 'col-lg-12' ),
					// Use 2 widget area
					2 => array( 'class' => 'col-lg-6 col-md-6' ),
					// Use 3 widget area
					3 => array( 'class' => 'col-lg-4 col-md-6' ),
					// Use 4 widget area
					4 => array( 'class' => 'col-lg-3 col-md-6' ),
					// Use 6 widget area
					6 => array( 'class' => 'col-lg-2 col-md-6' ),
				);

				// Print widget area
				for ( $i = 1; $i <= $footer_widget_area_count; ) {
					echo '<div class="' . $footer_widget_area[ $footer_widget_area_count ]['class'] . '">';
					if ( is_active_sidebar( 'footer-widget-' . $i ) ) {
						dynamic_sidebar( 'footer-widget-' . $i );
					}
					echo '</div>';
					$i++;
				}
				?>
			</div>
		<?php do_action( 'lightning_site_footer_content_append' ); ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'lightning_copyright_before' ); ?>

	<div class="container site-footer-copyright">
			<?php lightning_the_footer_copyight(); ?>
	</div>
</footer> 
