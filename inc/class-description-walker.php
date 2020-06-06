<?php
/**
 * Description Walker of Lightning
 *
 * @package Lightning
 */

/**
 * Walker_Nav_Menu start_el
 */
class Description_Walker extends Walker_Nav_Menu {
	/**
	 * Undocumented function
	 *
	 * @param string  $output Used to append additional content (passed by reference).
	 * @param WP_Post $item   Menu item data object.
	 * @param integer $depth  Depth of menu item. Used for padding.
	 * @param array   $args   An object of wp_nav_menu() arguments.
	 * @param integer $id     Current item ID.
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		global $wp_query;
		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = '';
		$value       = '';
		$classes     = empty( $item->classes ) ? array() : (array) $item->classes;

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) );
		$class_names = ' class="' . esc_attr( $class_names ) . '"';
		$output     .= $indent . '<li id="menu-item-' . $item->ID . '"' . $value . $class_names . '>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
		$attributes .= ! empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
		$attributes .= ! empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
		$attributes .= ! empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';

		$prepend     = '<strong class="gMenu_name">';
		$append      = '</strong>';
		$description = ! empty( $item->description ) ? '<span class="gMenu_description">' . esc_attr( $item->description ) . '</span>' : '';

		if ( 0 !== $depth ) {
			$description = '';
			$append      = '';
			$prepend     = '';
		}

		$item_output  = $args->before;
		$item_output .= '<a' . $attributes . '>';
		$item_output .= $args->link_before . $prepend . apply_filters( 'the_title', $item->title, $item->ID ) . $append;
		$item_output .= $description . $args->link_after;
		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
