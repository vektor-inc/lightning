<?php if ( is_active_sidebar( 'footer-upper-widget-1' ) ) : ?>
<div class="section sectionBox siteContent_after">
	<div class="container ">
		<div class="row ">
			<div class="col-md-12 ">
			<?php dynamic_sidebar( 'footer-upper-widget-1' ); ?>
			</div>
		</div>
	</div>
</div>
<?php endif; ?>

<?php do_action( 'lightning_footer_before' ); ?>

<footer class="section siteFooter">
	<div class="footerMenu">
	   <div class="container">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'Footer',
					'container'      => 'nav',
					'items_wrap'     => '<ul id="%1$s" class="%2$s nav">%3$s</ul>',
					'fallback_cb'    => '',
					'depth'          => 1,
				)
			);
			?>
		</div>
	</div>
	<div class="container sectionBox">
		<div class="row ">
			<?php
			// Area setting
			$footer_widget_area = array(
				// Use 1 widget area
				1 => array( 'class' => 'col-md-12' ),
				// Use 2 widget area
				2 => array( 'class' => 'col-md-6' ),
				// Use 3 widget area
				3 => array( 'class' => 'col-md-4' ),
				// Use 4 widget area
				4 => array( 'class' => 'col-md-3' ),
			);

			$footer_widget_area_count = 3;
			$footer_widget_area_count = apply_filters( 'lightning_footer_widget_area_count', $footer_widget_area_count );

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
	</div>

	<?php do_action( 'lightning_copySection_before' ); ?>

	<div class="container sectionBox copySection text-center">
			<?php lightning_the_footerCopyRight(); ?>
	</div>
</footer>
<?php do_action( 'lightning_footer_after' ); ?>
<?php wp_footer(); ?>
</body>
</html>
