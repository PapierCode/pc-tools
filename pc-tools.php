<?php

/*
Plugin Name: [PC] Tools
Plugin URI: www.papier-code.fr
Description: Boite à outils Papier Codé
Version: 0.12.5
Author: Papier Codé
*/

/**
*
* * Includes
* * Textes
* * Traitements
* * Slug pour custom post
* * Pagination
* * SVG import
*
**/


/*================================
=            Includes            =
================================*/

/*----------  php classes  ----------*/

include 'class-add-custom-post.php';
include 'class-add-metabox.php';
include 'class-add-field-to-tax.php';
include 'class-add-custom-admin.php';
include 'class-add-recaptcha.php';


/*----------  JS & CSS  ----------*/

add_action( 'admin_enqueue_scripts', function () {

    // scripts pour admin
    wp_enqueue_script( 'tools-scripts', plugin_dir_url( __FILE__ ).'pc-tools-script.js' );

});


/*=====  End of Includes  ======*/

/*===================================
=            Traitements            =
===================================*/

/*----------  Limite du nombre de mots  ----------*/

function pc_words_limit($txt, $limit) {

    $stringLength = mb_strlen($txt,'utf-8');

    if ($stringLength > $limit) {

        $tempString = mb_substr($txt,0,$limit,'utf-8');
        $lastSpace = mb_strripos($tempString,' ',0,'utf-8');

        return mb_substr($tempString,0,$lastSpace,'utf-8').'...';

    } else {

        return $txt;

    }

}


/*----------  WP WYSIWYG  ----------*/

function pc_wp_wysiwyg($txt,$container = true) {

	$txt =	wpautop($txt);
	$txt =	do_shortcode($txt);

	if ( $container ) {
		return '<div class="editor">'.$txt.'</div>';
	} else {
		return $txt;
	}

}


/*----------  Date  ----------*/

$monthsList = array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');

// convertion date bdd -> affichage admin
function pc_date_bdd_to_admin($dateFn) {

	// si pas de date en paramètres, stop here !
	if ( !$dateFn ) { return; }

	global $monthsList; // mois en FR

	$date 		= new DateTime($dateFn); // création objet date avec la valeur en BDD

	$dayNumber 	= $date->format('d'); // jour
	$month 		= $date->format('n'); // mois
	$year 		= $date->format('Y'); // année

	// retourne ex : 15 juin 2016
	return $dayNumber.' '.$monthsList[$month-1].' '.$year;

}

// convertion date affichage -> bdd
function pc_date_admin_to_bdd($dateFn) {

	// si pas de date en paramètres, stop here !
	if ( !$dateFn ) { return; }

	global $monthsList; // mois en FR

	$exploDate 	= explode(' ', $dateFn); // valeur BDD explosée en tableau

	$monthNum 	= array_search($exploDate[1], $monthsList) + 1; // associe nom du mois au numéro de mois (index tableau)
	if (strlen($monthNum) < 2) { $monthNum = '0'.$monthNum;	} // préfixe avec 0 si numéro de mois < 10

	// retourne ex : 2016-06-15
	return $exploDate[2].'-'.$monthNum.'-'.$exploDate[0];

}


/*----------  Fusion de tableau multidimensionnel  ----------*/

function pc_array_multi_merge( $default, $new ) {

	// pour chaque entrée du nouveau tableau
    foreach ($new as $key => $value) {

        // si c'est un tableau imbriqué
        if ( is_array($new[$key]) ) {

        	// fusion des entrées
            $default[$key] = array_merge($default[$key],$new[$key]);

        } else {

        	// nouvelle entrée
            $default[$key] = $new[$key];

        }

    }

    return $default;

}


/*----------  Téléphone  ----------*/

function pc_phone($tel) {

	$tel = str_replace( ' ', '', $tel );
	$tel = '+33'.substr( $tel, 1, strlen($tel) );

	return $tel;

}


/*=====  End of Traitements  ======*/

/*===========================
=            Dev            =
===========================*/

/*----------  Afficher un tableau ou un objet  ----------*/

function pc_var($var, $margin = false) {

	$margin == true ? $style = 'style="margin-left:200px"' : $style = '';
	echo '<pre '.$style.'>'.print_r($var,true).'</pre>';

}


/*=====  FIN Dev  ======*/

/*==================================
=            Pagination            =
==================================*/

/*----------  pager  ----------*/

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
    if ( count($pagesList) > 0 ) {

	    $pager = '<ul class="pager pager-list reset-list">';
	    foreach ($pagesList as $page) {
	    	$page = str_replace($pagerOldClass, $pagerNewClass, $page);
	    	$pager .= '<li class="pager-item">'.$page.'</li>';
	    }
	    $pager .= '</ul>';

	    echo $pager;

	}

}


/*----------  navigation  ----------*/

// filtres pour changer les classes des liens
add_filter('next_post_link', 'post_link_attributes');
add_filter('previous_post_link', 'post_link_attributes');

	function post_link_attributes($datas) {
	    $class = 'class="the-class"';
	    return str_replace('<a href=', '<a '.$class.' href=', $datas);
	}

function pc_post_navigation($prevTxt = '<span>Article </span>Précédent', $nextTxt = '<span>Article </span>Suivant') {

	$pagination = '<ul class="pager pager-prevnext reset-list">';

	// construction du lien précédent
	$prevObject = get_previous_post();
	if( $prevObject != '' ) {

		$prevTitle 		= $prevObject->post_title;
		$prevUrl 		= get_permalink($prevObject->ID);
		$prevLink 		= '<a href="'.$prevUrl.'" class="pager-link pager-link-prev" title="'.$prevTitle.'"><span>'.$prevTxt.'</span></a>';
		$pagination 	.= '<li class="pager-item">'.$prevLink.'</li>';

	}

	// retour liste
	$pagination .= '<li class="pager-item"><a href="../" class="pager-link parger-link-back" title="Retour à la liste"><span>Retour</span></a></li>';

	// construction du lien suivant
	$nextObject = get_next_post();
	if( $nextObject != '' ) {

		$nextTitle 		= $nextObject->post_title;
		$nextUrl 		= get_permalink($nextObject->ID);
		$nextLink 		= '<a href="'.$nextUrl.'" class="pager-link pager-link-next" title="'.$nextTitle.'"><span>'.$nextTxt.'</span></a>';
		$pagination 	.= '<li class="pager-item">'.$nextLink.'</li>';

	}

	$pagination .= '</ul>';
	echo $pagination;

}


/*=====  FIN Pagination  ======*/

/*==================================
=            SVG import            =
==================================*/

function he_svg( $index, $color = false, $class = false, $hidden = true ) {

	global $sprite;
	$svg = $sprite[$index];

	// no print
	$svg = str_replace('<svg', '<svg class="no-print"', $svg);

	// couleur
	if ( $color ) {	$svg = str_replace('fill="#fff"', 'fill="'.$color.'"', $svg); }

	// aria hidden
	if ( $hidden ) { $svg = str_replace('<svg', '<svg aria-hidden="true"', $svg); }

	// aria hidden
	if ( $class ) { $svg = str_replace('<svg', '<svg class="'.$class.'"', $svg); }

	return $svg;

}


/*=====  FIN SVG import  ======*/
