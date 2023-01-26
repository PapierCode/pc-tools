(function($){
	if (typeof(acf) == 'undefined') { return; }
	// pas de duplication d'un bloc flexible dans l'éditeur
	acf.add_action('ready append', function(e) { $('.acf-block-fields a.acf-icon.-duplicate').remove(); });
})(jQuery);