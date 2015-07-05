<?php get_header(); ?>
<div class="section siteContent">
<div class="container">
<div class="row">

<div class="col-md-8">
<main class="site-main" id="main" role="main">
    <?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
    <div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="entry-body">
    <?php the_content(); ?>
    </div>
	<?php
	$args = array(
		'before'           => '<nav class="page-link"><dl><dt>Pages :</dt><dd>',
		'after'            => '</dd></dl></nav>',
		'link_before'      => '<span class="page-numbers">',
		'link_after'       => '</span>',
		'echo'             => 1 );
	wp_link_pages( $args ); ?>
    </div><!-- [ /#post-<?php the_ID(); ?> ] -->

</main>
<?php endwhile; ?>
</div><!-- [ /.col-md-8 ] -->

<div class="col-md-3 col-md-offset-1 site-sub subSection">
<?php
if ( is_active_sidebar( 'common-side-top-widget-area' ) )
  dynamic_sidebar( 'common-side-top-widget-area' );

// Display post type widget area
$widdget_area_name = 'page-side-widget-area';
if ( is_active_sidebar( $widdget_area_name ) ){
  dynamic_sidebar( $widdget_area_name );
} else {

	if($post->ancestors){
		foreach($post->ancestors as $post_anc_id){
			$post_id = $post_anc_id;
		} // foreach($post->ancestors as $post_anc_id){
	} else {
		$post_id = $post->ID;
	} // if($post->ancestors){

	if ($post_id) {
		$children = wp_list_pages("title_li=&child_of=".$post_id."&echo=0");
		if ($children) { ?>
			<aside class="widget widget_archive">
			<nav class="localNav">
			<h1 class="subSection-title"><?php echo get_the_title($post_id); ?></h1>
			<ul>
			<?php echo $children; ?>
			</ul>
			</nav>
			</aside>
		<?php } // if ($children)
	} // if ($post_id)

} // if ( is_active_sidebar( $widdget_area_name ) ){

if ( is_active_sidebar( 'common-side-bottom-widget-area' ) )
  dynamic_sidebar( 'common-side-bottom-widget-area' );
?>
</div><!-- [ /.site-sub ] -->

</div><!-- [ /.row ] -->
</div><!-- [ /.container ] -->
</div><!-- [ /.siteContent ] -->
<?php get_footer(); ?>