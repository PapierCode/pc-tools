<?php

/*
Plugin Name: [PC] Tools
Plugin URI: www.papier-code.fr
Description: Boite à outils Papier Codé
Version: 1.0.2
Author: Papier Codé
*/


/*----------  Fonctions  ----------*/

include 'functions/pc-fn-dev.php';
include 'functions/pc-fn-get-page.php';
include 'functions/pc-fn-text.php';
include 'functions/pc-fn-date.php';
include 'functions/pc-fn-image.php';
include 'functions/pc-fn-pager.php';

// anciennes versions
include 'functions/pc-fn-old.php';


/*----------  Classes  ----------*/

include 'classes/pc-class-add-custom-post.php';
include 'classes/pc-class-add-metabox.php';
include 'classes/pc-class-add-field-to-tax.php';
include 'classes/pc-class-add-custom-admin.php';
include 'classes/pc-class-add-recaptcha.php';


/*----------  JS & CSS  ----------*/

add_action( 'admin_enqueue_scripts', function () {

    // scripts pour admin
    wp_enqueue_script( 'tools-scripts', plugin_dir_url( __FILE__ ).'pc-tools-script.js' );

});
