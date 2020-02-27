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

function pc_get_pager( $query = FALSE, $current = FALSE, $classCss = '', $svg = array('arrow','','svg-block') ) {

    $pagination = array(
        'mid_size'				=> 0,
        'next_text' 			=> '<span class="visually-hidden">Suivant</span>'.pc_svg($svg[0],$svg[1],$svg[2]),
        'prev_text' 			=> '<span class="visually-hidden">Précédent</span>'.pc_svg($svg[0],$svg[1],$svg[2]),
        'type' 					=> 'array',
        'before_page_number' 	=> '<span class="visually-hidden">Page </span>',
        'format'                => '?paged=%#%#main',
    );

    if ( $query && $current ) {

        $pagination['total'] = $query->max_num_pages;
        $pagination['current'] = $current;

    }

    // tableau contenant chaque élément (liens et '...')
    $pagesList = paginate_links($pagination);

    // affichage
    if ( isset($pagesList) && count($pagesList) > 0 ) {
		
		$pagerOldClass = array('page-numbers', 'prev', 'current', 'dots', 'next');
		$pagerNewClass = array('pager-link', 'pager-link--prev', 'is-active', 'pager-dots', 'pager-link--next');

		$pager = '<ul class="pager-list reset-list no-print '.$classCss.'">';
        foreach ($pagesList as $page) {
            $page = str_replace($pagerOldClass, $pagerNewClass, $page);
            $page = str_replace('aria-is-active', 'aria-current', $page);
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

// filtres pour changer les classes des liens
add_filter('next_post_link', 'post_link_attributes');
add_filter('previous_post_link', 'post_link_attributes');

	function post_link_attributes($datas) {
	    $class = 'class="the-class"';
	    return str_replace('<a href=', '<a '.$class.' href=', $datas);
	}

function pc_post_navigation($prevTxt = '<span>Article </span>Précédent', $nextTxt = '<span>Article </span>Suivant', $parent = '../' ) {

	$pagination = '<ul class="pager pager-prevnext reset-list">';

	// construction du lien précédent
	$prevObject = get_previous_post();
	if( $prevObject != '' ) {

		$prevTitle 		= $prevObject->post_title;
		$prevUrl 		= get_permalink($prevObject->ID);
		$prevLink 		= '<a href="'.$prevUrl.'" class="pager-link pager-link-prev" title="'.$prevTitle.'"><span>'.$prevTxt.'</span></a>';
		$pagination 	.= '<li class="pager-item">'.$prevLink.'</li>';

	}

	// retour liste
	$pagination .= '<li class="pager-item"><a href="'.$parent.'" class="pager-link parger-link-back" title="Retour à la liste"><span>Retour</span></a></li>';

	// construction du lien suivant
	$nextObject = get_next_post();
	if( $nextObject != '' ) {

		$nextTitle 		= $nextObject->post_title;
		$nextUrl 		= get_permalink($nextObject->ID);
		$nextLink 		= '<a href="'.$nextUrl.'" class="pager-link pager-link-next" title="'.$nextTitle.'"><span>'.$nextTxt.'</span></a>';
		$pagination 	.= '<li class="pager-item">'.$nextLink.'</li>';

	}

	$pagination .= '</ul>';
	echo $pagination;

}


/*=====  FIN Navigation  =====*/