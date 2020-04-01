<?php
/**
 * 
 * Fonctions pour les textes à afficher
 * 
 ** Limite du nombre de caractères
 ** Traitement WYSIWYG
 ** Téléphone au format international
 ** Message
 * 
 */


/*======================================================
=            Limite du nombre de caractères            =
======================================================*/

function pc_words_limit( $txt, $limit ) {

    $length = mb_strlen($txt,'utf-8');

    if ($length > $limit) {

        $temp = mb_substr( $txt,0, $limit, 'utf-8' );
        $last_space = mb_strripos( $temp, ' ', 0, 'utf-8' );

        return mb_substr( $temp, 0, $last_space, 'utf-8') . '...';

    } else {

        return $txt;

    }

}


/*=====  FIN Limite du nombre de caractères  =====*/

/*==========================================
=            Traitement WYSIWYG            =
==========================================*/

function pc_wp_wysiwyg( $txt, $container = true ) {

	$txt =	do_shortcode( $txt );
	$txt =	wpautop( $txt );

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

function pc_phone( $tel ) {

	$tel = str_replace( ' ', '', $tel );
	$tel = '+33' . substr( $tel, 1, strlen($tel) );

	return $tel;

}


/*=====  FIN Téléphone au format international  =====*/

/*===============================
=            Message            =
===============================*/

function pc_display_alert_msg( $msg, $type = '', $format = '', $elt = 'p' ) {

	// defaut
	$css = 'msg';
	// type block
	$css .= ( $format == 'block' ) ? ' msg--block' : '';
	// erreur ou succès
	if ( $type == 'error' ) { $css .= ' msg--error'; }
	else if ( $type == 'success' ) { $css .= ' msg--success'; }

	// affichage
	$return = '<'.$elt.' class="'.$css.'">';
	$return .= '<span class="msg-ico">'.pc_svg( 'msg', '', 'svg-block' ).'</span>';
	$return .= '<span class="msg-txt">'.$msg.'</span>';
	$return .= '</'.$elt.'>';

	return $return;

}


/*=====  FIN Message  =====*/