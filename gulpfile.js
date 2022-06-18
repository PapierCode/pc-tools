/**
*
* Gulp pour Papier Codé
*
** dépendance : package.json
** installation : commande "npm install"
*
**/


/*======================================
=            Initialisation            =
======================================*/

// Chargement des plugins

const { src, dest, watch, series } = require( 'gulp' ); // base

const jshint		= require( 'gulp-jshint' ); // recherche d'erreurs js
const concat		= require( 'gulp-concat' ); // empile plusieurs fichiers js en un seul
const terser		= require( 'gulp-terser' ); // minification js

    
/*=====  FIN Initialisation  ======*/

/*================================
=            Tâche JS            =
================================*/

function js_hint() {

	return src( 'scripts/*.js' )
        .pipe(jshint( { esnext:true, browser:true } ))
        .pipe(jshint.reporter( 'default' ));

}

function js() {

    return src( 'scripts/*.js' )
        .pipe( concat( 'pc-tools-scripts.js' ) )
        .pipe( terser() )
        .pipe( dest( './' ) );

}


/*=====  FIN Tâche JS  =====*/

/*==================================
=            Monitoring            =
==================================*/

exports.watch = function() {
	watch( [ 'scripts/*.js' ], series(js_hint,js)  )
};


/*=====  FIN Monitoring  ======*/