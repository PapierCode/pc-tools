<?php
/*
Plugin Name: [PC] Tools
Plugin URI: www.papier-code.fr
Description: Boite à outils Papier Codé
Version: 1.11.2
Author: Papier Codé
*/


/*----------  Fonctions  ----------*/

include 'functions/pc-fn-dev.php';			// outils de développement
include 'functions/pc-fn-statistics.php';	// script Matomo ou Google Analytics
include 'functions/pc-fn-get-page.php';		// rechercher une page par sa spécificité
include 'functions/pc-fn-text.php';			// manipuler des textes
include 'functions/pc-fn-date.php';			// manipuler des dates
include 'functions/pc-fn-image.php';		// rechercher une image par son ID, manipuler du svg
include 'functions/pc-fn-pager.php';		// afficher une pagination ou une navigation entre article

include 'functions/pc-fn-old.php';			// comptabilité


/*----------  Classes  ----------*/

include 'classes/pc-class-add-custom-post.php';		// création d'un post et/ou d'une taxonomie
include 'classes/pc-class-add-metabox.php';			// création de métaboxes aux posts
include 'classes/pc-class-add-field-to-tax.php';	// création de métaboxes aux taxonomies
include 'classes/pc-class-add-custom-admin.php';	// création d'une page d'administration
include 'classes/pc-class-add-hcaptcha.php';		// création hcaptcha 
include 'classes/pc-class-add-math-captcha.php';	// création captcha mathématique 


/*----------  Javascript  ----------*/

add_action( 'admin_enqueue_scripts', function ( $hook ) {
	
	if ( 'user-new.php' == $hook ) { return; }

    // scripts utiles aux fonctions et classes ci-dessus
	wp_enqueue_script( 'pc-tools-scripts', plugin_dir_url( __FILE__ ).'pc-tools-script.js' );
	wp_enqueue_media();

});
