/**
*
* [PC] Tools : fonctions javascript pour l'admin WP
*
* * Images upload
* * Pdf upload
* * Date Picker
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
        btn.remove();
    });

} // FIN pc_media_remove()

// attache la fonction aux btns déjà présents
$('.pc-media-remove').each(function() { $(this).click(function() { pc_media_remove( $(this) ); }); });


/*----------  IMG upload  ----------*/

if ( $('.pc-img-select').length > 0 ) {

    /*----------  sélection  ----------*/  

    var imgUploader; // modale (objet)

    $('.pc-img-select').each(function() {

        $(this).click(function() {

            var $btnSelect      = $(this),                          // bouton ajouter/modifier
                $container      = $(this).parent(),                 // conteneur direct
                $hiddenField    = $container.find('.pc-media-id');    // champ caché qui transmet à la bdd

            // si la modal a déjà été ouverte : réutilisation et sortie de la fonction
            if ( imgUploader ) { imgUploader.open(); return; }


            /*----------  Création de l'objet modale  ----------*/
            
            var imgUploader = new wp.media.view.MediaFrame.Select({

                // titre de la modal
                title: 'Insérer une image',
                // caractéristiques
                library: {
                    // order : 'ASC', 'DESC'
                    order: 'DESC',
                    // orderby : 'name', 'author', 'date', 'title', 'modified', 'uploadedTo', 'id', 'post__in', 'menuOrder'
                    orderby: 'date',
                    // type mime
                    type: 'image'
                },
                button: {
                    // texte du bouton de la modal
                    text: 'Insérer une image'
                },
                // droit de sélectionner plusieurs images
                multiple: false

            }); // FIN new wp.media


            /*----------  Au clic sur le bouton de validation de la modal  ----------*/
            
            imgUploader.on('select', function() {

                // datas de l'image sélectionnée
                var imgDatas = imgUploader.state().get('selection').first().toJSON();

                // si format valide
                if ( imgDatas.subtype === 'jpeg' || imgDatas.subtype === 'jpg' || imgDatas.subtype === 'png' ) {

	                // mise à jour du champ caché
	                $hiddenField.val(imgDatas.id);

                    // si une preview existe déjà
	                if ( $container.find('.pc-media-preview').length > 0 ) {

                        // modification de l'attribut src
	                    $container.find('.pc-media-preview').attr('src', imgDatas.sizes.thumbnail.url);

                    // si pas de preview
	                } else {

                        // ajoute la preview
	                	$container.prepend('<img class="pc-media-preview" src="'+imgDatas.sizes.thumbnail.url+'" />');

                        // si suppression autorisée
                        if ( $btnSelect.data('remove') == 'active') {

                            // ajoute le btn
     	                	$container.append('<input class="button pc-media-remove" type="button" value="Supprimer"/>');
                            // au clic sur le nouveau btn de suppression
    	                	$container.find('.pc-media-remove').click(function() { pc_media_remove( $(this) ); });

                        } // FIN if btn suppression

	                } // FIN if preview 

	                // si erreur affichée précédemment
	                $container.find('p').remove();

                // si format non valide
	            } else {

	            	$container.append('<p style="color:red">Le format de fichier est invalide</p>');

	            } // FIN if format

            }); // FIN imgUploader.on(select)


            /*----------  ouverture de la modal  ----------*/
            
            imgUploader.open();


        }); // FIN $(this).click()

    }); // FIN $('.pc-img-select').each()

} // FIN if $('.pc-img-select')


/*----------  PDF upload  ----------*/

if ( $('.pc-pdf-select').length > 0 ) {

    /*----------  sélection  ----------*/  

    var pdfUploader; // modale (objet)

    $('.pc-pdf-select').each(function() {

        $(this).click(function() {

            var $btnSelect      = $(this),                          // bouton ajouter/modifier
                $container      = $(this).parent(),                 // conteneur direct
                $hiddenField    = $container.find('.pc-media-id');  // champ caché qui transmet à la bdd

            // si la modal a déjà été ouverte : réutilisation et sortie de la fonction
            if (pdfUploader) { pdfUploader.open(); return; }


            /*----------  création de l'objet modale  ----------*/

            var pdfUploader = new wp.media.view.MediaFrame.Select({

                // titre de la modal
                title: 'Insérer un pdf',
                // caractéristiques
                library: {
                    // order : 'ASC', 'DESC'
                    order: 'DESC',
                    // orderBy : 'name', 'author', 'date', 'title', 'modified', 'uploadedTo', 'id', 'post__in', 'menuOrder'
                    orderby: 'date',
                    // type mime
                    type: 'application/pdf'
                },
                button: {
                    // texte du bouton de la modale
                    text: 'Insérer un pdf'
                },
                // droit de sélectionner plusieurs images
                multiple: false 

            }); // FIN new wp.media

            
            /*----------  Au clic sur le bouton de validation de la modal  ----------*/

            pdfUploader.on('select', function() {

                // datas de l'image sélectionnée
                var pdfDatas = pdfUploader.state().get('selection').first().toJSON();

                // si format valide
                if ( pdfDatas.subtype === 'pdf' ) {

                    // mise à jour du champ caché
                    $hiddenField.val(pdfDatas.id);

                    // si une preview existe déjà
                    if ( $container.find('.pc-media-preview').length > 0 ) {

                        // modification de l'attribut src
                        $container.find('.pc-media-preview').attr('href', pdfDatas.url);

                    // si pas de preview
                    } else {

                        // ajoute la preview
                        $container.prepend('<a class="pc-media-preview" href="'+pdfDatas.url+'" target="_blank">Voir le fichier actuel</a>');

                        // si suppression autorisée
                        if ( $btnSelect.data('remove') == 'active') {

                            // ajoute le btn
                            $container.append('<input class="button pc-media-remove" type="button" value="Supprimer"/>');
                            // au clic sur le nouveau btn de suppression
                            $container.find('.pc-media-remove').click(function() { pc_media_remove( $(this) ); });

                        } // FIN if btn suppression

                    } // FIN if preview 

                    // si erreur affichée précédemment
                    $container.find('p').remove();

                // si format non valide
                } else {

                    $container.append('<p style="color:red">Le format de fichier est invalide</p>');

                } // FIN if format

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
    	monthNamesShort: [ "janv.", "févr.", "mars", "avr.", "mai", "juin",	"juil.", "août", "sept.", "oct.", "nov.", "déc." ],
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

// ajouter au champ : class="pc-counter" data-max-length="XXX" data-counter-type="signs/words"

if ( $('.pc-counter').length > 0 ) {

    function counterResult(type,txt) {

        var result;

        switch(type) {
            case 'signs' :
                result = txt.length;
                break;
            case 'words' :
                result = txt.trim().split(' ').length;
                break;
        }

        return result;

    }

    $('.pc-counter').each(function() {

        var maxLength = $(this).data('max-length'),
            savedLength = counterResult( $(this).data('counter-type'), $(this).val() ),
            descAttr = savedLength > maxLength ? 'style="color:red"' : 'style="color:green"';

        $(this).after('<p class="description" '+ descAttr +'><span class="pc-counter">'+ savedLength +'</span> / '+ maxLength +' mots affichés.</p>');

        $(this).on('keyup mouseout', function() {
            var current = counterResult( $(this).data('counter-type'), $(this).val() ),
                $message = $(this).next();

            if ( current > maxLength ) {
                $message.css('color', 'red');
            } else {
                $message.css('color', 'green');
            }

            $(this).next().children().text( current );

        });
        
    });

}


/*=====  FIN Compteurs  ======*/
	
}); // End document.ready