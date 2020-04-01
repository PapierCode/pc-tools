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

function pc_svg( $index, $color = false, $class = 'svg-block', $hidden = true ) {

	global $sprite; // cf. images/sprite.php
	$svg = $sprite[$index];

	// no print
	$svg = str_replace('<svg', '<svg class="no-print"', $svg);

	// couleur
	if ( $color ) {	$svg = str_replace('fill="#fff"', 'fill="'.$color.'"', $svg); }

	// aria hidden
	if ( $hidden ) { $svg = str_replace('<svg', '<svg aria-hidden="true"', $svg); }

	// css
	$svg = str_replace('class="no-print"', 'class="no-print '.$class.'"', $svg);

	return $svg;

}

/*=====  FIN Get SVG  =====*/

/*=================================
=            Get image            =
=================================*/

/*----------  Par ID  ----------*/

function pc_get_img( $id, $size, $return = 'img', $class = '' ) {

	$attr = wp_get_attachment_image_src( $id, $size );
	$alt = get_post_meta( $id, '_wp_attachment_image_alt', true );

    switch ($return) {
        case 'img':
            return '<img src="'.$attr[0].'" class="'.$class.'" width="'.$attr[1].'" height="'.$attr[2].'" alt="'.esc_attr($alt).'"/>';
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

function pc_sprite_to_js( $icons ) {

	global $sprite;

	$js_sprite = array();
	foreach ( $icons as $id ) { $js_sprite[$id] = $sprite[$id]; }

	$js_sprite = apply_filters( 'pc_filter_js_sprite', $js_sprite );
	
	echo '<script>var sprite = '.json_encode( $js_sprite, JSON_PRETTY_PRINT ).'</script>';

}


/*=====  FIN Sprite PHP to JS  =====*/