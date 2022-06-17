jQuery(document).ready(function($){
	
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

});