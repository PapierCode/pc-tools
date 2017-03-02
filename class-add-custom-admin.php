<?php

/**
*
* [PC] Tools : création d'une page d'administration
*
**/


class PC_Add_Admin_Page {

	public $title; 
	public $slug; 
	public $optionName;
	public $content; 
	public $capability; 


    /*====================================
    =            Constructeur            =
    ====================================*/

    /*
    *
    * * [string] 	$title      	: titre de la page
    * * [string] 	$parent    		: slug de la page parent ou vide si menu de premier niveau
    * * [string] 	$menuLabel  	: texte du menu
    * * [string] 	$slug    		: slug de la page, sans caractères spéciaux ni majuscules, tiret pour séparer les mots
    * * [array] 	$content    	: sections et champs
    * * [string] 	$capability    	: droits d'accès, "editor" (defaut) ou "admin"
    * * [number] 	$position    	: position dans le menu
    * * [string] 	$icon    		: icône dans le menu
    *
    * cf. https://developer.wordpress.org/reference/functions/add_menu_page/
    * cf. https://developer.wordpress.org/reference/functions/add_submenu_page/
    * cf. https://developer.wordpress.org/resource/dashicons/
    * cf. https://codex.wordpress.org/Creating_Options_Pages
    *
    */
    
    public function __construct( $title, $parent, $menuLabel, $slug = '', $content, $capability = 'editor', $position = '99', $icon = 'dashicons-clipboard' ) {

    	/*----------  variables de la class  ----------*/
    	
    	$this->title 		= $title;
    	$this->slug 		= $slug;
    	$this->content 		= $content;
    	$this->optionName 	= $slug.'-option';
    	$this->capability 	= $capability;
	    

	    /*----------  Insertion dans le menu  ----------*/
	    
	    add_action( 'admin_menu', function() use( $title, $parent, $menuLabel, $slug, $position, $icon, $capability ) {

	    	// accès à la page aux éditeurs ou que au administrateur
	    	if ( $capability == 'admin' ) { $capability = 'manage_options'; }
	    	else { $capability = 'edit_others_posts'; }

	    	if ( '' == $parent ) {

	    		// niveau 1 dans le menu WP
		    	add_menu_page(
					$title,
					$menuLabel,
					$capability,
					$slug,
					array( $this, 'page_admin_display' ),
					$icon,
					$position
				);

			} else {

				// niveau 2 dans le menu WP
				add_submenu_page(
					$parent,
					$title,
					$menuLabel,
					$capability,
					$slug,
					array( $this, 'page_admin_display' )
				);

			}

	    });


	    /*----------  Création du contenu  ----------*/

	    add_action( 'admin_init', array( $this, 'add_admin_fields') );


	} // FIN __construct()


    /*=====  FIN Constructeur  ======*/
    
    /*=================================
    =            Conteneur            =
    =================================*/
    
    public function page_admin_display() {

    	echo '<div class="wrap"><h1>'.$this->title.'</h1>';

	    settings_errors();

	    echo '<form method="post" action="options.php" enctype="multipart/form-data">';

	        settings_fields($this->slug.'-settings');

	        do_settings_sections($this->slug);

	        // l'input submit
	        submit_button(); 

	    echo '</form></div>';


    } // FIN page_admin_display
    
    
    /*=====  FIN Conteneur  ======*/

    /*===============================
    =            Contenu            =
    ===============================*/
    
    public function add_admin_fields() {

    	/*----------  Entrée BDD  ----------*/
    	
    	register_setting(
	    	$this->slug.'-settings', 	// $option_group
	    	$this->optionName			// $option_name
	    );


    	/*----------  autorisation de sauvegarder pour les éditeurs  ----------*/
    	
    	if ( $this->capability == 'editor' ) { add_filter( 'option_page_capability_'.$this->slug.'-settings', function(){ return 'edit_theme_options'; }); }


    	/*----------  Sections & champs  ----------*/
    	
    	$content = $this->content;

    	// sections
    	foreach ( $content as $sectionDatas ) {

    		if ( isset($sectionDatas['desc']) ) { $desc = $sectionDatas['desc']; } else { $desc = ''; }
    		$idSection 	= $this->slug.'-'.$sectionDatas['id'];

			add_settings_section(

				$idSection,									// $id
				$sectionDatas['title'],						// $title
				function() use( $desc ) { echo $desc; },	// $callback
				$this->slug 								// $page

			);

			// champs
			foreach ($sectionDatas['fields'] as $fieldKey => $fieldValues) {

				// valeurs en bdd
				$inBdd = get_option($this->optionName);

				// toutes les propriétés
				$datasFields = array_merge(
					// défaut
					array(
						'inBdd'		=> $inBdd,
						'label_for'	=> '', // attribut for du label généré par wp
		                'type'      => '',
						'name'		=> '',
		                'label'     => '',
		                'help'      => '',
		                'attr'      => '',
		                'css'       => '',
		                'options'   => array()
		            ),
		            // arguments passés lors de la création
					$fieldValues
				);

				// id préfixé
				$datasFields['label_for'] = $sectionDatas['prefix'].'-'.$datasFields['label_for'];
				// construction du name
				$datasFields['name'] = $this->optionName.'['.$datasFields['label_for'].']';

				// type
				switch ( $datasFields['type'] ) {

					case 'text':
						$type = 'display_input_text';
						break;

					case 'checkbox':
						$type = 'display_checkbox';
						break;

					case 'radio':
						$type = 'display_radio';
						break;

					case 'select':
						$type = 'display_select';
						break;

					case 'textarea':
						$type = 'display_textarea';
						break;

					case 'wysiwyg':
						$type = 'display_wysiwyg';
						$datasFields['options']['textarea_name'] = $datasFields['name'];
						break;

					case 'img':
						$type = 'display_img';
						break;

					case 'pdf':
						$type = 'display_pdf';
						break;

				} // FIN switch($datasFields['type'])

				add_settings_field(

			    	$datasFields['label_for'],	// $id
			    	$datasFields['label'],		// $title
			    	array( $this, $type ),		// $callback
			    	$this->slug,				// $page
			    	$idSection,					// $section
			    	$datasFields				// $datas passé au callback

			    ); // FIN add_settings_field(
			
			}; // FIN foreach($value['fields'])

    	}; // FIN foreach($content)

    } // FIN add_admin_fields()
    
    
    /*=====  FIN Contenu  ======*/

    /*========================================
    =            Rendu des champs            =
    ========================================*/

    /*----------  help (commun à tous les champs)  ----------*/

    public function display_desc( $msg ) {

    	if ( '' != $msg ) { echo '<p class="description">'.$msg.'</p>';	}

    }
    
    
    /*----------  Input text  ----------*/
    
    public function display_input_text( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }

		echo '<input type="text" name="'.$datas['name'].'" id="'.$id.'" value="'.$value.'" style="'.$datas['css'].'"  '.$datas['attr'].'/>';

		$this->display_desc( $datas['help'] );
	    
	}
    

    /*----------  Checkbox  ----------*/
    
    public function display_checkbox( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $checked = 'checked="checked"'; } else { $checked = ''; }

		echo '<input type="checkbox" name="'.$datas['name'].'" id="'.$id.'" value="1"' .$checked. ' '.$datas['attr'].'/>';

		$this->display_desc( $datas['help'] );		
	    
	}
    

    /*----------  Radio  ----------*/
    
    public function display_radio( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }
	
		$radioIndex = 0; // <br/> à partir de 1
		foreach ($datas['options'] as $radioKey => $radioValue) {
			if ( $radioIndex > 0 ) { echo '<br/>'; }
			echo '<input type="radio" id="'.$id.'-'.$radioIndex.'" '.$datas['attr'].' name="'.$datas['name'].'" value="'.$radioValue.'" '.checked($radioValue, $value, false).'/>';
			echo '<label for="'.$id.'-'.$radioIndex.'">'.$radioKey.'</label>';
			$radioIndex++;
		}

		$this->display_desc( $datas['help'] );		
	    
	}
    

    /*----------  Select  ----------*/
    
    public function display_select( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $selected = esc_attr( $datas['inBdd'][$id] ); } else { $selected = ''; }

		$select = '<select id="'.$id.'" name="'.$datas['name'].'" '.$datas['attr'].'>';
	    $select .= '<option value=""></option>';
	    foreach($datas['options'] as $cle => $value) {
	        $select .= '<option value="'.$value.'"' . selected( $selected, $value, false) . '>'.$cle.'</option>';
	    }
	    $select .= '</select>';

	    echo $select;

	    $this->display_desc( $datas['help'] );	
	    
	}
    
    
    /*----------  Textarea  ----------*/
    
    public function display_textarea( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }

		echo '<textarea name="'.$datas['name'].'" id="'.$id.'" '.$datas['attr'].' style="'.$datas['css'].'" />'.$value.'</textarea>';

		$this->display_desc( $datas['help'] );
	    
	}
    
    
    /*----------  WYSIWYG  ----------*/
    
    public function display_wysiwyg( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = $datas['inBdd'][$id]; } else { $value = ''; }

		wp_editor( $value, $id, $datas['options'] );

		$this->display_desc( $datas['help'] );
	    
	}
    
    
    /*----------  Image  ----------*/
    
    public function display_img( $datas ) {

		$id 		= $datas['label_for']; 	// id du champ
		$value 		= '';					// valeur en bdd
		$dataRemove = '';					// signale l'activation du btn remove au javascript
		$btnRemove 	= '';					// btn remove (html)
		
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
			$value = $datas['inBdd'][$id];
			// affichage image
			$img = wp_get_attachment_image_src($value,'medium');
        	echo '<img class="pc-media-preview" src="'.$img[0].'" />';
		}

		// btn de suppression
		if ( $datas['options']['btnremove'] == true ) {
			$dataRemove		= 'data-remove="active"';
			if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
				$btnRemove 	= '<input class="button pc-media-remove" type="button" value="Supprimer"/>';
			}
		}

		echo '<input type="hidden" name="'.$datas['name'].'" id="'.$id.'" class="pc-media-id" value="'.$value.'" />';
		echo '<input class="button pc-img-select" type="button" value="Sélectionner une image" '.$dataRemove.' />';
		echo $btnRemove;

		$this->display_desc( $datas['help'] );
	    
	}
    
    
    /*----------  Pdf  ----------*/
    
    public function display_pdf( $datas ) {

		$id 		= $datas['label_for']; 	// id du champ
		$value 		= '';					// valeur en bdd
		$dataRemove = '';					// signale l'activation du btn remove au javascript
		$btnRemove 	= '';					// btn remove (html)
		
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
			$value = $datas['inBdd'][$id];
			// affichage image
			$pdfUrl = wp_get_attachment_url($value);
        	echo '<a class="pc-media-preview" href="'.$pdfUrl.'" target="_blank">Voir le fichier actuel</a>';
		}

		// btn de suppression
		if ( $datas['options']['btnremove'] == true ) {
			$dataRemove		= 'data-remove="active"';
			if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
				$btnRemove 	= '<input class="button pc-media-remove" type="button" value="Supprimer"/>';
			}
		}

		echo '<input type="hidden" name="'.$datas['name'].'" id="'.$id.'" class="pc-media-id" value="'.$value.'" />';
		echo '<input class="button pc-pdf-select" type="button" value="Sélectionner un pdf" '.$dataRemove.' />';
		echo $btnRemove;

		$this->display_desc( $datas['help'] );
	    
	}

    
    /*=====  FIN Rendu des champs  ======*/
    	
}