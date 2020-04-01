<?php
/**
 * 
 */


/*========================================
=            Get page par ...            =
========================================*/

/*----------  Template  ----------*/

function pc_get_page_by_template( $template_name, $type = 'url' ) {

    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => $template_name,
        'posts_per_page' => -1
    ) );

    switch ($type) {
        case 'url':
            return get_the_permalink( $page[0]->ID );
            break;
        case 'object':
            return $page[0];
            break;
    }

}

/*----------  Contenu spécifique  ----------*/

function pc_get_page_by_custom_content( $slug, $type = 'url' ) {

    $page = get_posts( array(
        'post_type' => 'page',
        'meta_key' => 'content-from',
        'meta_value' => $slug,
        'posts_per_page' => -1
	) );
	
    switch ($type) {
        case 'url':
            return get_the_permalink( $page[0]->ID );
            break;
        case 'object':
            return $page[0];
            break;
    }

}


/*=====  FIN Get page par template  =====*/
