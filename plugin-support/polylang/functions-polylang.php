<?php
add_filter( 'vk_get_display_taxonomies_exclusion', 'lightning_exclude_term_list_custom' );
function lightning_exclude_term_list_custom( $exclusion ){
    $exclusion[] = 'language';
    $exclusion[] = 'post_translations';
    return $exclusion;
}