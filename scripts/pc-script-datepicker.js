jQuery(document).ready(function($){

var $pc_metaboxes = $('.pc-metabox');
var $relatives_pickers = $pc_metaboxes.find( 'input[type="date"], input[type="time"], input[type="datetime-local"]' );

if ( $relatives_pickers.length > 0 ) {

	var relatives_pickers_add_min = function( from, to ) {	
		to.attr( 'min', from.val() );
	};

	$relatives_pickers.each( function() {

		if ( this.hasAttribute( 'data-after' ) ) {

			var $input_from = $( '#'+$(this).attr('data-after') ),
			$input_to = $(this);
			$(this).attr( 'min', $input_from.val() );

			if ( '' != $input_from.val() ) { relatives_pickers_add_min( $input_from, $input_to ); }

			$input_from.on( 'input', function() {
				relatives_pickers_add_min( $input_from, $input_to );
			});

		}

	});

} 
	
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
	};

	var pc_date_remove = function() {
		$(this).prev('.pc-date-picker').val('');
		$(this).remove();
	};

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

});