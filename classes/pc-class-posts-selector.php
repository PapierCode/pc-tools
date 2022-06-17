<?php


class PC_Posts_Selector {

	private $field_name;
	private $field_value;
	private $repeater_args;
	private $query_args;


	/*=================================
	=            Construct            =
	=================================*/
	
	public function __construct ( $field_name, $field_value, $query_args, $repeater_args = array() ) {

		$this->field_name = $field_name;
		$this->field_value = $field_value;
		$this->repeater_args = $repeater_args;
		$this->query_args = $query_args;

	}

	
	/*=====  FIN Construct  =====*/

	private function get_posts_list() {

		$repeater_args = $this->repeater_args;
		$field_value = explode( ',', $this->field_value );
		
		$posts = get_posts( $this->query_args );

		$posts_list = array();
		foreach ( $posts as $post ) {
			if ( isset( $repeater_args['subpages'] ) ) {
				if ( $post->post_parent < 1 || in_array( $post->ID, $field_value ) ) {
					$posts_list[] = array( 
						'id' => $post->ID,
						'title' => $post->post_title
					);
				}
			} else {
				$posts_list[] = array( 
					'id' => $post->ID,
					'title' => $post->post_title
				);
			}
		}
		
		return $posts_list;

	}

	private function display_repeater_item( $src = false, $id = 0 ) {

		$css = 'posts-selector-elt';
		if ( $src ) { $css .= ' posts-selector-src'; }

		$return = '<div class="'.$css_class.'>';
	
			// sélecteur de post
			$return .= '<select><option value=""></option></select>';
			// effacer la ligne
			$return .= ' <span title="Effacer" style="vertical-align:middle; cursor:pointer;" class="wpr-repeater-btn-delete dashicons dashicons-trash"></span>';
			// déplacer la ligne
			$return .= ' <span title="Déplacer" style="vertical-align:middle; cursor:move;" class="dashicons dashicons-move"></span>';
		
		$return .= '</div>';

		return $return;

	}

	/*=========================================
	=            Affichage complet            =
	=========================================*/
	
	public function display() {

		$repeater_args = $this->repeater_args;
	
		$return = '<div class="posts-selector" data-name="'.$this->field_name.'" data-posts="'._wp_specialchars( wp_json_encode($this->get_posts_list()), ENT_QUOTES, 'UTF-8', true ).'">';
			
			$return .= '<div class="posts-selector-list"></div>';
		
			$add_button_txt = ( isset($repeater_args['add_button_txt']) ) ? $repeater_args['add_button_txt'] : 'Ajouter un item';
			$return .= '<button type="button" class="button pc-repeater-more">'.$add_button_txt.'</button>';
			$return .= '<input type="hidden" name="'.$this->field_name.'" value="'.$this->field_value.'" class="posts-selector-target" />';

		$return .= '</div>';

		return $return;

	}
	
	
	/*=====  FIN Affichage complet  =====*/

}