<?php

/*
Plugin Name: [PC] Tools
Plugin URI: www.papier-code.fr
Description: Boite à outils Papier Codé
Version: 1.1.0
Author: Papier Codé
*/

// 02/12/16 : ajout fonctions pagination
// 28/11/16 : pc_wp_wysiwyg() echo remplace return
// 07/09/16 : class en include
// 07/09/16 : suppression de pc_save_custom_post_field() & pc_save_taxonomy_field()
// 26/08/16 : admin fields, ajout argument css (input & textearea) & lines (textarea)
// 23/08/16 : Ajout pc_clean_txt() => 1.1.0
// 22/08/16 : Création

/**
* 
* * Includes
* * Textes
* * Traitements
* * Pagination
*
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

?>