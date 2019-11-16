<?php
/**********************************************/
// Load modules
/**********************************************/

/*
Setting of term color target.
*/
add_filter( 'term_color_taxonomies_custom', 'lightning_term_color_taxonomies_custom' );
function lightning_term_color_taxonomies_custom( $taxonomies ) {
	// Get taxonomies
	$args           = array( 'show_ui' => true );
	$get_taxonomies = get_taxonomies( $args );
	// term color を有効化する taxonomy の配列に追加
	foreach ( $get_taxonomies as $key => $value ) {
			$taxonomies[] = $value;
	}
	return $taxonomies;
}

	/*
	Reason of use after_setup_theme that,
	don't work call by theme custom post type's custom taxonomy
	★★★★★★ 関数のprefixは固有のものに変更する事 ★★★★★★
	*/

add_action( 'after_setup_theme', 'lightning_load_term_color' );
function lightning_load_term_color() {
	require_once 'package/class.term-color.php';
}
