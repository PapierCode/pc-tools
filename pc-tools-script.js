/**
*
* [PC] Tools : fonctions javascript pour l'admin WP
*
** Fonctions médias
** Medias
** Medias : galerie
** Medias : pdf
** Date Picker
** Compteur
** Checkboxes required
*
**/

/*========================================
=            Fonctions médias            =
========================================*/

/*----------  Suppression prévisualisation  ----------*/

function pc_media_remove( $button ){

    // vide le champ caché
    $button.prevAll('.pc-media-id').val('');
    // supprime la prévisualisation et le bouton de suppression après une animation
    $button.prevAll('.pc-media-preview').slideUp('500', function() {
        jQuery(this).empty().show();
        $button.prev('.pc-media-select').val('Ajouter');
        $button.remove();
    });

} // FIN pc_media_remove()


/*----------  Ouverture modale  ----------*/

function pc_media_modal( $button ) {

	var $container		= $button.parent(),                    		// conteneur parent
		$preview		= $container.find('.pc-media-preview'), 	// conteneur aperçu
		$field    		= $container.find('.pc-media-id'),      	// champ caché qui transmet à la bdd

		type			= $button.data('type'),		// type de media
		modal_params,								// paramètres de la modale
		modal;										// modale


	/*----------  Paramètre modale  ----------*/

	switch ( type ) {

		case 'img' :
			modal_params = {
				title: 'Insérer une image',
				library: { type: 'image' },
				button: { text: 'Insérer une image' },
				multiple: false
			}; 
			break;

		case 'pdf' :
			modal_params = {
				title: 'Insérer un pdf',
				library: { type: 'application/pdf' },
				button: { text: 'Insérer un pdf' },
				multiple: false 
			};
			break;

		case 'file' :
			modal_params = {
				title: 'Insérer un fichier',
				button: { text: 'Insérer un fichier' },
				multiple: false 
			};
			break;

		case 'audio' :
			modal_params = {
				title: 'Insérer un fichier audio',
				library: { type: 'audio/mpeg' },
				button: { text: 'Insérer un fichier audio' },
				multiple: false
			}; 
			break;

	}


	/*----------  Création de l'objet modale  ----------*/
	
	modal = wp.media( modal_params ); // FIN wp.media


	/*----------  Au clic sur le bouton de validation de la modal  ----------*/
	
	modal.on( 'select', function() {

		// datas du media sélectionnée
		var media_datas = modal.state().get('selection').first().toJSON();

		// mise à jour du champ caché
		$field.val(media_datas.id);

		// création preview inner
		var media_url, preview_inner;
		switch ( type ) {
			case 'img' :
				media_url = media_datas.sizes.hasOwnProperty('thumbnail') ? media_datas.sizes.thumbnail.url : media_datas.url ;
				preview_inner = '<div class="pc-media-preview-item" style="background-image:url(' + media_url + ');"></div>';
				break;
			case 'audio' :
				media_url = media_datas.url;
				preview_inner = '<audio class="pc-audio-preview" controls src="' + media_url + '"></audio>';
			case 'pdf' :
			default :
				media_url = media_datas.url;
				preview_inner = '<a class="pc-pdf-preview" href="' + media_url + '" target="_blank"><div class="dashicons dashicons-media-default"></div> Voir le fichier</a>';
		}

		// mise à jour preview
		$preview.html( preview_inner );

		// mise à jour texte du bouton
		$button.val('Modifier');

		// si suppression autorisée
		if ( $button.data('remove') == 'active' && $container.find('.pc-media-remove').length == 0 ) {
			// ajoute le bouton de suppression
			$button.after('<input class="button pc-media-remove" data-cible="image" type="button" value="Supprimer"/>');
			// au clic sur le nouveau bouton de suppression
			$container.find('.pc-media-remove').click(function() { pc_media_remove( jQuery(this) ); });
		} // FIN if btn suppression

	}); // FIN modal.on(select)


	/*----------  ouverture de la modal  ----------*/
	
	modal.open();

}


/*=====  FIN Fonctions médias  =====*/


jQuery(document).ready(function($){

/*===============================
=            Communs            =
===============================*/

var $body = $('body'),
	$pc_metaboxes = $('.pc-metabox');


/*=====  FIN Communs  ======*/

/*==============================
=            Médias            =
==============================*/

$body.on( 'click', '.pc-media-select[data-type]', function() {
    pc_media_modal( $(this) );
});
$body.on( 'click', '.pc-media-remove', function() {
    pc_media_remove( $(this) );
});


/*=====  FIN Médias  =====*/

/*========================================
=            Medias : galerie            =
========================================*/

var $pcGallerySelect = $('.pc-gallery-select');

if ( $pcGallerySelect.length > 0 ) {

	$pcGallerySelect.click( function() {

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
			// affichage
			$container.find('.pc-media-preview').html(galleryPreview);
			// texte du bouton
			$btnSelect.val('Modifier');

			// si suppression autorisée
			$btn_Remove = $container.find('.pc-media-remove');
			if ( 'active' == $btnSelect.data('remove') && 0 == $btn_Remove.length) {
				// ajoute le btn
				$btnSelect.after('<input class="button pc-media-remove" data-cible="gallery" type="button" value="Supprimer"/>');
				// au clic sur le nouveau btn de suppression
				$container.find('.pc-media-remove').click(function() { pc_media_remove( $(this) ); });
			} // FIN if btn suppression

		}); // FIN galleryUploader.on(select)

		galleryUploader.open();

	});

} // FIN if $pcGallerySelect


/*=====  FIN Medias : galerie  =====*/


/*=====  FIN Medias : pdf  =====*/

/*===================================
=            Date Picker            =
===================================*/

var $relatives_pickers = $pc_metaboxes.find( 'input[type="date"], input[type="time"], input[type="datetime-local"]' );

if ( $relatives_pickers.length > 0 ) {

	var relatives_pickers_add_min = function( from, to ) {	
		to.attr( 'min', from.val() );
	};

	$relatives_pickers.each( function() {

		if ( this.hasAttribute( 'data-after' ) ) {

			var $input_from = $( '#'+$(this).attr('data-after') ),
			$input_to = $(this);
			$(this).attr( 'min', $( '#'+$(this).attr('data-after') ).val() );

			if ( '' != $input_from.val() ) { relatives_pickers_add_min( $input_from, $input_to ); }

			$input_from.on( 'input', function() {
				relatives_pickers_add_min( $input_from, $input_to );
			});

		}

	});

} 


/*=====  End of Date Picker  ======*/

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

/*===================================
=            Modale lien            =
===================================*/

var wpLinkCibleId; // attribut id du input

// ouverture
$body.on( 'click', '.pc-link-select', function() {

    // ajout d'un classe pour du css
    $body.addClass('pc-modal-link');
    // input ciblé
    wpLinkCibleId = $(this).data('cible');
    // le script s'attend à un wysiwyg, faut le feinter
    wpActiveEditor = true;
    // ouvre la modale
    wpLink.open(wpLinkCibleId);

});

// validation
$body.on( 'click', '#wp-link-submit', function(event) {

    // ajout de l'url dans le champ
    $('#'+wpLinkCibleId).val(wpLink.getAttrs().href);
    // fermeture de la modale
    wpLink.close();
    // suppression class pour le css
    $body.removeClass('pc-link-modal');
    
});

// fermeture
$body.on( 'click', '#wp-link-cancel, #wp-link-backdrop, #wp-link-close', function(event) {

    // fermeture de la modale
    wpLink.close();
    // suppression class pour le css
    $body.removeClass('pc-link-modal');

});

/*=====  FIN Modale lien  ======*/

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

}

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

/*=======================================
=            Coordonnées GPS            =
=======================================*/

var $addressToGps = $('.pc-address-to-gps');

if ( $addressToGps.length > 0 ) {

	// init geocoding
	var geocoder = new google.maps.Geocoder();

	$addressToGps.each( function( index ) {

		/*----------  Variables  ----------*/	

		var $metabox = $(this), // table

		// champs
		$address = $metabox.find('.address'),
		$cp = $metabox.find('.cp'),
		$city = $metabox.find('.city'),
		$gpsLat = $metabox.find('.lat'),
		$gpdLng = $metabox.find('.lng'),

		// map
		mapId = 'map-'+index,
		latLngInitial, zoomInitial, map, marker;

		/*----------  Carte  ----------*/		
		
		// ajout container map
		$metabox.after( '<div id="'+mapId+'" style="height:300px;margin-right:10px"></div>' );
		// config map au chargement
		if ( $gpsLat.val() != '' && $gpdLng.val() != '' ) {
			latLngInitial = new google.maps.LatLng( $gpsLat.val(), $gpdLng.val() );
			zoomInitial = 15;
		} else {
			latLngInitial = new google.maps.LatLng( 46.8518280627862, 2.4293935625 );
			zoomInitial = 5;
		}
		// affichage map
		map = new google.maps.Map( document.getElementById( mapId ), {
			zoom: zoomInitial,
			center: latLngInitial,
			controlSize:30,
			streetViewControl: false,
			fullscreenControl: false,
			mapTypeControl: false,
			styles:[{
				featureType: 'poi',
				stylers: [{ visibility: 'off' }]
			}]
		});
		// création marqueur
		marker = new google.maps.Marker({
			position: latLngInitial,
			draggable:true
		});
		// affichage marqueur
		marker.setMap(map);
		// ajout événement fin de déplacement marqueur
		google.maps.event.addListener( marker, 'dragend', function(marker) {
			// position du marqueur
			var location = marker.latLng;
			// mise à jour inputs
			$gpsLat.val( location.lat() );
			$gpdLng.val( location.lng() );
			// recentrage map
			map.setCenter( location );
		});

		/*----------  Géocoding  ----------*/
		
		$metabox.find('button').click( function() {

			// champs obligatoires
			if ( $address.val() == '' ) { alert('Le champ Adresse est obligatoire.'); return; }
			if ( $cp.val() == '' ) { alert('Le champ Code Postal est obligatoire.'); return; }
			if ( $city.val() == '' ) { alert('Le champ Ville est obligatoire.'); return; }
			// adresse compléte
			addresseToConvert = $address.val()+' '+$cp.val()+' '+$city.val();
			
			// requête geocoding
			geocoder.geocode( { address: addresseToConvert }, ( results, status ) => {

				// résultat
				if ( status == 'OK' ) {
					// coordonnées
					var location = results[0].geometry.location;
					// mise à jour inputs
					$gpsLat.val( location.lat() ); 
					$gpdLng.val( location.lng() ); 
					// mise à jour position marqueur
					marker.setPosition(location);
					// recentrage map
					map.setCenter(location);
					// mise à jour zoom
					map.setZoom(15);

				// 0 résultat || erreur				
				} else if ( status == 'ZERO_RESULTS' ) {
					alert( 'L\'adresse n\'a pas été trouvée.');
				} else {
					alert( 'Une erreur est survenue, merci de le signaler à Papier Codé.');
				}

			});

		});

	});

}


/*=====  FIN Coordonnées GPS  =====*/

/*=====================================
=            Compatibilité            =
=====================================*/

/*----------  Date picker (jQuery)  ----------*/

var $pcDatePicker = $('.pc-date-picker');

if ( $pcDatePicker.length > 0 ) {

    $pcDateRemove = $('.pc-date-remove');

	var pc_on_pick_a_date = function() {
		if ( $(this).next('.pc-date-remove').length < 1 && !$(this).prop('required') ) {
			$(this).after('<button class="reset-btn pc-date-remove" type="button" title="Supprimer"><span class="dashicons dashicons-no-alt"></span></button>');
			$(this).next('.pc-date-remove').click(pc_date_remove);
		}
	}

	var pc_date_remove = function() {
		$(this).prev('.pc-date-picker').val('');
		$(this).remove();
	}

		$pcDatePicker.datepicker({

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
			yearSuffix: "",
			onSelect:pc_on_pick_a_date

		});

	if ( $pcDateRemove.length > 0 ) { $pcDateRemove.click(pc_date_remove); }

	var $pcDatePickerRequired = $pcDatePicker.filter('[required]');

	if ( $pcDatePickerRequired.length > 0 ) {

		$('form#post').submit(function(event){

			// custom field date
			var error = false, $fieldError = false;

			$pcDatePickerRequired.each(function() {
				if ( $(this).val() == '' ) {
					if ( !$(this).hasClass('pc-field-error') ) {
						$(this).addClass('pc-field-error').after('<p><em class="description pc-message-error">Ce champ est obligatoire.</em></p>');
					}
					dateRequired = true;
					if ( !error ) {
						error = true;
						$fieldError = $(this);
					}
				} else if ( $(this).hasClass('pc-field-error') ) {
					$(this).removeClass('pc-field-error').next('p').remove();
				}
			});
			
			if ( error ) {
				event.preventDefault();
				$('html, body').animate({ scrollTop: $fieldError.offset().top - 50 }, 500);
			}	
			
		});

	} // FIN if $pcDatePickerRequired


} // FIN if $pcDatePicker


/*=====  FIN Compatibilité  =====*/

    
}); // End document.ready