<?php
add_filter( 'vk_exclude_term_list', 'lightning_exclude_term_list_custom' );
function lightning_exclude_term_list_custom( $exclusion ){
    $exclusion[] = 'language';
    $exclusion[] = 'post_translations';
    return $exclusion;
}