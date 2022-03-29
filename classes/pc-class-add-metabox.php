<?php

/**
 *
 * [PC] Tools : création d'une metabox et des champs associés
 *
 */


class PC_Add_Metabox {

	private $for;
	private $title;
	private $id;
	private $content;
	private $position;
	private $priority;


    /*====================================
    =            Constructeur            =
    ====================================*/

    /**
	 * [array] 	$post 		: slugs des custom posts concernés
     * [string]	$title 		: titre de la metabox
     * [string]	$id 		: identifiant de la metabox
     * [array]	$content 	: contenu de la metabox
     * [string]	$position 	: position dans l'interface, "normal" (defaut) ou "side"
     * [string]	$priority 	: priorité d'affichage, "high" (defaut) ou "low"
	 *
	 */

   public function __construct( $for, $title, $id, $content, $position = 'normal', $priority = 'high' ) {

		/*----------  Préparation des données  ----------*/

		$content = array_merge(
				array(
					'desc' 		=> '',
					'prefix' 	=> '',
					'fields' 	=> array()
				),
				$content
		);

    	foreach ($content['fields'] as $key => $field ) {			
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
		            'options'		=> array(),
					'clean'			=> true,
					'default'		=> '',
					'admin_not_in'	=> false
		      	),
				$content['fields'][$key]
			);

    	}

		$this->for			= $for;
		$this->title 		= $title;
		$this->id 			= $id;
		$this->content 		= $content;
		$this->position 	= $position;
		$this->priority 	= $priority;
		

		/*----------  Dépendances  ----------*/

		add_action( 'admin_enqueue_scripts', array( $this, 'add_dependencies' ) );

		/*----------  Création  ----------*/

		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ), 10, 2 );

	   /*----------  Sauvegarde  ----------*/

    	add_action( 'save_post', array( $this, 'save_metabox' ) );

	} // FIN __construct()


   	/*=====  FIN Constructeur  ======*/

	/*===================================
	=            Dépendances            =
	===================================*/
	
	public function add_dependencies( $hook_suffix ) {
		
		if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) ) {

			$fields = $this->content['fields'];

			foreach ( $fields as  $field ) {

				if ( $field['type'] == 'url' && ( !isset($field['options']['btnselection']) || true == $field['options']['btnselection'] ) ) {
					wp_enqueue_editor();
				}

			}

		}

	}
	
	
	/*=====  FIN Dépendances  =====*/
	
	/*=========================================
	=            Création métaboxe            =
	=========================================*/
	
	public function add_metabox( $post_type, $post ) {

		if ( apply_filters( 'pc_filter_add_metabox', true, $this->id, $post ) ) {

			add_meta_box(
				$this->id,
				$this->title,
				array( $this, 'display_metabox_content' ),
				$this->for,
				$this->position,
				$this->priority,
				$this->content
			);

		}

	}
	
	
	/*=====  FIN Création métaboxe  =====*/

	/*==============================
	=            Champs            =
	==============================*/

	public function display_metabox_content( $post ) {

		$content = apply_filters( 'pc_filter_metabox_content', $this->content, $this->id, $post );

		// description
		if ( !empty( $content['desc'] ) ) { echo '<div class="pc-metabox-help">'.$content['desc'].'</div>'; }

		// input hidden de vérification pour la sauvegarde
		wp_nonce_field( basename( __FILE__ ), $this->id.'-'.'nonce' );

		echo '<table class="form-table pc-metabox">';

			// champs
			foreach ( $content['fields'] as $field ) {

				if ( $field['admin_not_in'] ) { continue; }

				echo '<tr>';
					$this->display_field( $field, $content['prefix'], $post );
				echo '</tr>';

			} // FIN foreach($data[args])

		echo '</table>';

	} // FIN display_metabox_content()


	/*=====  FIN Champs  ======*/

	/*=======================================
	=            Affichage champ            =
	=======================================*/
	
	private function display_field( $field, $prefix, $post ) {

		// nom & identifiant
		$name = $prefix.'-'.$field['id'];
		// sauvegarde & défaut
		$value = get_post_meta( $post->ID, $name, true );
		if ( '' == $value ) { $value = $field['default']; }
		// attribut obligatoire
		$required = ( $field['required'] ) ? 'required' : '';

		$label_for = ( !in_array( $field['type'], array( 'radio', 'checkboxes' ) ) ) ? ' for="'.$name.'"' : '';
		$label_txt = ( $field['required'] ) ? $field['label'].'<span class="label-required"> *</span>' : $field['label'];
		echo '<th><label'.$label_for.'>'.$label_txt.'</label></th>';
		
		echo '<td>';

			switch ( $field['type'] ) {

				case 'text':
				case 'email':
				case 'number':
				case 'date':
				case 'time':
				case 'datetime-local':
					echo '<input type="'.$field['type'].'" id="'.$name.'" style="'.$field['css'].'" '.$field['attr'].' name="'.$name.'" value="'.$value.'" '.$required.' />';
					break;

				case 'checkbox':
					echo '<input type="checkbox" id="'.$name.'" '.$field['attr'].' style="'.$field['css'].'" name="'.$name.'" value="1" '.checked('1', $value, false).'/>';
					break;

				case 'textarea':
					echo '<textarea id="'.$name.'" '.$field['attr'].' style="'.$field['css'].'" name="'.$name.'" '.$required.' >'.$value.'</textarea>';
					break;

				case 'select':
					echo '<select id="'.$name.'" '.$field['attr'].' style="'.$field['css'].'" name="'.$name.'" '.$required.' ><option value=""></option>';
					foreach ($field['options'] as $optionsKey => $optionValue) {
						echo '<option value="'.$optionValue.'" '.selected($value,$optionValue,false).'>'.$optionsKey.'</option>';
					}
					echo '</select>';
					break;

				case 'url':
					$display_url_picker = ( !isset($field['options']['btnselection']) || true == $field['options']['btnselection'] ) ? true : false;
					if ( $display_url_picker ) { echo '<div style="display:flex;"><div style="flex-grow:1;margin-right:10px;">'; }
					echo '<input type="url" id="'.$name.'" name="'.$name.'" value="'.$value.'" '.$required.' style="width:100%" />';
					if ( $display_url_picker ) { echo '</div><div><button type="button" class="button pc-link-select" data-cible="'.$name.'">Sélectionner</button></div></div>'; }
					break;

				case 'radio':
					echo '<div>';
						$radio_index = 0;
						$value = ( '' == $value && '' != $field['default'] ) ? $field['default'] : $value;
						foreach ( $field['options'] as $radio_label => $radio_value ) {
							if ( $radio_index > 0 ) { echo '<br/>'; }
							echo '<input type="radio" id="'.$name.'-'.$radio_index.'" '.$field['attr'].' style="'.$field['css'].'" name="'.$name.'" value="'.$radio_value.'" '.checked($radio_value, $value, false).' '.$required.' />';
							echo '<label for="'.$name.'-'.$radio_index.'">'.$radio_label.'</label>';
							$radio_index++;
						}
					echo '</div>';
					break;

				case 'checkboxes':
					echo ( $required == 'required' ) ? '<div class="pc-checkboxes-required">' : '<div>';
						$checkbox_index = 0;
						foreach ( $field['options'] as $checkbox_label => $checkbox_value ) {
							if ( $checkbox_index > 0 ) { echo '<br/>'; }
							$checked = ( !empty($value) && in_array( $checkbox_value, $value ) ) ? 'checked' : '';
							echo '<input type="checkbox" id="'.$name.'-'.$checkbox_index.'" '.$field['attr'].' style="'.$field['css'].'" name="'.$name.'[]" value="'.$checkbox_value.'" '.$checked.' '.$required.' />';
							echo '<label for="'.$name.'-'.$checkbox_index.'">'.$checkbox_label.'</label>';
							$checkbox_index++;
						}
					echo '</div>';
					break;

				case 'wysiwyg':
					$settings_pc = get_option( 'pc-settings-option' );
					$wysiwyg_default_settings = array(
						'media_buttons'		=> true,
						'quicktags'    		=> ( !current_user_can('administrator') ) ? false : true,
						'textarea_rows'		=> 6,
						'tinymce'      		=> array (
							'toolbar1'                  	=> $settings_pc['tinymce-toolbar1'],
							'toolbar2'                  	=> $settings_pc['tinymce-toolbar2'],
							'block_formats'             	=> $settings_pc['tinymce-block'],
							'visualblocks_default_state'	=> true,
							'paste_as_text'             	=> true,
							'wp_autoresize_on'				=> true,
							'media_alt_source'          	=> false, // options plugin media
							'media_poster'              	=> false // options plugin media
						)
					);
					$wysiwyg_settings = ( is_array( $field['options'] ) && !empty($field['options']) ) ? pc_array_multi_merge( $wysiwyg_default_settings, $field['options'] ) : $wysiwyg_default_settings;
					wp_editor( $value, $name, $wysiwyg_settings );
					break;

				case 'img':
				case 'pdf':
				case 'audio':
				case 'file':
				case 'gallery':
					$button_media_txt = 'Ajouter';
					echo '<div class="pc-media-preview">';
					if ( '' != $value ) {
						$button_media_txt = 'Modifier';
						switch ( $field['type'] ) {
							case 'pdf':
							case 'file':
								echo '<a class="pc-'.$field['type'].'-preview" href="'.wp_get_attachment_url($value).'" target="_blank"><div class="dashicons dashicons-media-default"></div> Voir le fichier</a>';
								break;
							case 'audio':
								echo '<audio class="pc-audio-preview" controls src="'.wp_get_attachment_url($value).'"></audio>';
								break;
							case 'img':				
							case 'gallery':				
								$gallery_images_id = explode( ',', $value );
								foreach ( $gallery_images_id as $image_id ) {
									echo '<div class="pc-media-preview-item" style="background-image:url('.wp_get_attachment_image_src($image_id,'thumbnail')[0].');"></div>';
								}
								break;
						}
					}
					echo '</div>';

					echo '<input type="text" id="'.$name.'" class="pc-media-id visually-hidden" name="'.$name.'" value="'.$value.'" '.$required.'/>';

					$button_media_css = array( 'button', 'pc-media-select' );
					if ( 'gallery' == $field['type'] ) { $button_media_css[] = 'pc-gallery-select'; }

					$button_media_data = array();
					if ( 'gallery' != $field['type'] ) { $button_media_data[] = 'data-type="'.$field['type'].'"'; }
					if ( isset( $field['options']['btnremove'] ) && true == $field['options']['btnremove'] ) { 
						$button_media_data[] = 'data-remove="active"';
						$display_button_media_remove = true;
					}

					echo '<input class="'.implode(' ',$button_media_css).'" type="button" value="'.$button_media_txt.'" '.implode(' ',$button_media_data).'>';
					if ( isset( $display_button_media_remove ) && '' != $value ) {
						echo ' <input class="button pc-media-remove" type="button" value="Supprimer"/>';
					}
					break;

			} // FIN switch($field['type'])
		
			// description du champ
			if ( !empty($field['desc']) ) { echo '<p class="description">'.$field['desc'].'</p>'; }

		echo '</td>';		

	}
	
	
	/*=====  FIN Affichage champ  =====*/

	/*==================================
	=            Sauvegarde            =
	==================================*/

	public function save_metabox( $post_ID ) {

		$content = $this->content; 	// pour la liste des champs

		// check input hidden de vérification
    	if ( isset($_POST[$this->id.'-'.'nonce']) && wp_verify_nonce( $_POST[$this->id.'-'.'nonce'], basename( __FILE__ ) ) ) {

			foreach ($content['fields'] as $field) {

				// id préfixé
				$name = $content['prefix'].'-'.$field['id'];
				// valeur renvoyé par le form
				$value_temp = $_POST[$name];
				// nettoyage
				switch ( $field['type'] ) {
					case 'text':
						if ( $field['clean'] ) { $value_temp = sanitize_text_field( $value_temp ); }
						if ( $field['clean'] ) { $value_temp = sanitize_text_field( $value_temp ); }
						break;
					case 'textarea':
						if ( $field['clean'] ) { $value_temp = sanitize_textarea_field( $value_temp ); }
						break;
				}
				// valeur en bdd
				$value_saved = get_post_meta( $post_ID, $name, true );

				// si une valeur arrive & si rien en bdd
				if ( $value_temp && '' == $value_saved ) {
					add_post_meta( $post_ID, $name, $value_temp, true );

				// si une valeur arrive & différente de la bdd
				} elseif ( $value_temp && $value_temp != $value_saved ) {
					update_post_meta( $post_ID, $name, $value_temp );

				// si rien n'arrive & si un truc en bdd
				} elseif ( '' == $value_temp && $value_saved ) {
					delete_post_meta( $post_ID, $name );
				}

			}

		}

	} // FIN save_metabox()


	/*=====  FIN Sauvegarde  ======*/

}
