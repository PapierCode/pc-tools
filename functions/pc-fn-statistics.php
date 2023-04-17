<?php
/**
 * 
 * Statistiques
 * 
 */

/*==============================
=            Matomo            =
==============================*/

function pc_display_tag_matomo( $id ) {

	echo '<script>var _paq = window._paq || [];_paq.push(["trackPageView"]);_paq.push(["enableLinkTracking"]);(function(){var u="https://analytics.papier-code.fr/";_paq.push(["setSecureCookie", true]);_paq.push(["setTrackerUrl", u+"matomo.php"]);_paq.push(["setSiteId", "'.$id.'"]);_paq.push(["HeatmapSessionRecording::disable"]);var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0];g.type="text/javascript"; g.async=true; g.defer=true; g.src=u+"matomo.js"; s.parentNode.insertBefore(g,s);})();</script>';

}


/*=====  FIN Matomo  =====*/

/*==============================
=            Google            =
==============================*/

function pc_display_tag_analytics( $id ) {
	
	echo '<script async src="https://www.googletagmanager.com/gtag/js?id='.$id.'"></script><script>window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);} gtag("js", new Date());gtag("config", "'.$id.'");</script>';

}



/*=====  FIN Google  =====*/