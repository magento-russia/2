rm.namespace('rm.checkout');
(function($) { $(function() {
	(function() {
		/** @type {Object} */
		var cookies = {
			'#opc-billing .df-yandex-market-address': 'rm.yandex_market.address.billing'
			,'#opc-shipping .df-yandex-market-address': 'rm.yandex_market.address.shipping'
		};
		/** @type {Object} */
		var options = {expires: 1, path: '/'};
		$.each(cookies, function(selector, cookieToSet) {
			$(selector).click(function() {
				$.cookie(cookieToSet, 1, options);
				$.each(cookies, function(selector, cookie) {
					if (cookie !== cookieToSet) {
						$.removeCookie(cookie, options);
					}
				});
			});
		});
	})();
}); })(jQuery);