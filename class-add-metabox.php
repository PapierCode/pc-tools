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
    
    public function __construct( $posts, $title, $id, $content, $context = 'normal', $priority = 'high' ) {

	    /*----------  variables de la class  ----------*/
	    
	    $this->id = $id;

		// toutes les propriétés sont fusionnées avec celles par défaut
		// pour éviter une erreur si les non obligatoires sont absentes à la création
	    $content = array_merge(
	    	// defaut
	    	array(
	    		'desc' 		=> '',
	    		'prefix' 	=> '',
	    		'fields' 	=> array()
	    	),
	    	// arguments passés lors de la création 
	    	$content
	    );
	    $this->content = $content;


		/*----------  Création  ----------*/

        add_action( 'admin_init', function() use( $id, $title, $posts, $context, $priority, $content ) {

        	add_meta_box(	        		
	            $id,									// $id
	            $title,									// $title
	            array( $this, 'add_metabox_fields' ),	// $callback
	            $posts,									// $screen
	            $context,								// $context
	            $priority,								// $priority
	            $content 								// $callback_args
	        );

        } );


	    /*----------  Sauvegarde  ----------*/
	    
    	add_action( 'save_post', array( $this, 'save_metabox_fields' ) );


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

			// toutes les propriétés sont fusionnées avec celles par défaut
			// pour éviter une erreur si les non obligatoires sont absentes à la création
			$field = array_merge(
				// defaut
				array(
		            'type'  	=> '',
		            'label' 	=> '',
		            'desc'  	=> '',
		            'id'    	=> '',
		            'attr' 		=> '',
		            'css'		=> '',
		            'options'	=> ''

		        ),
				// arguments passés lors de la création 
				$field
			);

			// id prefixé
			$field['id'] = $datas['args']['prefix'].'-'.$field['id'];
			// valeurs en bdd
			$savedValue = get_post_meta( $post->ID, $field['id'], true );

			echo '<tr>';

			switch ( $field['type'] ) {

				case 'text':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<input type="text" id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="'.$savedValue.'" />';
					break;

				case 'checkbox':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<input type="checkbox" id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="1" '.checked('1', $savedValue, false).'/>';
					break;

				case 'radio':
					echo '<th>'.$field['label'].'</th><td>';
					$radioIndex = 0;
					foreach ($field['options'] as $radioKey => $radioValue) {
						if ( $radioIndex > 0 ) { echo '<br/>'; }
						echo '<input type="radio" id="'.$field['id'].'-'.$radioIndex.'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="'.$radioValue.'" '.checked($radioValue, $savedValue, false).'/>';
						echo '<label for="'.$field['id'].'-'.$radioIndex.'">'.$radioKey.'</label>';
						$radioIndex++;
					}
					break;

				case 'textarea':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<textarea id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'">'.$savedValue.'</textarea>';
					break;

				case 'select':
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<select id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'"><option value=""></option>';
					foreach ($field['options'] as $optionsKey => $optionValue) {
						echo '<option value="'.$optionValue.'" '.selected($savedValue,$optionValue,false).'>'.$optionsKey.'</option>';
					}
					echo '</select>';
					break;

				case 'wysiwyg':
					echo '<td colspan="2" style="padding-left:0">';
					wp_editor( $savedValue, $field['id'], $field['options'] );
					break;

				case 'img':
					// btn de suppression
					$dataRemove = '';
					$btnRemove 	= '';
					if ( $field['options']['btnremove'] == true ) {
						$dataRemove		= 'data-remove="active"';
						if ( isset($savedValue) && '' != $savedValue ) {
							$btnRemove 	= '<input class="button pc-media-remove" type="button" value="Supprimer"/>';
						}
					}
					// label
					echo '<th><label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					// si une valeur en bdd
					if ( isset($savedValue) && '' != $savedValue ) {
						// affichage image
						$img = wp_get_attachment_image_src($savedValue,'thumbnail');
			        	echo '<img class="pc-media-preview" src="'.$img[0].'" />';
					}
					// champs
					echo '<input type="hidden" id="'.$field['id'].'" class="pc-media-id" name="'.$field['id'].'" value="'.$savedValue.'" />';
					echo '<input class="button pc-img-select" type="button" value="Sélectionner une image" '.$dataRemove.' />';
					echo ' '.$btnRemove;
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

		$content = $this->content; 	// pour la liste des champs

		// check input hidden de vérification
    	if ( isset($_POST[$this->id.'-'.'nonce']) && wp_verify_nonce( $_POST[$this->id.'-'.'nonce'], basename( __FILE__ ) ) ) {

			foreach ($content['fields'] as $field) {

				// id préfixé
				$id = $content['prefix'].'-'.$field['id'];
				// valeur renvoyé par le form
				$fieldTemp = $_POST[$id];
				// valeur en bdd
				$fieldSave = get_post_meta( $post_ID, $id, true );

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

?>