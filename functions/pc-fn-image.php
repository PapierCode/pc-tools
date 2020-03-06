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

function pc_svg( $index, $color = false, $class = false, $hidden = true ) {

	global $sprite; // cf. images/sprite.php
	$svg = $sprite[$index];

	// no print
	$svg = str_replace('<svg', '<svg class="no-print"', $svg);

	// couleur
	if ( $color ) {	$svg = str_replace('fill="#fff"', 'fill="'.$color.'"', $svg); }

	// aria hidden
	if ( $hidden ) { $svg = str_replace('<svg', '<svg aria-hidden="true"', $svg); }

	// css
	if ( $class ) { $svg = str_replace('class="no-print"', 'class="no-print '.$class.'"', $svg); }

	return $svg;

}

/*=====  FIN Get SVG  =====*/

/*=================================
=            Get image            =
=================================*/

/*----------  Par ID  ----------*/

function pc_get_img( $id, $size, $return = 'img', $class = '' ) {

	$imgAttr = wp_get_attachment_image_src($id,$size);
	$imgAlt = get_post_meta($id, '_wp_attachment_image_alt', true);

    switch ($return) {
        case 'img':
            return '<img src="'.$imgAttr[0].'" class="'.$class.'" width="'.$imgAttr[1].'" height="'.$imgAttr[2].'" alt="'.esc_attr($imgAlt).'"/>';
            break;
        case 'datas':
            $datas = $imgAttr;
            $datas[3] = $imgAlt;
            return $datas;
            break;
    }

}


/*=====  FIN Get image  =====*/

/*========================================
=            Sprite PHP to JS            =
========================================*/

function pc_sprite_to_js() {

	global $sprite;

	$sprite_js = array(
		'arrow' => $sprite['arrow'],
		'cross' => $sprite['cross'],
	);

	$sprite_js = apply_filters( 'pc_filter_sprite_js', $sprite_js );
	
	echo '<script>var sprite = '.json_encode($sprite_js, JSON_PRETTY_PRINT).'</script>';

}


/*=====  FIN Sprite PHP to JS  =====*/