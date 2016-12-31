<?php

/*
Plugin Name: [PC] Tools
Plugin URI: www.papier-code.fr
Description: Boite à outils Papier Codé
Version: 1.1.0
Author: Papier Codé
*/

/**
* 
* * Includes
* * Textes
* * Traitements
* * Slug pour custom post
* * Pagination
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


/*----------  JS & CSS  ----------*/

add_action( 'admin_enqueue_scripts', function () {

    // scripts pour admin
    wp_enqueue_script( 'tools-scripts', plugin_dir_url( __FILE__ ).'pc-tools-script.js' );

});


/*=====  End of Includes  ======*/

/*==============================
=            Textes            =
==============================*/

/*----------  Blabla  ----------*/

function pc_txt($quoi) {

	switch ($quoi) {

		case 'seoIntro':
			return '<p>Ces deux champs sont utiles au référencement et s\'affichent dans les résultats des moteurs de recherche, par exemple dans Google : le <em>Titre</em> correspond à la ligne de texte bleue, la <em>Description</em> aux 2 lignes en noir en dessous. <strong>Nombre de signes maximum conseillés : respectivement 70 et 200.</strong></p>';
			break;
		
		default:
			return;
			break;

	}

}


/*=====  End of Textes  ======*/

/*===================================
=            Traitements            =
===================================*/

/*----------  Nettoyage de texte  ----------*/

function pc_clean_txt($txt) {

	$txt = strip_tags($txt);						// Supprime les balises HTML et PHP d'une chaîne
	$txt = preg_replace('#\n|\t|\r#',' ',$txt);		// Supprime les sauts de ligne et paragraphe
	$txt = htmlspecialchars($txt);					// Convertit les caractère spéciaux en HTML
	$txt = preg_replace('#«|»#','&quot;',$txt);		// Convertit les guillemets en HTML

	return $txt;

}


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

function pc_wp_wysiwyg($txt) {

	$txt =	wpautop($txt);
	$txt =	do_shortcode($txt);

	echo $txt;

}

/*----------  Date  ----------*/

$monthsList		= array('janvier', 'février', 'mars', 'avril', 'mai', 'juin', 'juillet', 'août', 'septembre', 'octobre', 'novembre', 'décembre');

// convertion date bdd -> affichage admin
function pc_date_bdd_to_admin($dateFn) {

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

	global $monthsList; // mois en FR

	$exploDate 	= explode(' ', $dateFn); // valeur BDD explosée en tableau

	$monthNum 	= array_search($exploDate[1], $monthsList) + 1; // associe nom du mois au numéro de mois (index tableau)
	if (strlen($monthNum) < 2) { $monthNum = '0'.$monthNum;	} // préfixe avec 0 si numéro de mois < 10

	// retourne ex : 2016-06-15
	return $exploDate[2].'-'.$monthNum.'-'.$exploDate[0];

}


/*=====  End of Traitements  ======*/

/*===========================
=            Dev            =
===========================*/

/*----------  Afficher un tableau ou un objet  ----------*/

function pc_display_var($var, $margin = false) {

	$margin == true ? $style = 'style="margin-left:200px"' : $style = '';
	echo '<pre '.$style.'>'.print_r($var,true).'</pre>';

}


/*=====  FIN Dev  ======*/

/*=============================================
=            Slug pour custom post            =
=============================================*/

/*
*
* Génération d'un slug pour custom post en fonction du menu de navigation
*
* * $custom : nom du custom post déclaré avec la class PC_Add_Custom_Post
* * $level 	: nombre de niveaux à prendre en compte (1 ou 2) 
* * $alt 	: alternative si aucune correspondance dans le menu
*
*/

function pc_get_slug_for_custom_post( $custom, $level, $alt ) {

	// Options Papier Codé où sera sauvegardé le slug
	$pcSettings = get_option( 'pc-settings-option' );
	// class WP pour les requêtes sql
	global $wpdb;
	// slug final
	$postSlug = '';
	// identifiant de l'option enrgistrée en base
	$postSlugId = $postSlug.'Slug';

	// recherche des items de menu qui publient des archives
	$results = $wpdb->get_results( 'SELECT post_id FROM preform_postmeta WHERE meta_value = "post_type_archive"' );

	// pour chaque item
	foreach ($results as $result) {

		// propriété de l'item
		$itemMenu = get_post_meta($result->post_id);
		// si l'item publie les customs recherché
		if ( $itemMenu['_menu_item_object'][0] == $custom ) {

			// formate son titre pour en faire le slug
			$postSlug = sanitize_title( get_the_title($result->post_id) );
			
			// si l'item a un parent
			if ( $level == 2 && $itemMenu['_menu_item_menu_item_parent'][0] != 0 ) {

				// ajoute le nom du parent formaté en slug
				$postSlug = sanitize_title(get_the_title($itemMenu['_menu_item_menu_item_parent'][0])).'/'.$postSlug;
				
			} // FIN if $level = 2 & l'item a un parent

		} else { 

			// pas de publication dans le menu de navigation
			// utilisation de l'alternative
			$postSlug = $alt;

		} // FIN if $itemMenu['_menu_item_object']

	} // FIN foreach item

	// le slug est enregistré en base
	// si c'est la première fois ou si le nouveau slug est différent
	if ( !isset($pcSettings[$postSlugId]) || $pcSettings[$postSlugId] != $postSlug ) {

		// ajoute ou modifie la valeur dans le tableau créé en début de ce fichier
		$pcSettings[$postSlugId] = $postSlug;
		// mise à jour de la base
		update_option( 'pc-settings-option', $pcSettings ,'', 'no');
		// regénération des permaliens
		flush_rewrite_rules();

	} // FIN test/comparaison du slug en Base

	return $postSlug;
	
} // FIN get_slug_for_custom_post()

/*=====  FIN Slug pour custom post  ======*/

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
        'total' 				=> $query->max_num_pages,						// nombre total de page
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