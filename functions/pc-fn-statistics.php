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
	
	echo '<script>var _paq=window._paq=window._paq||[];_paq.push(["trackPageView"]),_paq.push(["enableLinkTracking"]),function(){var a="https://analytics.papier-code.fr/";_paq.push(["setTrackerUrl",a+"matomo.php"]),_paq.push(["setSiteId","'.$id.'"]);var e=document,p=e.createElement("script"),t=e.getElementsByTagName("script")[0];p.async=!0,p.src=a+"matomo.js",t.parentNode.insertBefore(p,t)}();</script>';

}



/*=====  FIN Google  =====*/