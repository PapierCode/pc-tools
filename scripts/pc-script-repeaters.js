document.addEventListener( 'DOMContentLoaded', () => {

	'use strict';

	const selectors = document.querySelectorAll( '.posts-selector' );

	selectors.forEach( ( selector ) => {

		const posts = JSON.parse( selector.dataset.posts ); // liste des posts

		const container = selector.querySelector( '.posts-selector-list' ); // container des éléments 
		const add = selector.querySelector( '.pc-repeater-more' ); // btn ajout
		const meta = selector.querySelector( '.posts-selector-meta' ); // champ sauvegardé

		let selection = meta.value; // posts sélectionnés (string)


		/*----------  Création/ajout d'une line   ----------*/	
		
		function addLine( selected = -1 ) {

			let line = document.createElement( 'div' );
			line.classList.add( 'posts-selector-item' );

			/*----------  Select  ----------*/			

			let select = document.createElement( 'select' );
			select.dataset.from = '';
			
			let emptyOption = document.createElement( 'option' );
			select.append( emptyOption );

			posts.forEach( ( post ) => {

				let option = document.createElement( 'option' );
				option.setAttribute( 'value', post.id );
				if ( post.id == selected ) {
					option.setAttribute( 'selected', '' );
					select.dataset.from = selected;
				} else if ( selection.includes( post.id ) ) {
					option.style.display = 'none';
				}
				option.innerText = post.title;

				select.append( option );

			});

			select.addEventListener( 'change', ( e ) => {

				let selects = container.querySelectorAll( 'select' );
				let selectCurrent = e.target;
				let value = selectCurrent.value;
				let from = selectCurrent.dataset.from;
				
				if ( '' !== value ) {
					
					// masque l'option dans les autres selects
					selects.forEach( ( select ) => { 
						if ( selectCurrent !== select ) { select.querySelector( 'option[value="'+value+'"]' ).style.display = 'none'; }
					} );

					selection.push( parseInt( value ) );

				} 

				if ( value !== from ) {

					// réaffiche l'option dans les autres selects
					selects.forEach( ( select ) => { 
						if ( selectCurrent !== select && from != '' ) { select.querySelector( 'option[value="'+from+'"]' ).style.display = 'block'; }
					} );

					selection = selection.filter( ( id ) => { return id != selectCurrent.dataset.from; } );

				}

				selectCurrent.dataset.from = value;
				updateTarget( container );

			} );

			line.append( select );			

			/*----------  Bouton suppression  ----------*/			
			
			let trash = document.createElement( 'span' );
			trash.setAttribute( 'title', 'Supprimer' );
			trash.classList.add( 'pc-repeater-btn-trash', 'dashicons', 'dashicons-trash' );

			trash.addEventListener( 'click', ( e ) => {

				let parent = e.target.parentNode;
				let value = parent.querySelector( 'select' ).value;

				if ( value != '' ) { // réaffichage dans les autres selects
					let selects = container.querySelectorAll( 'select' );
					selects.forEach( ( select ) => { 
						select.querySelector( 'option[value="'+value+'"]' ).style.display = 'block';
					} );
				}

				selection = selection.filter( ( id ) => {
					return id != parent.querySelector( 'select' ).value;
				} );

				parent.remove();
				updateTarget( container );

			});
			
			line.append( trash );

			/*----------  Bouton déplacer  ----------*/			

			let move = document.createElement( 'span' );
			move.setAttribute( 'title', 'Déplacer' );
			move.classList.add( 'pc-repeater-btn-move', 'dashicons', 'dashicons-move' );

			line.append( move );

			/*----------  Ajout de la ligne  ----------*/			

			container.append( line );

		}


		/*----------  Mise à jour du champ à sauvegarder  ----------*/
				
		function updateTarget() {

			let save = [];

			let selects = container.querySelectorAll( 'select' );
			selects.forEach( ( select ) => { if ( select.value != '' ) { save.push( select.value ); } } );
			
			meta.value = save.join();

			// console.log( 'save', save.join() );
			// console.log( 'selection', selection );

		}


		/*----------  Init  ----------*/

		if ( selection != '' ) {

			selection = selection.split(',');
			selection = selection.map( ( id ) => { return parseInt( id ); } );
			selection.forEach( ( id ) => { addLine( id ); } );

		} else { selection = []; }
		
		add.addEventListener( 'click', () => {

			if ( selection.length == posts.length || container.querySelectorAll( '.posts-selector-item' ).length == posts.length ) {
				alert( 'Il n\'y a plus de possibilité d\'ajout.' );
			} else {
				addLine();
			}

	 	} );

		// tri temporaire en jQuery
		let $container = jQuery( container );
		$container.sortable( { handle : '.pc-repeater-btn-move' } );
		$container.on( 'sortupdate', updateTarget );

	} );

} );

/*================================
=            Repeater            =
================================*/

jQuery(document).ready(function($){

var $repeaters = $('.pc-repeater');

if ( $repeaters.length > 0 ) {

	$repeaters.each( function() {

		var $repeater = $(this);
		var fields = $repeater.data('fields').split(',');
		var separator = $repeater.data('separator');

		var $target = $repeater.nextAll('.pc-repeater-target');
		var $more = $repeater.nextAll('.pc-repeater-more');
		var $src = $repeater.nextAll('.pc-repeater-src');

		var update_repeater = function() {
			
			var to_save = '';

			$repeater.children().each(function() {

				var $current = $(this);
				var sub_to_save = '';

				fields.forEach(function( name ){
					sub_to_save += '['+name+']'+$current.find('[name='+name+']').val()+'[/'+name+']';
				});

				if ( to_save == '' ) { to_save = sub_to_save; }
				else { to_save += separator + sub_to_save; }

			});

			console.log(to_save);
			$target.val(to_save);

		};

		$repeater.on( 'focusout', 'input, textarea, select', function(e) {
			update_repeater();
		});

		$repeater.on( 'click', '.pc-repeater-btn-trash', function() {
			$(this).parents('.pc-repeater-item').remove();
			update_repeater();
		});
		
		$more.click(function(){
			$src
				.clone()
				.html(function(i,old) { return old.replaceAll('[index]', $repeater.children().length ); })
				.appendTo($repeater)
				.removeClass('pc-repeater-src')
				.find('input[data-required="required"]').prop('required',true);
		});

		$repeater.sortable({
			handle : '.pc-repeater-btn-move',
			placeholder: 'pc-repeater-placeholder'
		});
			
		$repeater.on( "sortupdate", function( event, ui ) {
			update_repeater();
		} );

	});

} // FIN if $repeater

});


/*=====  FIN Repeater  =====*/