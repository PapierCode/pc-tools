<?php

/**
*
* [PC] Tools : exemple de création d'une page d'administration
*
*
*/

// si la class est disponible
if ( class_exists('PC_Add_Admin_Page') ) {

/*----------  Contenu  ----------*/

// pour le champ de type wysiwig
// cf. page 'PC réglages' généré par le widget '[PC] Custom WP'
$tinymceDefault = get_option( 'pc-settings-option' );

// champ custom
// la variable contient le html du champ
$customFieldContent = 'Champ customisé (ex. : liste de posts).';

// sections et champs associés
$customAdminContent = array(
    array(
        'title'     => 'Ensemble de champs n°1',                // obligatoire
        'id'        => 'section-a',                             // obligatoire
        'desc'      => '<p>Un contenu HTML libre.</p>',
        'prefix'    => 'prefix',                                // obligatoire
        'fields'    => array(
            array( // + callback sanitize
                'type'      => 'url',                           // obligatoire
                'label_for' => 'input-url',                     // obligatoire
                'label'     => 'Custom input url',              // obligatoire
                'desc'      => 'Aide ou description du champ',
                'required'  => false
            ),
            array( // + callback sanitize
                'type'      => 'date',                          // obligatoire
                'label_for' => 'input-date',                    // obligatoire
                'label'     => 'Custom input date',             // obligatoire
                'desc'      => 'Aide ou description du champ',
                'required'  => false
            ),
            array( // + callback sanitize
                'type'      => 'text',                          // obligatoire
                'label_for' => 'input-txt',                     // obligatoire
                'label'     => 'Custom input txts',             // obligatoire
                'desc'      => 'Aide ou description du champ',
                'attr'      => 'class="" data-attr=""',
                'css'       => 'width:100%;',
                'required'  => false
            ),
            array(
                'type'      => 'checkbox',                      // obligatoire
                'label_for' => 'input-checkbox',                // obligatoire
                'label'     => 'Custom input checkbox',         // obligatoire
                'desc'      => 'Aide ou description du champ',
                'attr'      => 'class="" data-attr=""'
            ),
            array(
                'type'      => 'radio',                         // obligatoire
                'label_for' => 'radio',                         // obligatoire
                'label'     => 'Custom radio',                  // obligatoire
                'desc'      => 'Aide ou description du champ',
                'attr'      => 'class="" data-attr=""',
                'required'  => false,
                'options'   => array(                           // obligatoire
                    'Valeur A' => 'a',
                    'Valeur B' => 'b',
                    'Valeur C' => 'c'
                )
            ),
            array(
                'type'      => 'select',                        // obligatoire
                'label_for' => 'select',                        // obligatoire
                'label'     => 'Custom select',                 // obligatoire
                'desc'      => 'Aide ou description du champ',
                'attr'      => 'class="" data-attr=""',
                'css'       => '',
                'required'  => false,
                'options'   => array(                           // obligatoire
                    'Valeur A' => 'a',
                    'Valeur B' => 'b',
                    'Valeur C' => 'c'
                )
            ),
            array(
                'type'      => 'img',                           // obligatoire
                'label_for' => 'img',                           // obligatoire
                'label'     => 'Custom image',                  // obligatoire
                'desc'      => 'Aide ou description du champ',
                'options'   => array(                           // obligatoire
                    'btnremove' => true
                )
            ),
            array(
                'type'      => 'gallery',                       // obligatoire
                'label_for' => 'gallery',                       // obligatoire
                'label'     => 'Custom gallerie',               // obligatoire
                'desc'      => 'Aide ou description du champ',
                'options'   => array(                           // obligatoire
                    'btnremove' => true
                )
            ),
            array(
                'type'      => 'pdf',                           // obligatoire
                'label_for' => 'pdf',                           // obligatoire
                'label'     => 'Custom pdf',                    // obligatoire
                'desc'      => 'Aide ou description du champ',
                'options'   => array(                           // obligatoire
                    'btnremove' => true
                )
            ),
            array(
                'type'      => 'custom',                        // obligatoire
                'label_for' => 'custom',                        // obligatoire
                'label'     => 'Custom',                        // obligatoire
                'desc'      => 'Aide ou description du champ',
                'display'   => $customFieldContent
            )
        )
    ),
    array(
        'title'     => 'Ensemble de champs n°2',                // obligatoire
        'id'        => 'section-b',                             // obligatoire
        'desc'      => '<p>Un contenu HTML libre.</p>',
        'prefix'    => 'prefix',                                // obligatoire
        'fields'    => array(
            array( // + callback sanitize
                'type'      => 'textarea',                      // obligatoire
                'label_for' => 'textarea',                      // obligatoire
                'label'     => 'Custom textarea',               // obligatoire
                'desc'      => 'Aide ou description du champ',
                'attr'      => 'class="" data-attr=""',
                'css'       => 'width:100%;'
            ),
            array(
                'type'      => 'wysiwyg',                       // obligatoire
                'label_for' => 'wysiwyg',                       // obligatoire
                'label'     => 'Custom wysiwyg',                // obligatoire
                'desc'      => 'Aide ou description du champ'
                /* 'options'   => array( // cf. plugins/pc-custom-wp/pc-custom-wp_settings
                    'media_buttons'                     => '', // true/false
                    'quicktags'                         => '', // true/false
                    'textarea_rows'                     => '', // number
                    'tinymce'                           => array (
                        'toolbar1'                          => '', // liste des boutons séparés par une virgule
                        'toolbar2'                          => '', // liste des boutons séparés par une virgule
                        'block_formats'                     => '', // liste des élements de type bloc à afficher dans le select "Formats"
                        'visualblocks_default_state'        => '', // true/false
                        'paste_as_text'                     => '' // true/false
                    )
                ) */
            )
        )
    )
);


/*----------  Création  ----------*/

// cf. https://developer.wordpress.org/reference/functions/add_menu_page/
// cf. https://developer.wordpress.org/reference/functions/add_submenu_page/
// cf. https://developer.wordpress.org/resource/dashicons/
// cf. https://codex.wordpress.org/Creating_Options_Pages

$customAdmin = new PC_Add_Admin_Page(
    'Custom Administration',    // obligatoire, titre de la page
	'',                         // obligatoire, slug page parent ou vide
    'Custom Administration',    // obligatoire, texte du menu
    'custom-admin',             // obligatoire, slug de la page, sans caractères spéciaux ni espaces ni majuscules
    $customAdminContent,        // obligatoire, contenu
    'editor',                   // droit d'accès 'editor'(defaut) ou 'admin'
    99,                         // position dans le menu (niveau 1), entre 0 et 99 (default)
    'dashicons-clipboard',      // icône dans le menu (niveau 1), par defaut 'dashicons-clipboard'
    'sanitize_example'          // fonction de traitement de données
);


/*----------  Traitement des données  ----------*/

function sanitize_example($datas) {

    // nettoyage des champs input libres
    $datas['prefix-input-txt'] = sanitize_text_field($datas['prefix-input-txt']);
    $datas['prefix-input-url'] = sanitize_text_field($datas['prefix-input-url']);
    $datas['prefix-input-date'] = sanitize_text_field(pc_date_admin_to_bdd($datas['prefix-input-date']));
    $datas['prefix-textarea'] = sanitize_text_field($datas['prefix-textarea']);

    return $datas;

}


} // FIN if class_exists
