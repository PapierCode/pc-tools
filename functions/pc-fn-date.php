<?php
/**
 * 
 * Fonctions pour les dates
 * 
 ** Convertion bdd -> affichage
 ** Convertion affichage -> bdd
 * 
 */

$pcMonthsList = array(
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

function pc_date_bdd_to_admin($dateFn) {

	// si pas de date en paramètres, stop here !
	if ( !$dateFn ) { return; }

	global $pcMonthsList; // mois en FR

	$date 		= new DateTime($dateFn); // création objet date avec la valeur en BDD

	$dayNumber 	= $date->format('d'); // jour
	$month 		= $date->format('n'); // mois
	$year 		= $date->format('Y'); // année

	// retourne ex : 15 juin 2016
	return $dayNumber.' '.$pcMonthsList[$month-1].' '.$year;

}

/*=====  FIN Convertion bdd -> affichage  =====*/

/*===================================================
=            Convertion affichage -> bdd            =
===================================================*/

function pc_date_admin_to_bdd($dateFn) {

	// si pas de date en paramètres, stop here !
	if ( !$dateFn ) { return; }

	global $pcMonthsList; // mois en FR

	$exploDate 	= explode(' ', $dateFn); // valeur BDD explosée en tableau

	$monthNum 	= array_search($exploDate[1], $pcMonthsList) + 1; // associe nom du mois au numéro de mois (index tableau)
	if (strlen($monthNum) < 2) { $monthNum = '0'.$monthNum;	} // préfixe avec 0 si numéro de mois < 10

	// retourne ex : 2016-06-15
	return $exploDate[2].'-'.$monthNum.'-'.$exploDate[0];

}


/*=====  FIN Convertion affichage -> bdd  =====*/