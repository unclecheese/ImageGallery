// Initializes the Shadowbox. This can be replaced with the necessary JS code for any other lightbox.
(function($) {
	$(function(){
		Shadowbox.init({
			counterType:'skip',
			continuous:false,
			counterLimit:10
		});
	});
})(jQuery);