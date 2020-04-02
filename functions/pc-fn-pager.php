<?php
/**
 * 
 * Pager
 * Navigation
 * 
 */


/*=============================
=            Pager            =
=============================*/

/**
 * 
 * @param object	$query		Requête WP custom
 * @param integer	$current	Numéro de page courante
 * @param string	$css		Classes CSS supplémentaires
 * @param array		$svg		Voir pc_svg()
 * 
 */

function pc_get_pager( $query = '', $current = '', $css = '', $svg = array( 'arrow', '', 'svg-block' ) ) {

    $pagination = array(
        'mid_size'				=> 0,
        'next_text' 			=> '<span class="visually-hidden">Suivant</span>'.pc_svg( $svg[0], $svg[1], $svg[2] ),
        'prev_text' 			=> '<span class="visually-hidden">Précédent</span>'.pc_svg( $svg[0], $svg[1], $svg[2] ),
        'type' 					=> 'array',
        'before_page_number' 	=> '<span class="visually-hidden">Page </span>',
        'format'                => '?paged=%#%#main',
    );

    if ( is_object($query) && $current != '' ) {

        $pagination['total'] = $query->max_num_pages;
        $pagination['current'] = $current;

    }

    // tableau contenant chaque élément (liens et '...')
    $list = paginate_links( $pagination );

    // affichage
    if ( isset($list) && count($list) > 0 ) {
		
		$css_old = array( 'page-numbers', 'prev', 'current', 'dots', 'next' );
		$css_new = array( 'pager-link', 'pager-link--prev', 'is-active', 'pager-dots', 'pager-link--next' );

		$pager = '<ul class="pager-list reset-list no-print '.$css.'">';

        foreach ($list as $page) {

            $page = str_replace( $css_old, $css_new, $page );
            $page = str_replace( 'aria-is-active', 'aria-current', $page );
			$pager .= '<li class="pager-item">'.$page.'</li>';
			
		}
		
        $pager .= '</ul>';

        echo $pager;

    }

}


/*=====  FIN Pager  =====*/

/*==================================
=            Navigation            =
==================================*/

/**
 * 
 * @param string	$prev_inner		Contenu du lien précédent
 * @param string	$next_inner 	Contenu du lien suivant
 * @param string	$parent			Url de la page parent
 * 
 */

function pc_post_navigation( $prev_inner = '<span>Article </span>Précédent', $next_inner = '<span>Article </span>Suivant', $parent = '../' ) {

	$pagination = '<ul class="pager pager-prevnext reset-list">';

	// construction du lien précédent
	$prev_object = get_previous_post();

	if( is_object($prev_object) ) {

		$prev_title 	= $prev_object->post_title;
		$prev_url 		= get_permalink( $prev_object->ID );
		$prev_link 		= '<a href="'.$prev_url.'" class="pager-link pager-link-prev" title="'.$prev_title.'"><span>'.$prev_inner.'</span></a>';

		$pagination 	.= '<li class="pager-item">'.$prev_link.'</li>';

	}

	// retour liste
	$pagination .= '<li class="pager-item"><a href="'.$parent.'" class="pager-link parger-link-back" title="Retour à la liste"><span>Retour</span></a></li>';

	// construction du lien suivant
	$next_object = get_next_post();

	if( is_object($next_object) ) {

		$next_title 	= $next_object->post_title;
		$next_url 		= get_permalink( $next_object->ID );
		$next_link 		= '<a href="'.$next_url.'" class="pager-link pager-link-next" title="'.$next_title.'"><span>'.$next_inner.'</span></a>';

		$pagination 	.= '<li class="pager-item">'.$next_link.'</li>';

	}

	$pagination .= '</ul>';

	echo $pagination;

}


/*=====  FIN Navigation  =====*/