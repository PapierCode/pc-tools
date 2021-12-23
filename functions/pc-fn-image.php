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
	if ( $hidden ) { $svg = str_replace('<svg', '<svg aria-hidden="true" focusable="false"', $svg); }

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

	$sprite_js = array();
	foreach ( $icons as $id ) { $sprite_js[$id] = str_replace( '<svg', '<svg aria-hidden="true" focusable="false"', $sprite[$id] ); }
	
	echo '<script>var sprite = '.json_encode( $sprite_js, JSON_PRETTY_PRINT ).'</script>';

}


/*=====  FIN Sprite PHP to JS  =====*/