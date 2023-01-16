<?php

/*===========================================
=            Extraction repeater            =
===========================================*/

function pc_repeater_extract_sub( $string, $tag ) {

	if ( str_contains( $string, $tag ) ) {

		$tag_length = strlen( $tag );
		$sub_start = strpos( $string, '['.$tag.']' ) + ( $tag_length + 2 );
		$sub_length = strpos( $string, '[/'.$tag.']' ) - $sub_start;

		return substr( $string, $sub_start, $sub_length );

	} else { return ''; }

}

function pc_repeater_extract( $string, $tags, $separator = '[/]' ) {

	$elts = explode( $separator, $string);
	$subs = array();

	foreach ( $elts as $elt ) {
		$sub = array();
		foreach ( $tags as $tag ) {
			$sub[$tag] =  pc_repeater_extract_sub( $elt, $tag );
		}
		$subs[] = $sub;
	}

	return $subs;

}


/*=====  FIN Extraction repeater  =====*/


class PC_Repeater {

	private $field_name;
	private $field_value;
	private $fields;
	private $repeater_args;


	/*=================================
	=            Construct            =
	=================================*/
	
	public function __construct ( $field_name, $field_value, $fields, $repeater_args ) {

		$this->field_name = $field_name;
		$this->field_value = $field_value;
		$this->fields = $fields;
		$this->repeater_args = $repeater_args;

	}

	
	/*=====  FIN Construct  =====*/

	private function display_repeater_item( $index, $saved = null, $src = false ) {

		$css = 'pc-repeater-item';
		if ( $src ) { $css .= ' pc-repeater-src'; }

		$return = '<div class="'.$css.'">';
		$return .= '<div class="pc-repeater-fields">';

		foreach ( $this->fields as $name => $args ) {

			$id = $name.'-'.$index;
			$value = ( is_array( $saved ) ) ? $saved[$name] : '';
			$required = ( isset( $args['required'] ) && !$src ) ? ' required' : '';
			$data_required = ( isset( $args['required'] )) ? ' data-required="required"' : '';

			$attrs = '';
			if ( isset( $args['attrs'] )) {
				foreach ( $args['attrs'] as $attr => $attr_value ) {
					$attrs .= ' '.$attr.'="'.$attr_value.'"';
				}
			}

			$return .= '<div class="pc-repeater-field">';

				/*----------  Label  ----------*/
				
				$return .= '<label for="'.$id.'">'.$args['label'];
				if ( '' != $data_required ) { $return .= '<span class="label-required"> *</span>'; }
				$return .= '</label>';

				/*----------  Champ  ----------*/
				
				$return .= '<div class="pc-repeater-type pc-repeater-type_'.$args['type'].'">';

				switch ( $args['type'] ) {
					
					case 'text':
					case 'number':
						$return .= '<input type="'.$args['type'].'" style="width:100%" id="'.$id.'" name="'.$name.'" value="'.$value.'"'.$required.$data_required.$attrs.' />';
						break;
					
					case 'url':
						$return .= '<div style="display:flex;"><div style="flex-grow:1;margin-right:10px;"><input type="'.$args['type'].'" style="width:100%" id="'.$id.'" name="'.$name.'" value="'.$value.'"'.$required.$data_required.$attrs.' /></div><div><button type="button" class="button pc-link-select" data-cible="'.$id.'">Sélectionner</button></div></div>';
						break;
					
					case 'textarea':
						$return .= '<textarea style="width:100%" id="'.$id.'" name="'.$name.'"'.$required.$data_required.$attrs.' />'.$value.'</textarea>';
						break;
					
					case 'select':
						$return .= '<select style="width:100%" id="'.$id.'" name="'.$name.'"'.$required.$data_required.$attrs.'>';
							$return .= '<option value=""></option>';
							foreach ( $this->repeater_args[$name] as $option_value => $option_label ) {
								$return .= '<option value="'.$option_value.'" '.selected( $value, $option_value, false ).'>'.$option_label.'</option>';
							}
						$return .= '</select>';
						break;

					case 'img':
						$return .= '<div class="pc-media-preview">';
						if ( !$src && '' != $value && is_object( get_post( $value ) ) ) {
							$btn_txt = 'Modifier';
							$return .= '<div class="pc-media-preview-item" style="background-image:url('.wp_get_attachment_image_src($value,'ico')[0].');"></div>';
						} else { $btn_txt = 'Ajouter'; }
						$return .= '</div>';
						$return .= '<input type="text" name="'.$name.'" id="'.$id.'" class="pc-media-id visually-hidden" value="'.$value.'"'.$required.$data_required.' />';
						$return .= '<input class="button pc-media-select" data-type="image" type="button" value="'.$btn_txt.'" />';
						break;

					case 'audio':
						$return .= '<div class="pc-media-preview">';
						if ( !$src && '' != $value && is_object( get_post( $value ) ) ) {
							$btn_txt = 'Modifier';
							$return .= '<audio class="pc-audio-preview" controls src="'.wp_get_attachment_url($value).'"></audio>';
						} else { $btn_txt = 'Ajouter'; }
						$return .= '</div>';
						$return .= '<input type="text" name="'.$name.'" id="'.$id.'" class="pc-media-id visually-hidden" value="'.$value.'"'.$required.$data_required.' />';
						$return .= '<input class="button pc-media-select" data-type="audio" type="button" value="'.$btn_txt.'" />';
						break;

				}

				$return .= '</div>'; // FIN .pc-repeater-field

			$return .= '</div>'; // FIN 

		}

		$return .= '</div>'; // FIN .pc-repeater-fields

		/*----------  Actions  ----------*/
		
		$return .= '<div class="pc-repeater-actions"><span class="pc-repeater-btn-move dashicons dashicons-move" title="Déplacer"></span><span class="pc-repeater-btn-trash dashicons dashicons-trash" title="Supprimer"></span></div>';

		$return .= '</div>'; // FIN .pc-repeater-item

		return $return;

	}

	/*=========================================
	=            Affichage complet            =
	=========================================*/
	
	public function display( $separator = '[/]' ) {

		$repeater_args = $this->repeater_args;
	
		$fields_names = array();
		foreach ( $this->fields as $name => $args ) { $fields_names[] = $name; }
	
		$return = '<div class="pc-repeater" data-fields="'.implode( ',', $fields_names ).'" data-separator="'.$separator.'">';

			if ( '' != $this->field_value ) {

				$items_saved = pc_repeater_extract( $this->field_value , $fields_names, $separator );

				foreach ( $items_saved as $item_index => $item_value ) {
					$return .= $this->display_repeater_item( $item_index, $item_value );
				}

			}

		$return .= '</div>';
		
		$add_button_txt = ( isset($repeater_args['add_button_txt']) ) ? $repeater_args['add_button_txt'] : 'Ajouter un item';
		$return .= '<button type="button" class="button pc-repeater-more">'.$add_button_txt.'</button>';
		$return .= '<input type="hidden" name="'.$this->field_name.'" value="'.$this->field_value.'" class="pc-repeater-target" />';

		$return .= $this->display_repeater_item( '[index]', null, true );

		return $return;

	}
	
	
	/*=====  FIN Affichage complet  =====*/

}