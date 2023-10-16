<?php
/**
 * Page Header
 *
 * @package Lightning G3
 */

/*********************************************
 * Set Html Tag
 */
$post_top_info  = VK_Helpers::get_post_top_info();
$post_type_info = VK_Helpers::get_post_type_info();

// Use post top page（ Archive title wrap to div ）.
if ( $post_top_info['use'] ) {
	if ( is_category() || is_tag() || is_tax() || is_single() || is_date() ) {
		$page_title_tag = 'div';
	} else {
		$page_title_tag = 'h1';
	}
	// Don't use post top（　Archive title wrap to h1　）.
} else {
	if ( ! is_single() ) {
		$page_title_tag = 'h1';
	} else {
		$page_title_tag = 'div';
	}
}

/*********************************************
 * Set display title name
 */

$page_header_title = '';

if ( is_search() ) {
	if ( ! empty( get_search_query() ) ) {
		$search_text = sprintf( __( 'Search Results for : %s', 'lightning' ), get_search_query() );
	} else {
		$search_text = __( 'Search Results', 'lightning' );
	}
	$page_header_title = $search_text;
} elseif ( ! empty( $wp_query->query_vars['bbp_search'] ) ) {
	$bbp_search        = esc_html( urldecode( $wp_query->query_vars['bbp_search'] ) );
	$page_header_title = sprintf( __( 'Search Results for : %s', 'lightning' ), $bbp_search );
} elseif ( is_404() ) {
	$page_header_title = __( 'Not found', 'lightning' );
} elseif ( is_author() ) {
	$page_header_title = get_the_archive_title();
} elseif ( is_category() || is_tag() || is_tax() || is_home() || is_author() || is_archive() || is_single() ) {

	// Case of post type == 'post'.
	if ( 'post' === $post_type_info['slug'] ) {
		// Case of use post top page.
		if ( $post_top_info['use'] ) {
			$page_header_title = $post_top_info['name'];

			// Case of don't use post top page.
		} else {

			if ( is_single() ) {

				$taxonomies = get_the_taxonomies();
				if ( $taxonomies ) {
					$taxonomy_slug     = key( $taxonomies );
					$taxo_cates        = get_the_terms( get_the_ID(), $taxonomy_slug );
					$page_header_title = esc_html( $taxo_cates[0]->name );
				} else {
					// Case of no category.
					$page_header_title = $post_type_info['name'];
				}
			} else {
				$page_header_title = get_the_archive_title();
			}
		} // if ( $post_top_info['use'] ) {
		// Case of custom post type.
	} else {
		$page_header_title = $post_type_info['name'];
	}
} elseif ( is_page() || is_attachment() ) {
	$page_header_title = get_the_title();
}

$page_header_title_html = '<' . $page_title_tag . ' class="page-header-title">' . $page_header_title . '</' . $page_title_tag . '>';

$allowed_html = array(
	'h1'     => array(
		'class' => array(),
	),
	'div'    => array(
		'class' => array(),
	),
	'span'   => array(
		'class' => array(),
		'style' => array(),
	),
	'img'    => array(
		'class'   => array(),
		'src'     => array(),
		'alt'     => array(),
		'height'  => array(),
		'width'   => array(),
		'loading' => array(),
	),
	'i'      => array(
		'class' => array(),
	),
	'br'     => array(
		'class' => array(),
	),
	'strong' => array(),
	'ruby'   => array(),
	'rt'     => array(),
);
?>
<div class="page-header"><div class="page-header-inner container">
<?php
echo wp_kses( apply_filters( 'lightning_page_header_title_html', $page_header_title_html ), $allowed_html );
?>
</div></div><!-- [ /.page-header ] -->
