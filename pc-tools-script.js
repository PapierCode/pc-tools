/**
*
* [PC] Tools : fonctions javascript pour l'admin WP
*
* * Images upload
* * Gallery Upload
* * Pdf upload
* * Date Picker
* * Compteur
*
**/


jQuery(document).ready(function($){

/*====================================
=            media upload            =
====================================*/

/*----------  Communs  ----------*/
    
function pc_media_remove(btn){

    // vide le champ caché
    btn.prevAll('.pc-media-id').val('');
    // supprime l'image et le btn de suppression après animation
    btn.prevAll('.pc-media-preview').slideUp('500', function() {
        $(this).remove();
        btn.prev('.button').val('Ajouter');
        btn.remove();
    });

} // FIN pc_media_remove()

// attache la fonction aux btns déjà présents
$('.pc-media-remove').each(function() { $(this).click(function() { pc_media_remove( $(this) ); }); });


/*----------  Image upload  ----------*/

if ( $('.pc-img-select').length > 0 ) {

    $('.pc-img-select').each(function() {

        $(this).click(function() {

            var $btnSelect      = $(this),                              // bouton ajouter/modifier
                $container      = $(this).parent(),                     // conteneur parent
                $hiddenField    = $container.find('.pc-media-id');      // champ caché qui transmet à la bdd

            // si la modal est déjà active
            if ( imgUploader ) { imgUploader.open(); return; }


            /*----------  Création de l'objet modale  ----------*/
            
            var imgUploader = wp.media({

                title: 'Insérer une image',
                library: { type: 'image' },
                button: { text: 'Insérer une image' },
                multiple: false

            }); // FIN wp.media


            /*----------  Au clic sur le bouton de validation de la modal  ----------*/
            
            imgUploader.on('select', function() {

                // datas de l'image sélectionnée
                var imgDatas = imgUploader.state().get('selection').first().toJSON();

                // mise à jour du champ caché
                $hiddenField.val(imgDatas.id);
                var imgUrl = imgDatas.sizes.hasOwnProperty('thumbnail') ? imgDatas.sizes.thumbnail.url : imgDatas.url ;

                // si une preview existe déjà
                if ( $container.find('.pc-media-preview').length > 0 ) {

                    // modification de la preview
                    $container.find('.pc-media-preview').html('<div class="pc-media-preview-item" style="background-image:url('+imgUrl+');"></div>');

                // si pas encore de preview
                } else {
                    // ajoute la preview
                    $container.prepend('<div class="pc-media-preview"><div class="pc-media-preview-item" style="background-image:url('+imgUrl+');"></div></div>');
                    // texte du bouton
                    $btnSelect.val('Modifier');

                    // si suppression autorisée
                    if ( $btnSelect.data('remove') == 'active') {

                        // ajoute le btn
                        $btnSelect.after('<input class="button pc-media-remove" data-cible="image" type="button" value="Supprimer"/>');
                        // au clic sur le nouveau btn de suppression
                        $container.find('.pc-media-remove').click(function() { pc_media_remove( $(this) ); });

                    } // FIN if btn suppression

                } // FIN if preview 

            }); // FIN imgUploader.on(select)


            /*----------  ouverture de la modal  ----------*/
            
            imgUploader.open();


        }); // FIN $(this).click()

    }); // FIN $('.pc-img-select').each()

} // FIN if $('.pc-img-select')


/*----------  Gallerie upload  ----------*/

if ( $('.pc-gallery-select').length > 0 ) {

    $('.pc-gallery-select').each(function() {

        $(this).click(function() {

            var $btnSelect      = $(this),                                      // bouton ajouter/modifier
                $container      = $(this).parent(),                             // conteneur parent
                $hiddenField    = $container.find('.pc-media-id'),            // champ caché qui transmet à la bdd
                imgIds          = $hiddenField.val();                           // contenu du champ caché
                gallery_state   = imgIds ? 'gallery-edit' : 'gallery-library';  // menu activé par défaut dans la modal (modifier ou créer)

            // si la modal a déjà été ouverte : réutilisation et sortie de la fonction
            if ( galleryUploader ) { galleryUploader.open(); return; }


            /*----------  Création de l'objet modal  ----------*/
            
            var galleryUploader = wp.media({

                title: 'Insérer une gallerie',
                button: { text: 'Insérer une gallerie' },
                library: { type: 'image' },
                frame: "post",
                state: gallery_state,
                multiple: true,

            }); // FIN wp.media


            /*----------  À l'ouverture de la modal  ----------*/
            
            galleryUploader.on( 'open', function() {
                
                // contenu du champ caché
                imgIds = $hiddenField.val();
                // si vide on sort de la fonction
                if ( !imgIds ) { return; }

                // conversion en tableau
                imgIds = imgIds.split( ',' );
                // récupère la propriété "librairie" de la modal qui s'ouvre
                var library = galleryUploader.state().get('library');

                // ajoute chaque image à la modal qui va regénérer une gallerie modifiable               
                imgIds.forEach( function( id ) {
                    // là je pige pas...
                    attachment = wp.media.attachment(id);
                    attachment.fetch();
                    library.add( attachment ? [ attachment ] : [] );
                } );

            } );


            /*----------  Au clic sur le bouton de validation de la modal  ----------*/

            galleryUploader.on('update', function() {

                // récupère les données renvoyées par la modal
                // un tableau d'objets
                var galleryDatas = galleryUploader.state().get('library').toJSON();

                // mise à jour du champ caché
                var imgIdsToSave = [],
                    galleryPreview = '',
                    imgUrl;
                for (var i = 0; i < galleryDatas.length; i++) {
                    imgIdsToSave.push(galleryDatas[i].id);
                    imgUrl = galleryDatas[i].sizes.hasOwnProperty('thumbnail') ? galleryDatas[i].sizes.thumbnail.url : galleryDatas[i].url ;
                    galleryPreview += '<div class="pc-media-preview-item" style="background-image:url('+imgUrl+');"></div>';
                }
                $hiddenField.val(imgIdsToSave.join());

                // si une preview existe déjà
                if ( $container.find('.pc-media-preview').length > 0 ) {

                    $container.find('.pc-media-preview').html(galleryPreview);

                // si pas encore de preview
                } else {

                    // ajoute la preview
                    $container.prepend('<div class="pc-media-preview">'+galleryPreview+'</div>');
                    // texte du bouton
                    $btnSelect.val('Modifier');

                    // si suppression autorisée
                    if ( $btnSelect.data('remove') == 'active') {

                        // ajoute le btn
                        $btnSelect.after('<input class="button pc-media-remove" data-cible="gallery" type="button" value="Supprimer"/>');
                        // au clic sur le nouveau btn de suppression
                        $container.find('.pc-media-remove').click(function() { pc_media_remove( $(this) ); });

                    } // FIN if btn suppression

                }

            }); // FIN galleryUploader.on(select)

            galleryUploader.open();


        }); // FIN $(this).click()

    }); // FIN $('.pc-gallery-select').each()

} // FIN if $('.pc-gallery-select')


/*----------  PDF upload  ----------*/

if ( $('.pc-pdf-select').length > 0 ) {

    $('.pc-pdf-select').each(function() {

        $(this).click(function() {

            var $btnSelect      = $(this),                          // bouton ajouter/modifier
                $container      = $(this).parent(),                 // conteneur direct
                $hiddenField    = $container.find('.pc-media-id');  // champ caché qui transmet à la bdd

            // si la modal a déjà été ouverte : réutilisation et sortie de la fonction
            if (pdfUploader) { pdfUploader.open(); return; }


            /*----------  création de l'objet modale  ----------*/

            var pdfUploader = wp.media({

                title: 'Insérer un pdf',
                library: { type: 'application/pdf' },
                button: { text: 'Insérer un pdf' },
                multiple: false 

            }); // FIN wp.media

            
            /*----------  Au clic sur le bouton de validation de la modal  ----------*/

            pdfUploader.on('select', function() {

                // datas de l'image sélectionnée
                var pdfDatas = pdfUploader.state().get('selection').first().toJSON();

                // mise à jour du champ caché
                $hiddenField.val(pdfDatas.id);

                // si une preview existe déjà
                if ( $container.find('.pc-pdf-preview').length > 0 ) {

                    // modification de l'attribut src
                    $container.find('.pc-pdf-preview').attr('href', pdfDatas.url);

                // si pas de preview
                } else {

                    // ajoute la preview
                    $container.prepend('<div class="pc-media-preview"><a class="pc-pdf-preview" href="'+pdfDatas.url+'" target="_blank">Voir le fichier actuel</a></div>');
                    // texte du bouton
                    $btnSelect.val('Modifier');

                    // si suppression autorisée
                    if ( $btnSelect.data('remove') == 'active') {

                        // ajoute le btn
                        $btnSelect.after('<input class="button pc-media-remove" type="button" value="Supprimer"/>');
                        // au clic sur le nouveau btn de suppression
                        $container.find('.pc-media-remove').click(function() { pc_media_remove( $(this) ); });

                    } // FIN if btn suppression

                } // FIN if preview 

                // si erreur affichée précédemment
                $container.find('p').remove();

            }); // FIN pdfUploader.on(select)


            /*----------  ouverture de la modal  ----------*/

            pdfUploader.open();


        }); // $(this).click()

    }); // $('.pc-pdf-select').each()

} // FIN if $('.pc-pdf-select')


/*=====  End of media upload  ======*/

/*===================================
=            Date Picker            =
===================================*/

if ( $('.pc-date-picker').length > 0 ) {

    $('.pc-date-picker').datepicker({

        closeText: "Fermer",
        prevText: "Précédent",
        nextText: "Suivant",
        currentText: "Aujourd'hui",
        monthNames: [ "janvier", "février", "mars", "avril", "mai", "juin", "juillet", "août", "septembre", "octobre", "novembre", "décembre" ],
        monthNamesShort: [ "janv.", "févr.", "mars", "avr.", "mai", "juin", "juil.", "août", "sept.", "oct.", "nov.", "déc." ],
        dayNames: [ "dimanche", "lundi", "mardi", "mercredi", "jeudi", "vendredi", "samedi" ],
        dayNamesShort: [ "dim.", "lun.", "mar.", "mer.", "jeu.", "ven.", "sam." ],
        dayNamesMin: [ "D","L","M","M","J","V","S" ],
        weekHeader: "Sem.",
        dateFormat: "dd MM yy",
        firstDay: 1,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ""

    });

}


/*=====  End of Date Picker  ======*/

/*=================================
=            Compteurs            =
=================================*/

// ajouter au champ : class="pc-counter" data-counter-max="XXX"

if ( $('.pc-counter').length > 0 ) {

    $('.pc-counter').each(function() {

        var maxLength = $(this).data('counter-max'), // attribut data
            savedLength = $(this).val().length, // longueur chaine enregistrée
            descAttr = savedLength > maxLength ? 'style="color:red"' : 'style="color:green"'; // vérif chaine enregistrée

        // création du message
        $(this).after('<p class="description" '+ descAttr +'><span class="pc-counter">'+ savedLength +'</span> / '+ maxLength +' '+ 'caractères conseillés' +'.</p>');

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
    
}); // End document.ready