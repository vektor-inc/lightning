<?php $options = lightning_get_theme_options(); ?>
<div class="entry-meta">

<?php if ( get_post_type() != 'product' ) : ?>

<span class="entry-meta-item entry-meta-item-date">
<i class="far fa-calendar-alt"></i>
<span class="published"><?php echo esc_html( get_the_date() ); ?></span>
</span>

<span class="entry-meta-item entry-meta-item-updated">
<i class="fas fa-history"></i>
<span class="screen-reader-text"><?php _e( 'Last updated', 'lightning' ); ?> : </span>
<span class="updated"><?php the_modified_date( '' ); ?></span>
</span>

<?php
// Post author
// For post type where author does not exist
$author = get_the_author();
if ( $author ) {
	$meta_hidden_author = ( ! empty( $options['postAuthor_hidden'] ) ) ? ' entry-meta_hidden' : '';
	?>

<span class="entry-meta-item entry-meta-item-author<?php echo $meta_hidden_author; ?>">
<span class="vcard author" itemprop="author">

<span class="entry-meta-item-author-image">
<?php
	// VK Post Author Display の画像を取得
	$profile_image_id = get_the_author_meta( 'user_profile_image' );
	if ( $profile_image_id ) {
		$profile_image_src = wp_get_attachment_image_src( $profile_image_id, 'thumbnail' );
		echo'<img src="' . $profile_image_src[0] . '" alt="' . esc_attr( $author ) . '" />';
	} else {
		echo get_avatar( get_the_author_meta( 'email' ), 30 );
	}
?>

</span>
<span class="fn" itemprop="name"><?php echo esc_html( $author ); ?></span>
</span>
</span>

<?php } // if ( $author ) { ?>

<?php endif; // if ( get_post_type() != 'product' ) ?>

</div>
