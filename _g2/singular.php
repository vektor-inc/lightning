<?php lightning_get_template_part( 'header' ); ?>

<?php
if ( lightning_is_page_header() ) {
	// Dealing with old files.
	// Actually, it's ok to only use get_template_part().
	/*
	 Page Header
	/*-------------------------------------------*/
	$old_file_name   = array();
	$old_file_name[] = 'module_pageTit.php';
	if ( locate_template( $old_file_name, false, false ) ) {
		locate_template( $old_file_name, true, false );
	} else {
		get_template_part( 'template-parts/page-header' );
	}
}
?>

<?php do_action( 'lightning_breadcrumb_before' ); ?>

<?php
if ( lightning_is_breadcrumb() ) {
	/*
	 BreadCrumb
	/*-------------------------------------------*/
	$old_file_name   = array();
	$old_file_name[] = 'module_panList.php';
	if ( locate_template( $old_file_name, false, false ) ) {
		locate_template( $old_file_name, true, false );
	} else {
		get_template_part( 'template-parts/breadcrumb' );
	}
}
?>

<?php do_action( 'lightning_breadcrumb_after' ); ?>

<div class="<?php lightning_the_class_name( 'siteContent' ); ?>">
<?php do_action( 'lightning_siteContent_prepend' ); ?>
<div class="container">
<?php do_action( 'lightning_siteContent_container_prepend' ); ?>
<div class="row">

	<div class="<?php lightning_the_class_name( 'mainSection' ); ?>" id="main" role="main">
		<?php do_action( 'lightning_mainSection_prepend' ); ?>
		<?php
		$template = 'template-parts/post/content-' . esc_attr( $post->post_name ) . '.php';
		$return   = locate_template( $template );
		if ( $return && $post->post_name != get_post_type() ) {
			locate_template( $template, true );
		} else {
			get_template_part( 'template-parts/post/content', get_post_type() );
		}
		?>
		<?php do_action( 'lightning_mainSection_append' ); ?>
	</div><!-- [ /.mainSection ] -->

	<?php if ( lightning_is_subsection_display() ) : ?>
		<div class="<?php lightning_the_class_name( 'sideSection' ); ?>">
			<?php do_action( 'lightning_sideSection_prepend' ); ?>
			<?php lightning_get_template_part( 'sidebar', get_post_type() ); ?>
			<?php do_action( 'lightning_sideSection_append' ); ?>
		</div><!-- [ /.subSection ] -->
	<?php endif; ?>

<?php do_action( 'lightning_additional_section' ); ?>

</div><!-- [ /.row ] -->
<?php do_action( 'lightning_siteContent_container_apepend' ); ?>
</div><!-- [ /.container ] -->
<?php do_action( 'lightning_siteContent_apepend' ); ?>
</div><!-- [ /.siteContent ] -->

<?php lightning_get_template_part( 'footer' ); ?>
