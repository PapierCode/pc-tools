<?php
/**
 * 
 * Fonctions pour les textes à afficher
 * 
 ** Limite du nombre de caractères
 ** Traitement WYSIWYG
 ** Téléphone au format international
 * 
 */


/*======================================================
=            Limite du nombre de caractères            =
======================================================*/

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


/*=====  FIN Limite du nombre de caractères  =====*/

/*==========================================
=            Traitement WYSIWYG            =
==========================================*/

function pc_wp_wysiwyg($txt,$container = true) {

	$txt =	do_shortcode($txt);
	$txt =	wpautop($txt);

	if ( $container ) {
		return '<div class="editor">'.$txt.'</div>';
	} else {
		return $txt;
	}

}


/*=====  FIN Traitement WYSIWYG  =====*/

/*=========================================================
=            Téléphone au format international            =
=========================================================*/

function pc_phone($tel) {

	$tel = str_replace( ' ', '', $tel );
	$tel = '+33'.substr( $tel, 1, strlen($tel) );

	return $tel;

}


/*=====  FIN Téléphone au format international  =====*/