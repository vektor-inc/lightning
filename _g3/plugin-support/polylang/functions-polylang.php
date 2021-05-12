<?php
function lightning_exclude_term_list_polylang( $exclusion ){
    $exclusion[] = 'language';
    $exclusion[] = 'post_translations';
    return $exclusion;
}
add_filter( 'vk_get_display_taxonomies_exclusion', 'lightning_exclude_term_list_polylang' );
add_filter( 'vk_breadcrumb_taxonomies_exludion', 'lightning_exclude_term_list_polylang' );