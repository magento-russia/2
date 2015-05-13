// программный код, который надо выполнить сразу после загрузки страницы
rm.namespace('rm.checkout');
(function($) {$(function() {
	rm.checkout.Ergonomic.construct({elementSelector: '.df .df-checkout-ergonomic'});
	rm.checkout.OrderComments.construct({});
	$(window).bind(
		rm.checkout.Ergonomic.sectionUpdated
		,/** @param {jQuery.Event} event */
		function(event) {
			if ('review' === event.section) {
				rm.checkout.OrderComments.construct({});
			}
		}
	);
});})(jQuery);