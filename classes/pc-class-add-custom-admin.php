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
    * * [function] 	$sanitize  		: fonction de traitement des données
    *
    * cf. https://developer.wordpress.org/reference/functions/add_menu_page/
    * cf. https://developer.wordpress.org/reference/functions/add_submenu_page/
    * cf. https://developer.wordpress.org/resource/dashicons/
    * cf. https://codex.wordpress.org/Creating_Options_Pages
    *
    */

    public function __construct( $title, $parent, $menuLabel, $slug, $content, $capability = 'editor', $position = '99', $icon = 'dashicons-clipboard', $sanitize ='' ) {

    	/*----------  variables de la class  ----------*/

    	$this->title 		= $title;
    	$this->slug 		= $slug;
    	$this->content 		= $content;
    	$this->optionName 	= $slug.'-option';
    	$this->capability 	= $capability;
    	$this->sanitize 	= $sanitize;


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

    	echo '<div class="wrap pc-settings"><h1>'.$this->title.'</h1>';

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
	    	$this->optionName,			// $option_name
	    	$this->sanitize				// $sanitize
	    );

    	/*----------  autorisation de sauvegarder pour les éditeurs  ----------*/

    	if ( $this->capability == 'editor' ) { add_filter( 'option_page_capability_'.$this->slug.'-settings', function(){ return 'edit_theme_options'; }); }


    	/*----------  Sections & champs  ----------*/

    	$content = $this->content;

    	// sections
    	foreach ( $content as $sectionDatas ) {

    		$desc = ( isset($sectionDatas['desc']) ) ? '<div class="pc-metabox-help">'.$sectionDatas['desc'].'</div>' : '';
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
		                'desc'      => '',
		                'attr'      => '',
		                'css'       => '',
		                'required'	=> false,
		                'options'   => array()
		            ),
		            // arguments passés lors de la création
					$fieldValues
				);

				// id préfixé
				$datasFields['label_for'] = $sectionDatas['prefix'].'-'.$datasFields['label_for'];
				// construction du name
				$datasFields['name'] = $this->optionName.'['.$datasFields['label_for'].']';
				// champ obligatoire
				if ( $datasFields['required'] ) { $datasFields['label'] = $datasFields['label'].'<span class="label-required"> *</span>'; }

				// type
				switch ( $datasFields['type'] ) {

					case 'text':
						$type = 'display_input_text';
						break;

					case 'email':
						$type = 'display_input_email';
						break;

					case 'number':
						$type = 'display_input_number';
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
						// paramètre pour wp_editor
						$datasFields['options']['textarea_name'] = $datasFields['name'];
						break;

					case 'img':
						$type = 'display_img';
						break;

					case 'gallery':
						$type = 'display_gallery';
						break;

					case 'pdf':
						$type = 'display_pdf';
						break;

					case 'file':
						$type = 'display_input_file';
						// attribut name simplifié
						$datasFields['name'] = $datasFields['label_for'];
						break;

					case 'url':
						$type = 'display_input_url';
						// chargement des scripts de l'éditeur
						add_action( 'admin_enqueue_scripts', function () {
							wp_enqueue_editor();
						});
						break;

					case 'date':
						$type = 'display_input_date';
						// chargement de jQuery DatePicker
						add_action( 'admin_enqueue_scripts', function () {
				    		wp_enqueue_script( 'jquery-ui-datepicker' );
				    		wp_enqueue_style( 'admin-datepicker-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );
						});
						break;

					case 'custom':
						$type = 'display_custom';
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

    /*----------  desc (commun à tous les champs)  ----------*/

    public function display_desc( $msg ) {

    	if ( '' != $msg ) { echo '<p class="description">'.$msg.'</p>';	}

    }


    /*----------  Input text  ----------*/

    public function display_input_text( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }

		echo '<input type="text" name="'.$datas['name'].'" id="'.$id.'" value="'.$value.'" style="'.$datas['css'].'"  '.$datas['attr'].' '.$required.' />';

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Input number  ----------*/

    public function display_input_number( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }

		echo '<input type="number" name="'.$datas['name'].'" id="'.$id.'" value="'.$value.'" style="'.$datas['css'].'"  '.$datas['attr'].' '.$required.' />';

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Input email  ----------*/

    public function display_input_email( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }

		echo '<input type="email" name="'.$datas['name'].'" id="'.$id.'" value="'.$value.'" style="'.$datas['css'].'"  '.$datas['attr'].' '.$required.' />';

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Input date  ----------*/

    public function display_input_date( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( pc_date_bdd_to_admin($datas['inBdd'][$id]) ); } else { $value = ''; }
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }
		// recherche de l'attibut class
		// pour ajouter la classe nécessaire au javascript
		$dateAttr = strpos($datas['attr'], 'class="');
		if ($dateAttr !== false) {
		    $dateAttr = str_replace('class="', 'class="pc-date-picker ', $datas['attr']);
		} else {
		    $dateAttr = 'class="pc-date-picker" '.$datas['attr'];
		}

		echo '<input type="text" name="'.$datas['name'].'" id="'.$id.'" value="'.$value.'" style="'.$datas['css'].'"  '.$dateAttr.' '.$required.' readonly />';

		if ( $value != '' && $required == '' ) {
			echo '<button class="reset-btn pc-date-remove" type="button" title="Supprimer"><span class="dashicons dashicons-no-alt"></span></button>';
		}

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Input url  ----------*/

    public function display_input_url( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }

		echo '<div style="display:flex;"><div style="flex-grow:1;margin-right:10px;"><input type="url" name="'.$datas['name'].'" id="'.$id.'" value="'.$value.'" '.$required.' style="width:100%;" /></div><div><button type="button" class="button pc-link-select" data-cible="'.$id.'">Sélectionner</button></div></div>';

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Checkbox  ----------*/

    public function display_checkbox( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $checked = 'checked="checked"'; } else { $checked = ''; }

		echo '<input type="checkbox" name="'.$datas['name'].'" id="'.$id.'" value="1"' .$checked. ' '.$datas['attr'].'/>';

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Radio  ----------*/

    public function display_radio( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }

		$radioIndex = 0; // <br/> à partir de 1
		foreach ($datas['options'] as $radioKey => $radioValue) {
			if ( $radioIndex > 0 ) { echo '<br/>'; }
			echo '<input type="radio" id="'.$id.'-'.$radioIndex.'" '.$datas['attr'].' name="'.$datas['name'].'" value="'.$radioValue.'" '.checked($radioValue, $value, false).' '.$required.' />';
			echo '<label for="'.$id.'-'.$radioIndex.'">'.$radioKey.'</label>';
			$radioIndex++;
		}

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Select  ----------*/

    public function display_select( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $selected = esc_attr( $datas['inBdd'][$id] ); } else { $selected = ''; }
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }

		$select = '<select id="'.$id.'" name="'.$datas['name'].'" '.$datas['attr'].' '.$required.' >';
	    $select .= '<option value=""></option>';
	    foreach($datas['options'] as $cle => $value) {
	        $select .= '<option value="'.$value.'"' . selected( $selected, $value, false) . '>'.$cle.'</option>';
	    }
	    $select .= '</select>';

	    echo $select;

	    $this->display_desc( $datas['desc'] );

	}


    /*----------  Textarea  ----------*/

    public function display_textarea( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }

		echo '<textarea name="'.$datas['name'].'" id="'.$id.'" '.$datas['attr'].' style="'.$datas['css'].'" '.$required.' />'.$value.'</textarea>';

		$this->display_desc( $datas['desc'] );

	}


    /*----------  WYSIWYG  ----------*/

    public function display_wysiwyg( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = $datas['inBdd'][$id]; } else { $value = ''; }

		// configuration wysiwyg par défaut
		$pcSettings = get_option( 'pc-settings-option' );
		$buttonsDefault = array(
            'media_buttons'		=> true,
		    'quicktags'    		=> true,
		    'textarea_rows'		=> 6,
		    'tinymce'      		=> array (
		        'toolbar1'                  	=> $pcSettings['tinymce-toolbar1'],
		        'toolbar2'                  	=> $pcSettings['tinymce-toolbar2'],
		        'block_formats'             	=> $pcSettings['tinymce-block'],
		        'visualblocks_default_state'	=> true,
		        'paste_as_text'             	=> true,
		        'media_alt_source'          	=> false,
		        'media_poster'              	=> false
		    )
        );
        // si une configuration est passé dans les arguments
		if ( $datas['options'] != '' ) {
			// configuration wysiwyg = fusion défaut/nouvelle
			$buttons = pc_array_multi_merge($buttonsDefault,$datas['options']);
		} else {
			// configuration wysiwyg = defaut
			$buttons = $buttonsDefault;
		}

		wp_editor( $value, $id, $buttons );

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Image  ----------*/

    public function display_img( $datas ) {

		$id 		= $datas['label_for']; 		// id du champ
		$value 		= '';						// valeur en bdd
		$dataRemove = '';						// signale l'activation du btn remove au javascript
		$btnRemove 	= '';						// btn remove (html)
		$btnTxt		= 'Ajouter';				// texte du bouton qui ouvre la modal

		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
			$btnTxt = 'Modifier';
			$value = $datas['inBdd'][$id];
			echo '<div class="pc-media-preview">';
			echo '<div class="pc-media-preview-item" style="background-image:url('.wp_get_attachment_image_src($value,'thumbnail')[0].');"></div>';
			echo '</div>';
		}
		// btn de suppression
		if ( $datas['options']['btnremove'] == true ) {
			$dataRemove		= 'data-remove="active"';
			if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
				$btnRemove 	= '<input class="button pc-media-remove" type="button" value="Supprimer"/>';
			}
		}
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }

		echo '<input type="text" name="'.$datas['name'].'" id="'.$id.'" class="pc-media-id visually-hidden" value="'.$value.'" '.$required.' />';
		echo '<input class="button pc-img-select" type="button" value="'.$btnTxt.'" '.$dataRemove.' />';
		echo $btnRemove;

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Gallerie  ----------*/

    public function display_gallery( $datas ) {

		$id 		= $datas['label_for']; 		// id du champ
		$value 		= '';						// valeur en bdd
		$dataRemove = '';						// signale l'activation du btn remove au javascript
		$btnRemove 	= '';						// btn remove (html)
		$btnTxt		= 'Ajouter';				// texte du bouton qui ouvre la modal

		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
			$btnTxt = 'Modifier';
			$value = $datas['inBdd'][$id];
			$imgIds = explode(',', $value);

			echo '<div class="pc-media-preview">';
			foreach ($imgIds as $imgId) {
				echo '<div class="pc-media-preview-item" style="background-image:url('.wp_get_attachment_image_src($imgId,'thumbnail')[0].');"></div>';
			}
			echo '</div>';
		}
		// btn de suppression
		if ( $datas['options']['btnremove'] == true ) {
			$dataRemove		= 'data-remove="active"';
			if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
				$btnRemove 	= '<input class="button pc-media-remove" type="button" value="Supprimer"/>';
			}
		}
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }		

		echo '<input type="text" name="'.$datas['name'].'" id="'.$id.'" class="pc-media-id visually-hidden" value="'.$value.'" '.$required.' />';
		echo '<input class="button pc-gallery-select" type="button" value="'.$btnTxt.'" '.$dataRemove.' />';
		echo $btnRemove;

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Pdf  ----------*/

    public function display_pdf( $datas ) {

		$id 		= $datas['label_for']; 		// id du champ
		$value 		= '';						// valeur en bdd
		$dataRemove = '';						// signale l'activation du btn remove au javascript
		$btnRemove 	= '';						// btn remove (html)
		$btnTxt		= 'Ajouter';				// texte du bouton qui ouvre la modal

		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
			$btnTxt = 'Modifier';
			$value = $datas['inBdd'][$id];
			// affichage lien pdf
			$pdfUrl = wp_get_attachment_url($value);
        	echo '<div class="pc-media-preview"><a class="pc-pdf-preview" href="'.$pdfUrl.'" target="_blank"><div class="dashicons dashicons-media-default"></div> Voir le fichier actuel</a></div>';
		}

		// btn de suppression
		if ( $datas['options']['btnremove'] == true ) {
			$dataRemove		= 'data-remove="active"';
			if ( isset($datas['inBdd'][$id]) && '' != $datas['inBdd'][$id] ) {
				$btnRemove 	= '<input class="button pc-media-remove" type="button" value="Supprimer"/>';
			}
		}
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }	

		echo '<input type="text" name="'.$datas['name'].'" id="'.$id.'" class="pc-media-id visually-hidden" value="'.$value.'" '.$required.' />';
		echo '<input class="button pc-pdf-select" type="button" value="'.$btnTxt.'" '.$dataRemove.' />';
		echo $btnRemove;

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Input file  ----------*/

    public function display_input_file( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }
		// champ obligatoire
		if ( $datas['required'] ) { $required = 'required'; } else { $required = ''; }

		echo '<input type="file" name="'.$datas['name'].'" id="'.$id.'" value="'.$value.'" style="'.$datas['css'].'"  '.$datas['attr'].' '.$required.' />';

		$this->display_desc( $datas['desc'] );

	}


    /*----------  Custom  ----------*/

    public function display_custom( $datas ) {

		$id = $datas['label_for'];
		// si une valeur en bdd
		if ( isset($datas['inBdd'][$id]) ) { $value = esc_attr( $datas['inBdd'][$id] ); } else { $value = ''; }

		echo $datas['display'];

		$this->display_desc( $datas['desc'] );

	}


    /*=====  FIN Rendu des champs  ======*/

}
