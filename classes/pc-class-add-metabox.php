<?php

/**
*
* [PC] Tools : création d'une metabox et des champs associés
*
**/


class PC_Add_Metabox {

	public $id;
	public $content;

    /*====================================
    =            Constructeur            =
    ====================================*/

    /*
    *
    * * [array]		$post 		: slugs des custom posts concernés
    * * [string]	$title 		: titre de la metabox
    * * [string]	$id 		: identifiant de la metabox
    * * [array]		$content 	: contenu de la metabox
    * * [string]	$position 	: position dans l'interface, "normal" (defaut) ou "side"
    * * [string]	$priority 	: priorité d'affichage, "high" (defaut) ou "low"
    *
    * cf. https://developer.wordpress.org/reference/functions/add_meta_box/
    *
    */

   public function __construct( $posts, $title, $id, $content, $position = 'normal', $priority = 'high' ) {


		/*----------  Vérifcation des données  ----------*/

		// pour les valeurs relatives à définition de la metabox
	   // fusion du contenu avec des valeurs vides
	   // évite une erreur en cas d'omission
	   $content = array_merge(
	    	// défaut
	    	array(
	    		'desc' 		=> '',
	    		'prefix' 	=> '',
	    		'fields' 	=> array()
	    	),
	    	// arguments passés lors de la création
	    	$content
	   );

		// pour les valeurs relatives à chaque champ défini
    	foreach ($content['fields'] as $key => $field ) {

			// fusion du propriétés du champ avec des valeurs vides
	    	// évite une erreur en cas d'omission
			$content['fields'][$key] = array_merge(
				// defaut
				array(
		            'type'  		=> '',
		            'label' 		=> '',
		            'desc'  		=> '',
		            'id'    		=> '',
		            'required'		=> false,
		            'attr' 			=> '',
		            'css'			=> '',
		            'options'		=> '',
					'clean'			=> true,
					'default'		=> ''
		      ),
				// arguments passés lors de la création
				$content['fields'][$key]
			);

    	} // FIN foreach($content[fields])


	   /*----------  Variables de la class  ----------*/

	   $this->id = $id;
	   $this->content = $content;


		/*----------  Création  ----------*/

		add_action( 'admin_init', function() use( $posts, $title, $id, $content, $position, $priority ) {

			add_meta_box(
		      $id,										// $id
		      $title,									// $title
		      array( $this, 'add_metabox_fields' ),	// $callback
		      $posts,									// $screen
		      $position,								// $position
		      $priority,								// $priority
		      $content 								// $callback_args
		  	);

		} );


	   /*----------  Sauvegarde  ----------*/

    	add_action( 'save_post', array( $this, 'save_metabox_fields' ) );


		/*----------  Scripts & styles supplémentaires  ----------*/

		// pour chaque champ défini
		foreach ($content['fields'] as  $field ) {

			// Scripts & styles supplémentaires : type date
			if ( $field['type'] == 'date' ) {

				add_action( 'admin_enqueue_scripts', function () {

					// chargement de jQuery DatePicker
					wp_enqueue_script( 'jquery-ui-datepicker' );
					wp_enqueue_style( 'admin-datepicker-css', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );

				});

			} // FIN if type=date

			// Scripts & styles supplémentaires : type url
			if ( $field['type'] == 'url' ) {

				add_action( 'admin_enqueue_scripts', function () {

					// chargement des scripts de l'éditeur
					wp_enqueue_editor();

				});

			} // FIN if type=url

		} // FIN foreach($content[fields])


	} // FIN __construct()


   /*=====  FIN Constructeur  ======*/

	/*==============================
	=            Champs            =
	==============================*/

	public function add_metabox_fields( $post, $datas ) {

		// description
		echo $datas['args']['desc'];

		// input hidden de vérification pour la sauvegarde
		wp_nonce_field( basename( __FILE__ ), $this->id.'-'.'nonce' );

		echo '<table class="form-table">';

		// champs
		foreach ( $datas['args']['fields'] as $field ) {

			// id prefixé
			$field['id'] = $datas['args']['prefix'].'-'.$field['id'];
			// valeurs en bdd
			$savedValue = get_post_meta( $post->ID, $field['id'], true );
			if ( $savedValue == '' ) { $savedValue = $field['default']; }
			// champ obligatoire
			if ( $field['required'] ) {
				$required = 'required';
				$field['label'] = $field['label'].'<span class="label-required"> *</span>';
			} else {
				$required = '';
			}

			echo '<tr>';

			switch ( $field['type'] ) {

				case 'text':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<input type="text" id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="'.$savedValue.'" '.$required.'  />';
					break;

				case 'email':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<input type="email" id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="'.$savedValue.'" '.$required.'  />';
					break;

				case 'number':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<input type="number" id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="'.$savedValue.'" '.$required.'  />';
					break;

				case 'checkbox':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<input type="checkbox" id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="1" '.checked('1', $savedValue, false).'/>';
					break;

				case 'radio':
					echo '<th>'.$field['label'].'</th><td><div>';
					$radioIndex = 0;
					foreach ($field['options'] as $radioKey => $radioValue) {
						if ( $radioIndex > 0 ) { echo '<br/>'; }
						echo '<input type="radio" id="'.$field['id'].'-'.$radioIndex.'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="'.$radioValue.'" '.checked($radioValue, $savedValue, false).' '.$required.' />';
						echo '<label for="'.$field['id'].'-'.$radioIndex.'">'.$radioKey.'</label>';
						$radioIndex++;
					}
					echo '</div>';
					break;

				case 'checkboxes':
					echo '<th>'.$field['label'].'</th><td';
					if ( $required == 'required' ) { echo ' class="pc-checkboxes-required"'; };
					echo '><div>';
					$checkboxIndex = 0;
					foreach ($field['options'] as $checkboxKey => $checkboxValue) {
						if ( $checkboxIndex > 0 ) { echo '<br/>'; }
						$checked = ( !empty($savedValue) && in_array($checkboxValue,$savedValue) ) ? 'checked' : '';
						echo '<input type="checkbox" id="'.$field['id'].'-'.$checkboxIndex.'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'[]" value="'.$checkboxValue.'" '.$checked.' '.$required.' />';
						echo '<label for="'.$field['id'].'-'.$checkboxIndex.'">'.$checkboxKey.'</label>';
						$checkboxIndex++;
					}
					echo '</div>';
					break;

				case 'textarea':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<textarea id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" '.$required.' >'.$savedValue.'</textarea>';
					break;

				case 'select':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<select id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" '.$required.' ><option value=""></option>';
					foreach ($field['options'] as $optionsKey => $optionValue) {
						echo '<option value="'.$optionValue.'" '.selected($savedValue,$optionValue,false).'>'.$optionsKey.'</option>';
					}
					echo '</select>';
					break;

				case 'wysiwyg':
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
					if ( $field['options'] != '' ) {
						// configuration wysiwyg = fusion défaut/nouvelle
						$buttons = pc_array_multi_merge($buttonsDefault,$field['options']);
					} else {
						// configuration wysiwyg = defaut
						$buttons = $buttonsDefault;
					}
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					wp_editor( $savedValue, $field['id'], $buttons );
					break;

				case 'img':
					$btnTxt = 'Ajouter';
					// label
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					// si une valeur en bdd
					if ( isset($savedValue) && '' != $savedValue ) {
						$btnTxt = 'Modifier';
						// affichage image
						echo '<div class="pc-media-preview">';
						echo '<div class="pc-media-preview-item" style="background-image:url('.wp_get_attachment_image_src($savedValue,'thumbnail')[0].');"></div>';
						echo '</div>';
					}
					// champs
					echo '<input type="text" id="'.$field['id'].'" class="pc-media-id visually-hidden" name="'.$field['id'].'" value="'.$savedValue.'" '.$required.'/>';
					echo '<input class="button pc-img-select" type="button" value="'.$btnTxt.'" ';
					// si btn de suppression activé
					if ( $field['options']['btnremove'] == true ) {
						echo 'data-remove="active" />';
						// affiche le btn si une image est déjà enregistrée
						if ( isset($savedValue) && '' != $savedValue ) {
							echo ' <input class="button pc-media-remove" type="button" value="Supprimer"/>';
						}
					} else { echo ' />'; }
					break;

				case 'pdf':
					$btnTxt = 'Ajouter';
					// label
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					// si une valeur en bdd
					if ( isset($savedValue) && '' != $savedValue ) {
						$btnTxt = 'Modifier';
						// affichage lien pdf
						$pdfUrl = wp_get_attachment_url($savedValue);
			        	echo '<div class="pc-media-preview"><a class="pc-pdf-preview" href="'.$pdfUrl.'" target="_blank"><div class="dashicons dashicons-media-default"></div> Voir le fichier actuel</a></div>';
					}
					// champs
					echo '<input type="hidden" id="'.$field['id'].'" class="pc-media-id" name="'.$field['id'].'" value="'.$savedValue.'"/>';
					echo '<input class="button pc-pdf-select" type="button" value="'.$btnTxt.'" ';
					// si btn de suppression activé
					if ( $field['options']['btnremove'] == true ) {
						echo 'data-remove="active" />';
						// affiche le btn si une image est déjà enregistrée
						if ( isset($savedValue) && '' != $savedValue ) {
							echo ' <input class="button pc-media-remove" type="button" value="Supprimer"/>';
						}
					} else { echo ' />'; }
					break;

				case 'gallery':
					$btnTxt = 'Ajouter';
					// label
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					// si une valeur en bdd
					if ( isset($savedValue) && '' != $savedValue ) {
						$btnTxt = 'Modifier';
						// affichage images
						$imgIds = explode(',', $savedValue);
						echo '<div class="pc-media-preview">';
						foreach ($imgIds as $imgId) {
							echo '<div class="pc-media-preview-item" style="background-image:url('.wp_get_attachment_image_src($imgId,'thumbnail')[0].');"></div>';
						}
						echo '</div>';
					}
					// champs
					echo '<input type="text" id="'.$field['id'].'" class="pc-media-id visually-hidden" name="'.$field['id'].'" value="'.$savedValue.'" '.$required.'/>';
					echo '<input class="button pc-gallery-select" type="button" value="'.$btnTxt.'" ';
					// si btn de suppression activé
					if ( $field['options']['btnremove'] == true ) {
						echo 'data-remove="active" />';
						// affiche le btn si une image est déjà enregistrée
						if ( isset($savedValue) && '' != $savedValue ) {
							echo ' <input class="button pc-media-remove" type="button" value="Supprimer"/>';
						}
					} else { echo ' />'; }
					break;

				case 'date':

					// recherche de l'attribut class
					// pour ajouter la classe nécessaire au javascript
					$dateAttr = strpos($field['attr'], 'class="');
					if ($dateAttr !== false) {
					    $dateAttr = str_replace('class="', 'class="pc-date-picker ', $field['attr']);
					} else {
					    $dateAttr = 'class="pc-date-picker" '.$field['attr'];
					}

					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<input type="text" id="'.$field['id'].'" '.$dateAttr.' style="'.$field['css'].'" name="'.$field['id'].'" value="'.pc_date_bdd_to_admin($savedValue).'"  '.$required.' readonly />';
					if ( $savedValue != '' && $required == '' ) {
						echo '<button class="reset-btn pc-date-remove" type="button" title="Supprimer"><span class="dashicons dashicons-no-alt"></span></button>';
					}
					break;

				case 'url':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td><div style="display:flex;">';
					echo '<div style="flex-grow:1;margin-right:10px;"><input type="url" id="'.$field['id'].'" name="'.$field['id'].'" value="'.$savedValue.'" '.$required.' style="width:100%" /></div><div><button type="button" class="button pc-link-select" data-cible="'.$field['id'].'">Sélectionner</button></div>';
					echo '</div>';
					break;

			} // FIN switch($field['type'])

			// description du champ
			if ( !empty($field['desc']) ) { echo '<p class="description">'.$field['desc'].'</p>'; }

			echo '</td></tr>';

		} // FIN foreach($data[args])

		echo '</table>';

	} // FIN add_metabox_fields()


	/*=====  FIN Champs  ======*/

	/*==================================
	=            Sauvegarde            =
	==================================*/

	public function save_metabox_fields( $post_ID ) {

		//pc_var($_POST); exit();

		$content = $this->content; 	// pour la liste des champs

		// check input hidden de vérification
    	if ( isset($_POST[$this->id.'-'.'nonce']) && wp_verify_nonce( $_POST[$this->id.'-'.'nonce'], basename( __FILE__ ) ) ) {

			foreach ($content['fields'] as $field) {

				// id préfixé
				$id = $content['prefix'].'-'.$field['id'];
				// valeur renvoyé par le form
				$fieldTemp = $_POST[$id];
				// nettoyage
				switch ($field['type']) {
					case 'text':
						if ( $field['clean'] ) { $fieldTemp = sanitize_text_field( $fieldTemp ); }
						break;
					case 'url':
						if ( $field['clean'] ) { $fieldTemp = sanitize_text_field( $fieldTemp ); }
						break;
					case 'textarea':
						if ( $field['clean'] ) { $fieldTemp = sanitize_textarea_field( $fieldTemp ); }
						break;
				}
				// valeur en bdd
				$fieldSave = get_post_meta( $post_ID, $id, true );

				// si champ de type date -> changement de format
				if ( $field['type'] == 'date' ) { $fieldTemp = pc_date_admin_to_bdd($fieldTemp); }

				// si une valeur arrive & si rien en bdd
				if ( $fieldTemp && '' == $fieldSave ) {
					add_post_meta( $post_ID, $id, $fieldTemp, true );

				// si une valeur arrive & différente de la bdd
				} elseif ( $fieldTemp && $fieldTemp != $fieldSave ) {
					update_post_meta( $post_ID, $id, $fieldTemp );

				// si rien n'arrive & si un truc en bdd
				} elseif ( '' == $fieldTemp && $fieldSave ) {
					delete_post_meta( $post_ID, $id );
				}

			}

		}

	} // FIN save_metabox_fields()


	/*=====  FIN Sauvegarde  ======*/

}
