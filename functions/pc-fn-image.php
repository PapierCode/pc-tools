<?php
/**
 * 
 * Fonctions utiles aux images
 * 
 ** Get SVG
 ** Get image
 * 
 */

/*====================================
=            Filtrage SVG            =
====================================*/

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

/*=====  FIN Filtrage SVG  =====*/

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


/*----------  Thumbnail par défaut  ----------*/

function pc_get_default_st( $class = '', $return = 'img' ) {

    global $images_project_sizes; // déclarée dans le thème
    $stDefault = array(
        get_bloginfo('template_directory').'/images/thumb-default.jpg',
        $images_project_sizes['st']['width'],
        $images_project_sizes['st']['height']
    );

    $stDefault = apply_filters( 'pc_filter_default_st', $stDefault );

    switch ($return) {
        case 'img':
            return '<img src="'.$stDefault[0].'" alt="" width="'.$stDefault[1].'" height="'.$stDefault[2].'" class="'.$class.'" />';
            break;
        case 'datas':
            return $stDefault;
            break;
    }

}


/*=====  FIN Get image  =====*/