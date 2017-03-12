<?php

/**
*
* [PC] Tools : ajout de champs aux taxonomies
*
**/


class PC_add_field_to_tax {

	public $tax;
	public $content;

    /*====================================
    =            Constructeur            =
    ====================================*/

    /*
    *
    * * [string]	$tax 		: nom de la taxonomie
    * * [array]		$content	: champs à intégrer
    *
    *
    */
    
    public function __construct( $tax, $content ) {

	    /*----------  Variables de la class  ----------*/
	    
	    $this->tax = $tax;


		/*----------  Fusion du contenu avec les valeurs par défaut  ----------*/

	    $content = array_merge(
	    	// defaut
	    	array(
	    		'title'		=> '',
	    		'desc' 		=> '',
	    		'prefix' 	=> '',
	    		'fields' 	=> array()
	    	),
	    	// arguments passés lors de la création 
	    	$content
	    );
	    $this->content = $content;


		/*----------  Création  ----------*/

        add_action( $tax.'_edit_form_fields', array( $this, 'add_field_to_tax' ) );


	    /*----------  Sauvegarde  ----------*/
	    
	    add_action( 'edited_'.$tax, array( $this, 'save_tax_fields' ) );


	} // FIN __construct()


    /*=====  FIN Constructeur  ======*/

	/*==============================
	=            Champs            =
	==============================*/
	
	public function add_field_to_tax( $term ) {

		// contenu enregistré dans la class
		$content = $this->content;

		// fermeture du tableau html existant
		echo '</table>';		
		// input hidden de vérification pour la sauvegarde
		wp_nonce_field( basename( __FILE__ ), $content['prefix'].'-'.'nonce' );
		// titre de l'ensemble
		echo '<h2>'.$content['title'].'</h2>'.$content['desc'].'<table class="form-table">';

		// champs
		foreach ($content['fields'] as $field) {

			// fusion du propriétés du champ avec des valeurs vides
	    	// évite une erreur en cas d'omission
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
			$field['id'] = $content['prefix'].'-'.$field['id'];
			// valeur en bdd
			$savedValue = get_term_meta( $term->term_id, $field['id'], true );

			echo '<tr class="form-field term-group-wrap"><th scope="row">';

			switch ( $field['type'] ) {

				case 'text':
					echo '<label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<input type="text" id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="'.$savedValue.'" />';
					break;

				case 'checkbox':
					echo '<label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<input type="checkbox" id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="1" '.checked('1', $savedValue, false).'/>';
					break;

				case 'radio':
					echo $field['label'].'</th><td>';
					$radioIndex = 0;
					foreach ($field['options'] as $radioKey => $radioValue) {
						if ( $radioIndex > 0 ) { echo '<br/>'; }
						echo '<input type="radio" id="'.$field['id'].'-'.$radioIndex.'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'" value="'.$radioValue.'" '.checked($radioValue, $savedValue, false).'/>';
						echo '<label for="'.$field['id'].'-'.$radioIndex.'">'.$radioKey.'</label>';
						$radioIndex++;
					}
					break;

				case 'select':
					echo '<label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<select id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'" name="'.$field['id'].'"><option value=""></option>';
					foreach ($field['options'] as $optionsKey => $optionValue) {
						echo '<option value="'.$optionValue.'" '.selected($savedValue,$optionValue,false).'>'.$optionsKey.'</option>';
					}
					echo '</select>';
					break;

				case 'textarea':
					echo '<label for="'.$field['id'].'">'.$field['label'].'</label></th><td>';
					echo '<textarea name="'.$field['id'].'" id="'.$field['id'].'" '.$field['attr'].' style="'.$field['css'].'">'.get_term_meta( $term->term_id, $field['id'], true ).'</textarea>';
					break;

				case 'wysiwyg':
					echo $field['label'].'</th><td>';
					wp_editor( $savedValue, $field['id'], $field['options'] );
					break;

			} // FIN switch($field['type'])

			if ( !empty($field['desc']) ) { echo '<p class="description">'.$field['desc'].'</p>'; }

			echo '</td></tr>';
			
		} // FIN foreach($content['fields'])

		

	} // FIN add_metabox_fields()


	/*=====  FIN Champs  ======*/

	/*==================================
	=            Sauvegarde            =
	==================================*/
	
	public function save_tax_fields( $term_id ) {

		$content = $this->content; // contient la liste des champs

    	if ( isset( $_POST[$content['prefix'].'-'.'nonce'] ) && wp_verify_nonce( $_POST[$content['prefix'].'-'.'nonce'], basename( __FILE__ ) ) ) {

			foreach ($content['fields'] as $field) {

				// id préfixé
				$id = $content['prefix'].'-'.$field['id'];
				// valeur renvoyé par le form
				$fieldTemp = $_PO
				switch ($field['type']) {
					case 'text':
						$fieldTemp = sanitize_text_field( $fieldTemp );
						break;
					case 'textarea':
						$fieldTemp = sanitize_textarea_field( $fieldTemp );
						break;
				}
				// valeur en bdd
				$fieldSave = get_term_meta( $term_id, $id, true );

				// si une valeur arrive & si rien en bdd
				if ( $fieldTemp && '' == $fieldSave ) {
					add_term_meta( $term_id, $id, $fieldTemp, true );

				// si une valeur arrive & différente de la bdd
				} elseif ( $fieldTemp && $fieldTemp != $fieldSave ) {
					update_term_meta( $term_id, $id, $fieldTemp );

				// si rien n'arrive & si un truc en bdd
				} elseif ( '' == $fieldTemp && $fieldSave ) {
					delete_term_meta( $term_id, $id );
				}				

			}

		}

	} // FIN save_metabox_fields()

	
	/*=====  FIN Sauvegarde  ======*/	
	
}