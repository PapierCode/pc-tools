<?php
/**
 * 
 * Fonction utiles
 * 
 ** Afficher un tableau ou un objet
 ** Fusion de tableau multidimensionnel
 ** Nettoyage des champs d'admin
 * 
 */


/*=======================================================
=            Afficher un tableau ou un objet            =
=======================================================*/

/**
 * 
 * @param n/a		$var		Donnée à afficher : chaine de caractères, variable, tableau, object,...
 * @param boolean	$margin		Marge à gauche pour un affichage dans l'administration WP
 * 
 */

function pc_var( $var, $margin = false ) {

	$margin == true ? $style = 'style="margin-left:200px"' : $style = '';
	echo '<pre '.$style.'>'.print_r( $var,true ).'</pre>';

}


/*=====  FIN Afficher un tableau ou un objet  =====*/

/*===========================================================
=            Fusion de tableau multidimensionnel            =
===========================================================*/

/**
 * 
 * @param array		$default	Tableau à mettre à jour
 * @param array		$new		Tableau ajouté
 * 
 */

function pc_array_multi_merge( $default, $new ) {

	// pour chaque entrée du nouveau tableau
    foreach ( $new as $key => $value ) {

        // si c'est un tableau imbriqué
        if ( is_array( $new[$key] ) ) {

        	// fusion des entrées
            $default[$key] = array_merge( $default[$key], $new[$key] );

        } else {

        	// nouvelle entrée
            $default[$key] = $new[$key];

        }

    }

    return $default;

}


/*=====  FIN Fusion de tableau multidimensionnel  =====*/

/*====================================================
=            Nettoyage des champs d'admin            =
====================================================*/

/**
 * 
 * @param array		$settings_fields	Liste et configuration des champs
 * @param array		@datas				Données à traiter
 * 
 */

function pc_sanitize_settings_fields( $settings_fields, $datas ) {

    foreach ( $settings_fields as $set ) {

        $prefix = $set['prefix'];

        foreach ( $set['fields'] as $field ) {

            switch ( $field['type'] ) {

                case 'text':
                case 'date':
                    $datas[$prefix.'-'.$field['label_for']] = sanitize_text_field( $datas[$prefix.'-'.$field['label_for']] );
                    break;
                case 'textarea':
                    $datas[$prefix.'-'.$field['label_for']] = sanitize_textarea_field( $datas[$prefix.'-'.$field['label_for']] );
                    break;
                case 'email':
                    $datas[$prefix.'-'.$field['label_for']] = sanitize_email( $datas[$prefix.'-'.$field['label_for']] );
                    break;
                case 'url':
                    $datas[$prefix.'-'.$field['label_for']] = esc_url_raw( $datas[$prefix.'-'.$field['label_for']] );
                    break;

            }

        }

    }

    return $datas;

}


/*=====  FIN Nettoyage des champs de paramètres  =====*/