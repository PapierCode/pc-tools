<?php
/**
 * 
 * Fonctions rétro-compatibles
 * 
 */

/*=============================
=            Pager            =
=============================*/

function pc_post_pager($prevTxt = '<span>Page </span><span>précédente</span>', $nextTxt = '<span>Page </span><span>suivante</span>') {

    // globale WP
    global $wp_query;

    // page courante
    $wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;

    // configuration
    // cf. https://codex.wordpress.org/Function_Reference/paginate_links
    $pagination = array(
        'total' 				=> $wp_query->max_num_pages,						// nombre total de page
        'current' 				=> $current,										// index de la page courante
        'mid_size'				=> 0,												// nombre de liens autour de la page courante
        'prev_text' 			=> $prevTxt,										// contenu lien "page précédente"
        'next_text' 			=> $nextTxt,										// contenu lien "page suivante"
        'type' 					=> 'array',											// type de rendu (liste non ordonnée)
        'before_page_number' 	=> '<span class="visually-hidden">Page </span>'		// insertion avant le numéro de page
    );

    // tableau contenant chaque élément (lien et '...')
    $pagesList = paginate_links($pagination);
    // classes pour les liens
    $pagerOldClass = array('page-numbers', 'prev', 'current', 'dots', 'next');
    $pagerNewClass = array('pager-link', 'pager-link-prev', 'is-active', 'pager-dots', 'pager-link-next');

    // affichage
    if ( isset($pagesList) && count($pagesList) > 0 ) {

        $pager = '<ul class="pager pager-list reset-list">';
        foreach ($pagesList as $page) {
            $page = str_replace($pagerOldClass, $pagerNewClass, $page);
            $pager .= '<li class="pager-item">'.$page.'</li>';
        }
        $pager .= '</ul>';

        echo $pager;

    }

}


/*=====  FIN Page  =====*/
