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

		case 'image' :
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
			case 'image' :
				media_url = media_datas.sizes.hasOwnProperty('thumbnail') ? media_datas.sizes.thumbnail.url : media_datas.url ;
				preview_inner = '<div class="pc-media-preview-item" style="background-image:url(' + media_url.replaceAll( ' ', '%20' ) + ');"></div>';
				break;
			case 'audio' :
				media_url = media_datas.url;
				preview_inner = '<audio class="pc-audio-preview" controls src="' + media_url.replaceAll( ' ', '%20' ) + '"></audio>';
				break;
			default :
				media_url = media_datas.url;
				preview_inner = '<a class="pc-pdf-preview" href="' + media_url.replaceAll( ' ', '%20' ) + '" target="_blank"><div class="dashicons dashicons-media-default"></div> Voir le fichier</a>';
				break;
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

jQuery(document).ready(function($){

	var $body = $('body');

	
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
			$hiddenField    = $container.find('.pc-media-id'),            	// champ caché qui transmet à la bdd
			imgIds          = $hiddenField.val();                           // contenu du champ caché
			gallery_state   = imgIds ? 'gallery-edit' : 'gallery-library';  // menu activé par défaut dans la modal (modifier ou créer)
			btn_modal   	= imgIds ? 'Modifier la galerie' : 'Ajouter à la galerie';  // texte btn modal

		// si la modal a déjà été ouverte : réutilisation et sortie de la fonction
		if ( galleryUploader ) { galleryUploader.open( { button: { text: 'blurb' } } ); return; }


		/*----------  Création de l'objet modal  ----------*/
		
		var galleryUploader = wp.media({

			title: btn_modal,
			button: { text: btn_modal },
			library: { type: 'image' },
			frame: "post",
			state: gallery_state,
			multiple: true,

		}); // FIN wp.media

		console.log(galleryUploader);


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
				galleryPreview += '<div class="pc-media-preview-item" style="background-image:url('+imgUrl.replaceAll( ' ', '%20' )+');"></div>';
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

}); // FIN jQuery ready