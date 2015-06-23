
<div class="entry-meta">
<span class="published entry-meta-items"><i class="fa fa-calendar"></i><?php echo esc_html( get_the_date() ); ?></span>

<?php // Post update ?>

<?php if ( is_single() ) : ?>
<?php global $lightning_theme_options;
	if ( 
		!isset($lightning_theme_options['postUpdate_hidden']) or 
		( isset($lightning_theme_options['postUpdate_hidden']) && !$lightning_theme_options['postUpdate_hidden'] )
	) : ?>
	<span class="updated entry-meta-items">/ <?php _e('Last updated','lightning'); ?> : <?php the_modified_date('') ?></span>
	<?php endif; ?>

<?php
// Post author
if ( 
	!isset($lightning_theme_options['postAuthor_hidden']) or 
	( isset($lightning_theme_options['postAuthor_hidden']) && !$lightning_theme_options['postAuthor_hidden'] )
) : ?>
<span class="vcard author entry-meta-items"><span class="fn"><i class="fa fa-pencil"></i><?php echo esc_html(get_the_author_meta( 'display_name' ));?></span></span>
<?php endif; ?>

<?php endif; // is_archive() ?>

<?php
$page_for_posts = lightning_get_page_for_posts();
	$taxonomies = get_the_taxonomies();
	if ($taxonomies):
		// get $taxonomy name
		$taxonomy = key( $taxonomies );
		$terms  = get_the_terms( get_the_ID(),$taxonomy );
		$term_url	= esc_url(get_term_link( $terms[0]->term_id,$taxonomy));
		$term_name	= esc_html($terms[0]->name);
		echo '<span class="entry-meta-items entry-meta-items-term"><a href="'.$term_url.'" class="btn btn-xs btn-primary">'.$term_name.'</a></span>';
	endif;
?>

</div>