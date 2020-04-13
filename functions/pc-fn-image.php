<?php
/**
 * 
 * Fonctions utiles aux images
 * 
 ** Get SVG
 ** Get image
 ** Sprite PHP to JS
 * 
 */

/*===============================
=            Get SVG            =
===============================*/

/**
 * 
 * @param string	$index		Index du tableau $sprite
 * @param string	$color		Attribut fill sur la balise svg
 * @param string	$css		Attribut class sur la balise svg
 * @param string	$hidden		Attribut aria-hidden sur la balise svg
 * 
 */

function pc_svg( $index, $color = '', $css = 'svg-block', $hidden = true ) {

	global $sprite; // cf. images/sprite.php
	$svg = $sprite[$index];

	// no print
	$svg = str_replace('<svg', '<svg class="no-print"', $svg);

	// couleur
	if ( $color != '' ) {	$svg = str_replace('fill="#fff"', 'fill="'.$color.'"', $svg); }

	// aria hidden
	if ( $hidden ) { $svg = str_replace('<svg', '<svg aria-hidden="true"', $svg); }

	// css
	$svg = str_replace('class="no-print"', 'class="no-print '.$css.'"', $svg);

	return $svg;

}

/*=====  FIN Get SVG  =====*/

/*=================================
=            Get image            =
=================================*/

/*----------  Par ID  ----------*/

/**
 * 
 * @param integer	$id		Identifiant de l'image
 * @param string	$size	Taille de l'image ("thumbnail", "medium",...)
 * @param string	$return	Valeur retournée par la fonction : "img" (HTML) ou "datas" (array)
 * @param string	$css	Attribut class sur la balise img
 * 
 */

function pc_get_img( $id, $size, $return = 'img', $css = '' ) {

	$attr = wp_get_attachment_image_src( $id, $size );
	$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );

    switch ($return) {
        case 'img':
            return '<img src="'.$attr[0].'" class="'.$css.'" width="'.$attr[1].'" height="'.$attr[2].'" alt="'.esc_attr($alt).'"/>';
            break;
        case 'datas':
            $datas = $attr;
            $datas[3] = $alt;
            return $datas;
            break;
    }

}

/*----------  Pour le partage, par défaut  ----------*/

function pc_get_img_default_to_share() {

	global $images_project_sizes;

	$img = array(
		get_template_directory_uri().'/images/share-default.jpg',
		$images_project_sizes['share']['width'],
		$images_project_sizes['share']['height']
	);
	$img = apply_filters( 'pc_filter_img_default_to_share', $img );

	return $img;

}


/*=====  FIN Get image  =====*/

/*========================================
=            Sprite PHP to JS            =
========================================*/

/**
 * 
 * @param array	$icons	Index du tableau $sprite à convertir
 * 
 */

function pc_sprite_to_js( $icons ) {

	global $sprite;

	$js_sprite = array();
	foreach ( $icons as $id ) { $js_sprite[$id] = $sprite[$id]; }

	$js_sprite = apply_filters( 'pc_filter_js_sprite', $js_sprite );
	
	echo '<script>var sprite = '.json_encode( $js_sprite, JSON_PRETTY_PRINT ).'</script>';

}


/*=====  FIN Sprite PHP to JS  =====*/