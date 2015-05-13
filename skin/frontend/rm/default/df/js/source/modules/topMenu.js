(function($){$(function() {
	rm.namespace('rm.topMenu');
	if (rm.topMenu.activeNodePath) {
		/** @type {jQuery} HTMLElement */
		var $container = $('#nav');
		$.each(rm.topMenu.activeNodePath, function(index, value) {
			/** @type string */
			var selector = '.id-' + value;
			$(selector, $container).addClass('active');
		});
	}
});})(jQuery);