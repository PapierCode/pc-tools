<?php
/**
*
* [PC] Tools : exemple de création de custom post & taxonomie
*
* * Post
* * Taxonomy
* * Modifications Query 
*
**/


define('POST_EXAMPLE_SLUG', 'custompost');
define('TAX_EXAMPLE_SLUG', 'customposttax');

// si la class est disponible
if ( class_exists('PC_Add_Custom_Post') ) {

/*===================================
=            Custom Post            =
===================================*/

// cf. https://codex.wordpress.org/Function_Reference/register_post_type

/*----------  Labels  ----------*/

$customPostLabels = array (
    'name'                  => 'Custom Posts',
    'singular_name'         => 'Custom Post',
    'menu_name'             => 'Custom Posts',
    'add_new'               => 'Ajouter un Custom Post', 
    'add_new_item'          => 'Ajouter un Custom Post',
    'new_item'              => 'Ajouter un Custom Post',
    'edit_item'             => 'Modifier le Custom Post',
    'all_items'             => 'Tous les Custom Posts',
    'not_found'             => 'Aucun Custom Post',
    'featured_image'        => 'Visuel'
);


/*----------  Configuration  ----------*/

$customPostArgs = array(
    'menu_position'     => 98,
    'menu_icon'         => 'dashicons-no',
    'supports'          => array( 'title', 'author', 'thumbnail', 'editor', 'revisions' ),
    'show_in_nav_menus' => (wp_get_current_user()->roles[0] === 'administrator') ? true : false,
    'taxonomies'        => array( TAX_EXAMPLE_SLUG ),
    'rewrite'           => array( 'slug' => POST_EXAMPLE_SLUG )
);


/*----------  Déclaration  ----------*/

$customPost = new PC_Add_Custom_Post(
    POST_EXAMPLE_SLUG,  // obligatoire, nom du custom post, sans caractères spéciaux ni espaces ni majuscules
    $customPostLabels,  // obligatoire, textes d'interface
    $customPostArgs     // obligatoire, paramètres du custom post
);


/*=====  FIN Custom Post  ======*/

/*=======================================
=            Custom Taxonomy            =
=======================================*/

// cf. https://codex.wordpress.org/Function_Reference/register_taxonomy

/*----------  Labels  ----------*/

$customPostCustomTaxLabels = array(
    'name'                          => 'Custom Taxonomies',
    'singular_name'                 => 'Custom Taxonomy',
    'menu_name'                     => 'Custom Taxonomies',
    'all_items'                     => 'Toutes les Custom Taxonomies',
    'edit_item'                     => 'Modifier la Custom Taxonomy',
    'view_item'                     => 'Voir la Custom Taxonomy',
    'update_item'                   => 'Mettre à jour la Custom Taxonomy',
    'add_new_item'                  => 'Ajouter une Custom Taxonomy',
    'new_item_name'                 => 'Ajouter une Custom Taxonomy',
    'search_items'                  => 'Rechercher une Custom Taxonomy',
    'popular_items'                 => 'Custom Taxonomies les plus utilisées',
    'separate_items_with_commas'    => 'Séparer les catégories avec une virgule',
    'add_or_remove_items'           => 'Ajout/supprimer une Custom Taxonomy',
    'choose_from_most_used'         => 'Choisir parmis les plus utilisées',
    'not_found'                     => 'Aucune Custom Taxonomy définie'
);

/*----------  Paramètres  ----------*/

// vide = paramètres par défaut
$customPostCustomTaxArgs = array(
    'rewrite'   => array( 'slug' => POST_EXAMPLE_SLUG.'/'.sanitize_title($customPostCustomTaxLabels['name']) ),
    'show_in_nav_menus' => (wp_get_current_user()->roles[0] === 'administrator') ? true : false,
);


/*----------  Déclaration  ----------*/

$customPost->add_custom_tax(
    TAX_EXAMPLE_SLUG,               // obligatoire, nom de la taxonomie, sans caractères spéciaux ni espaces ni majuscules
    $customPostCustomTaxLabels,     // obligatoire, textes d'interface
    $customPostCustomTaxArgs        // obligatoire, paramètres de la taxonomie
);


/*=====  FIN Custom taxonomy  ======*/

} // FIN if class_exists

/*==========================================
=            Modification Query            =
==========================================*/

// add_action( 'pre_get_posts', 'example_pre_get_posts', 1 );

    function example_pre_get_posts( $query ) {

        global $eventQueryDefault;

        // si c'est l'admin ou si ce n'est pas la requête WP : on sort !
        if ( is_admin() || !$query->is_main_query() ) { return; }

        // si 
        // if (  ) { 

            // $query->set( '...', '...' );   

        //}

    }


/*=====  FIN Modification Query  ======*/