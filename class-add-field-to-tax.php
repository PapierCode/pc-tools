<?php
/**
*
* Ajout de champs aux taxonomies
*
**/

// 07/09/16 : Création

/*=====================================
=            Mode d'emploi            =
=====================================*/
/*

if ( class_exists('PC_add_field_to_tax') ) {

	// si un champ de type wysiwig
	// tinyMce custom options, plugin [PC] Project WP config
	$tinymceDefault = get_option( 'pc-settings-option' );

	// contenu de la metabox
	$xxxContent = array(	    
        'title'     => [obligatoire] titre de la section libre,
        'desc'      => description de la metabox, html libre,
        'prefix'    => [obligatoire] préfix des champs, tiret pour séparer les mots, sans caractères spéciaux ni majuscules,
        'fields'    => array( liste des champs dans cette metabox
            array( répéter pour chaque champ
                'type'      => [obligatoire] 'text', 'checkbox', 'radio', 'select', 'textarea', 'wysiwyg'
                'id' 		=> [obligatoire] attribut for du label et id du champ, tiret pour séparer les mots, sans caractères spéciaux ni majuscules, 
                'label'     => [obligatoire] label du champ, texte libre,
                'desc'      => description du champ, texte libre sans html (balise p générée automatiquement),
                'attr'      => attribut du champ : class, data,...,
                'css'       => inline css,
                'options'	=> 
                [obligatoire] pour les types select et radio : 
                	array('Valeur A' => 'a', ... )
                [obligatoire] pour le type wysiwyg, la config générale (voir plugin [PC] Project WP config) ou à personnaliser : 
                	array(
	                    'media_buttons'                     => (isset($tinymceDefault['tinymce-media']) ? true : false),
	                    'quicktags'                         => (isset($tinymceDefault['tinymce-quicktags']) ? true : false),
	                    'textarea_rows'                     => $tinymceDefault['tinymce-rows'],
	                    'tinymce'                           => array (
	                        'toolbar1'                      	=> $tinymceDefault['tinymce-toolbar1'],
	                        'toolbar2'                      	=> $tinymceDefault['tinymce-toolbar2'],
	                        'block_formats'                 	=> $tinymceDefault['tinymce-block'],
	                        'visualblocks_default_state'    	=> (isset($tinymceDefault['tinymce-visualblocks']) ? true : false),
	                        'paste_as_text'                 	=> (isset($tinymceDefault['tinymce-paste']) ? true : false)
                    )
            ),
            ...
        ) // FIN liste des champs
	);

	// création
	$xxx = new PC_add_field_to_tax(
		[obligatoire] slug de la tax,
		[obligatoire] contenu de la page, voir $xxxContent ci-dessus
	);


} // FIN if(class_exists('PC_add_field_to_tax'))

*/
/*=====  FIN Mode d'emploi  ======*/


class PC_add_field_to_tax {

	public $tax;
	public $content;

    /*====================================
    =            Constructeur            =
    ====================================*/
    
    public function __construct( $tax, $content ) {

	    /*----------  Variables de la class  ----------*/
	    
	    $this->tax = $tax;

		// toutes les propriétés sont fusionnées avec celles par défaut
		// pour éviter une erreur si les non obligatoires sont absentes à la création
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

		$content = $this->content;

		echo '</table>';
		
		// input hidden de vérification pour la sauvegarde
		wp_nonce_field( basename( __FILE__ ), $content['prefix'].'-'.'nonce' );

		echo '<h2>'.$content['title'].'</h2>'.$content['desc'].'<table class="form-table">';

		// champs
		foreach ($content['fields'] as $field) {

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
			$field['id'] = $content['prefix'].'-'.$field['id'];

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
				$fieldTemp = $_POST[$id];
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

?>