<?php
/**
 * 
 * Fonctions pour les dates
 * 
 ** Convertion bdd -> affichage
 ** Convertion affichage -> bdd
 * 
 */

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


/*===================================================
=            Convertion bdd -> affichage            =
===================================================*/

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

/*=====  FIN Convertion bdd -> affichage  =====*/

/*===================================================
=            Convertion affichage -> bdd            =
===================================================*/

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


/*=====  FIN Convertion affichage -> bdd  =====*/