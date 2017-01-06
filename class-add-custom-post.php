<?php

/**
*
* [PC] Tools : création d'un custom post
*
**/


class PC_Add_Custom_Post {

    public $postType;
    public $postTypeSlug;
    public $postTypeArgs;
    public $postTypeLabels;


    /*====================================
    =            Constructeur            =
    ====================================*/

    /*
    *
    * * [string] $slug      : identifiant du custom
    * * [array]  $labels    : textes de l'interface
    * * [array]  $arg       : paramètres du custom post
    *
    * cf. https://codex.wordpress.org/Function_Reference/register_post_type
    *
    */

    public function __construct( $postType, $labels = array(), $args = array() ) {

        /*----------  Variables de la class  ----------*/
        
        $this->postType         = $postType;
        $this->postTypeLabels   = $labels;
        $this->postTypeArgs     = $args;

        $this->postTypeSlug     = $this->get_slug();
         

        /*----------  Création  ----------*/
        
        if ( !post_type_exists( $this->postType ) ) { 

            add_action( 'init', array( $this, 'add_custom_post' ) ); 

        } // FIN if !post_type_exists

    }


    /*=====  FIN Constructeur  ======*/

    /*===================================
    =            Custom Post            =
    ===================================*/
    
    public function add_custom_post() {

        /*----------  Fusion des textes & paramètres avec les valeurs par défaut  ----------*/
        
        // textes de l'interface
        $labels = array_merge(       
            // défaut
            array(
                'name'                  => 'Articles',
                'singular_name'         => 'article',
                'menu_name'             => 'Articles',
                'add_new'               => 'Ajouter un article', 
                'add_new_item'          => 'Ajouter un article',
                'new_item'              => 'Ajouter un article',
                'edit_item'             => 'Modifier l\'article',
                'all_items'             => 'Tous les articles',
                'not_found'             => 'Aucun article trouvé',
                'not_found_in_trash'    => 'La corbeille est vide',
                'featured_image'        => 'Visuel',
                'set_featured_image'    => 'Ajouter un visuel',
                'remove_featured_image' => 'Supprimer le visuel'
            ),           
            // labels passés lors de la création
            $this->postTypeLabels            
        );

        // configuration du custom post
        $args = array_merge(         
            // défaut
            array(
                'labels'            => $labels,
                'menu_position'     => 99,
                'menu_icon'         => 'dashicons-feedback',
                'supports'          => array( 'title' ),
                'rewrite'           => array( 'slug' => $this->postTypeSlug ),
                'public'            => true,
                'has_archive'       => true,
                'taxonomies'        => array()
            ),           
            // arguments passés lors de la création
            $this->postTypeArgs          
        );


        /*----------  Création  ----------*/
        
        register_post_type( $this->postType, $args );

        
        /*----------  Thumbnail  ----------*/

        // si demandé
        if ( in_array( 'thumbnail', $args['supports'] ) ) { add_theme_support( 'post-thumbnails', array( $this->postType ) ); }    


        /*----------  Désactivation metabox identifiant  ----------*/

        add_action( 'admin_menu', function() { remove_meta_box( 'slugdiv', $this->postType, 'normal' ); } ); 


    } // FIN add_custom_post()
    

    /*=====  FIN Custom Post  ======*/

    /*=================================
    =            Taxonomie            =
    =================================*/ 

    /*
    *
    * * [string] $name      : nom de la taxonomie
    * * [array]  $labels    : textes de l'interface
    * * [array]  $arg       : paramètre de la taxonomie
    *
    * cf. https://codex.wordpress.org/Function_Reference/register_taxonomy
    *
    */
    
    public function add_custom_tax( $name, $labels = array(), $args = array() ) {

        if ( !empty( $name ) ) {
                 
            /*----------  Fusion des textes & paramètres avec les valeurs par défaut  ----------*/

            // variables
            $taxName        = $name;
            $taxLabels      = $labels;
            $taxArgs        = $args;

            // textes de l'interface
            $args = array_merge(
                // défaut
                array(
                    'name'                          => 'Customs posts',
                    'singular_name'                 => 'Custom post',
                    'menu_name'                     => 'Types de CT',
                    'all_items'                     => 'Toutes les CT',
                    'edit_item'                     => 'Modifier la CT',
                    'view_item'                     => 'Voir la CT',
                    'update_item'                   => 'Mettre à jour la CT',
                    'add_new_item'                  => 'Ajouter une CT',
                    'new_item_name'                 => 'Nouvelle CT',
                    'search_items'                  => 'Rechercher une CT',
                    'popular_items'                 => 'CT les plus utilisés',
                    'separate_items_with_commas'    => 'Séparer les CT avec une virgule',
                    'add_or_remove_items'           => 'Ajout/supprimer une CT',
                    'choose_from_most_used'         => 'Choisir parmis les plus utilisés',
                    'not_found'                     => 'Aucune CT définie'
                ),
                // arguments passés lors de la déclaration
                $taxLabels
            );

            // configuration de la tax
            $args = array_merge(
                // défaut
                array(
                    'show_admin_column' => true,
                    'labels'            => $args,
                    'hierarchical'      => true,                        
                    'query_var'         => true,
                    'rewrite'           => array( 'slug' => $this->postTypeSlug.'/'.sanitize_title($args['name']) ),
                    'show_in_nav_menus' => false
                ),
                // arguments passés lors de la déclaration
                $taxArgs
            );


            /*----------  Création/ajout  ----------*/
            
            add_action( 'init', function() use( $taxName, $args ) {

                // si la tax n'existe pas
                if ( !taxonomy_exists( $taxName ) ) {

                    register_taxonomy( $taxName, $this->postType, $args );

                }
                // si la tax existe
                else {

                    // = tax partagée
                    register_taxonomy_for_object_type( $taxName, $this->postType );         

                }

            }, 0 );

        } // FIN if(!name)


    } // FIN add_custom_tax()

    
    /*=====  FIN Taxonomie  ======*/

    /*=============================================
    =            Slug pour custom post            =
    =============================================*/

    /*
    *
    * Génération d'un slug pour le custom post en fonction du menu de navigation
    *
    */

    public function get_slug() {

        // réglages projet où sera sauvegardé le slug
        $pcSettings = get_option( 'pc-settings-option' );
        // class WP pour les requêtes sql
        global $wpdb;
        // slug final
        $postSlug = '';
        // identifiant de l'option enrgistrée en base
        $postSlugId = $this->postType.'Slug';

        // recherche des items de menu qui publient des archives
        $results = $wpdb->get_results( 'SELECT post_id FROM '.$wpdb->prefix.'postmeta WHERE meta_value = "post_type_archive"' );

        // pour chaque item
        foreach ($results as $result) {

            // propriété de l'item
            $item = get_post_meta($result->post_id);
            // si l'item publie les customs recherché
            if ( $item['_menu_item_object'][0] == $this->postType ) {

                // formate son titre pour en faire le slug
                $postSlug = sanitize_title( get_the_title($result->post_id) );
                
                // si l'item a un parent
                if ( $item['_menu_item_menu_item_parent'][0] != 0 ) {

                    // le titre de l'item parent lui est associé 
                    if ( get_the_title($item['_menu_item_menu_item_parent'][0]) != '' ) {

                        $postSlug = sanitize_title(get_the_title($item['_menu_item_menu_item_parent'][0])).'/'.$postSlug;

                    // le titre de l'item parent est emprunté au contenu associé
                    } else {

                        $itemParent = get_post_meta($item['_menu_item_menu_item_parent'][0]);
                        $postSlug = sanitize_title(get_the_title($itemParent['_menu_item_object_id'][0])).'/'.$postSlug;

                    }
                    
                } // FIN if l'item a un parent

            } // FIN if $itemMenu['_menu_item_object']

        } // FIN foreach item

        // si slug vide
        if ( $postSlug == '' ) { $postSlug = sanitize_title($this->postTypeLabels['name']); }

        // le slug est enregistré en base
        // si c'est la première fois ou si il est différent
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
    
}