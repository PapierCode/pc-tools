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

/*=============================
=            Dates            =
=============================*/

$pc_months_list = array(
    'janvier',
    'février',
    'mars',
    'avril',
    'mai',
    'juin',
    'juillet',
    'août',
    'septembre',
    'octobre',
    'novembre',
    'décembre'
);


/*----------  Convertion bdd -> affichage  ----------*/

/**
 * 
 * @param string	$date_bdd	Date au format XXXx-XX-XX
 * 
 */

function pc_date_bdd_to_admin( $date_bdd ) {

	// si pas de date en paramètres, stop here !
	if ( !$date_bdd ) { return; }

	global $pc_months_list; // mois en FR

	$date 		= new DateTime( $date_bdd ); // création objet date avec la valeur en BDD

	$day 		= $date->format( 'd' ); // jour
	$month 		= $date->format( 'n' ); // mois
	$year 		= $date->format( 'Y' ); // année

	// retourne ex : 15 juin 2016
	return $day.' '.$pc_months_list[$month-1].' '.$year;

}


/*----------  Convertion affichage -> bdd   ----------*/

/**
 * 
 * @param string	$date_admin	Date au format 01 janvier 1977
 * 
 */

function pc_date_admin_to_bdd( $date_admin ) {

	// si pas de date en paramètres, stop here !
	if ( !$date_admin ) { return; }

	global $pc_months_list; // mois en FR

	$date_array = explode( ' ', $date_admin ); // valeur BDD retournée en tableau

	$month = array_search( $date_array[1], $pc_months_list ) + 1; // associe nom du mois au numéro de mois (index tableau)

	if ( strlen( $month ) < 2 ) { $month = '0' . $month;	} // préfixe avec 0 si numéro de mois < 10

	// retourne ex : 2016-06-15
	return $date_array[2] . '-' . $month . '-' . $date_array[0];

}


/*=====  FIN Dates  =====*/
