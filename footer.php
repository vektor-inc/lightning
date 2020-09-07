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

<footer class="<?php lightning_the_class_name( 'siteFooter' ); ?>">
	<?php if ( has_nav_menu( 'Footer' ) ) : ?>
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
	<?php endif; ?>
	<?php
	$footer_widget_area_count = 3;
	$footer_widget_area_count = apply_filters( 'lightning_footer_widget_area_count', $footer_widget_area_count );
	$footer_widget_exists = false;
	for ( $i = 1; $i <= $footer_widget_area_count; $i++ ) {
		if ( is_active_sidebar( 'footer-widget-' . $i ) ) {
			$footer_widget_exists = true;
		}
	}
	?>
	<?php if ( true === $footer_widget_exists ) : ?>
		<div class="container sectionBox footerWidget">
			<div class="row">
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
					// Use 6 widget area
					6 => array( 'class' => 'col-md-2' ),
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
		</div>
	<?php endif; ?>

	<?php do_action( 'lightning_copySection_before' ); ?>

	<div class="container sectionBox copySection text-center">
			<?php lightning_the_footerCopyRight(); ?>
	</div>
</footer>
<?php do_action( 'lightning_footer_after' ); ?>
<?php wp_footer(); ?>
</body>
</html>
