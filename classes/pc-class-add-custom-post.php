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
                'public'            => true,
                'has_archive'       => true,
                'taxonomies'        => array(),
				'active_slugdiv'	=> false
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

		if ( !$args['active_slugdiv'] ) {
	        add_action( 'admin_menu', function() { remove_meta_box( 'slugdiv', $this->postType, 'normal' ); } ); 
		}


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
    
}