<?php

/**
*
* [PC] Tools : exemple d'ajout de champ à une taxonomie
*
*
*/

// si la class est disponible
if ( class_exists('PC_add_field_to_tax') ) {

/*----------  Contenu  ----------*/

// pour le champ de type wysiwig
// cf. page 'PC réglages' généré par le widget '[PC] Custom WP'
$tinymceDefault = get_option( 'pc-settings-option' );

// section et liste des champs
$fieldToTaxContent = array(	
    'title'     => 'Ensemble de champs',                    // obligatoire 
    'desc'      => '<p>Un contenu HTML libre.</p>',
    'prefix'    => 'prefix',                                // obligatoire
    'fields'    => array(
        array(
            'type'      => 'text',                          // obligatoire
            'id'        => 'input-txt',                     // obligatoire
            'label'     => 'Custom input txt',              // obligatoire
            'desc'      => 'Aide ou description du champ',
            'attr'      => 'class="" data-attr=""',
            'css'       => 'width:100%;',
            'required'  => false
        ),
        array(
            'type'      => 'number',                        // obligatoire
            'id'        => 'input-txt',                     // obligatoire
            'label'     => 'Custom input txt',              // obligatoire
            'desc'      => 'Aide ou description du champ',
            'attr'      => 'class="" data-attr=""',
            'css'       => 'width:100%;',
            'required'  => false
        ),
        array(
            'type'      => 'textarea',                      // obligatoire
            'id'        => 'textarea',                      // obligatoire
            'label'     => 'Custom textarea',               // obligatoire
            'desc'      => 'Aide ou description du champ',
            'attr'      => 'class="" data-attr=""',
            'css'       => 'width:100%;',
            'required'  => false
        ),
        array(
            'type'      => 'checkbox',                      // obligatoire
            'id'        => 'checkbox',                      // obligatoire
            'label'     => 'Custom checkbox',               // obligatoire
            'desc'      => 'Aide ou description du champ',
            'attr'      => 'class="" data-attr=""',
            'css'       => ''
        ),
        array(
            'type'      => 'radio',                         // obligatoire
            'id'        => 'radio',                         // obligatoire
            'label'     => 'Custom radio',                  // obligatoire
            'desc'      => 'Aide ou description du champ',
            'attr'      => 'class="" data-attr=""',
            'css'       => '',
            'required'  => true,
            'options'   => array(                           // obligatoire
                'Valeur A' => 'a',
                'Valeur B' => 'b',
                'Valeur C' => 'c'
            )
        ),
        array(
            'type'      => 'select',                        // obligatoire
            'id'        => 'select',                        // obligatoire
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
            'type'      => 'color',                        // obligatoire
            'id'        => 'color',                        // obligatoire
            'label'     => 'Custom color',                 // obligatoire
            'desc'      => 'Aide ou description du champ',
            'attr'      => 'class="" data-attr=""',
            'css'       => '',
            'required'  => false,
            'options'   => array(                           // obligatoire
                'Vert' => 'green',
                'Orange' => 'orange',
                'Gris' => '#ccc'
            )
        ),
        array(
            'type'      => 'wysiwyg',                       // obligatoire
            'id' 		=> 'wysiwyg',                       // obligatoire
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
        ),
        array(
            'type'      => 'img',                      // obligatoire
            'id'        => 'img',                      // obligatoire
            'label'     => 'Image',                    // obligatoire
            'desc'      => 'Aide ou description du champ',
            'options'   => array(                      // obligatoire
                'btnremove' => true
            )
        )
    )
);


/*----------  Création  ----------*/

$fieldToTax = new PC_add_field_to_tax(
    TAX_EXAMPLE_SLUG,   // obligatoire, taxonomie concernée
    $fieldToTaxContent  // obligatoire
);


} // FIN if class_exists