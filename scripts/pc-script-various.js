jQuery(document).ready(function($){

/*======================================
=            Select couleur            =
======================================*/

var $selectColor = $('.pc-color-picker');

if ( $selectColor.length > 0 ) {

    $selectColor.change( function() {

        if ( $(this).val() !== '' ) { 
            $(this).css('background-color',$(this).val());
        } else {
            $(this).css('background-color','#fff');
        }

    });

}


/*=====  FIN Select couleur  =====*/

/*=================================
=            Compteurs            =
=================================*/

// ajouter au champ : class="pc-counter" data-counter-max="XXX"

var $pcCounter = $('.pc-counter');

if ( $pcCounter.length > 0 ) {

    $pcCounter.each(function() {

        var maxLength = $(this).data('counter-max'), // attribut data
            savedLength = $(this).val().length, // longueur chaine enregistrée
            descAttr = savedLength > maxLength ? 'style="color:red"' : 'style="color:green"'; // vérif chaine enregistrée

        // création du message
        $(this).after('<p class="description" '+ descAttr +'><span class="pc-counter-msg">'+ savedLength +'</span> / '+ maxLength +' '+ 'caractères conseillés' +'.</p>');

        // événements clavier/sourie
        $(this).on('keyup mouseout', function() {

            var current = $(this).val().length, // longueur chaine enregistrée
                $message = $(this).next(); // message relatif au champ

            // vérif chaine
            if ( current > maxLength ) {
                $message.css('color', 'red');
            } else {
                $message.css('color', 'green');
            }

            // affichage
            $(this).next().children().text( current );

        });
        
    });

}


/*=====  FIN Compteurs  ======*/

/*===========================================
=            Checkboxes required            =
===========================================*/

var $required_checkboxes = $('.pc-checkboxes-required'); // container

// fonction qui vérifie si une des checkboxe est cochée
var pc_checkboxes_required = function( $target ) {

    if ( $target.filter(':checked').length > 0 ) {
        $target.prop('required',false);
    } else {
        $target.prop('required',true);
    }

};

if ( $required_checkboxes.length > 0 ) {

    $required_checkboxes.each(function() {
            
        var $all = $(this).find('input');
        pc_checkboxes_required($all); // au chargement de la page
        $all.on('click', function() { // à chaque clic sur une checkboxe
            pc_checkboxes_required($all);            
        });

    });

}


/*=====  FIN Checkboxes required  =====*/
    
}); // End document.ready