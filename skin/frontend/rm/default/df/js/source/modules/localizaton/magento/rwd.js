;(function($) { $(function() {
	$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
		/** @type {jQuery} HTMLBodyElement */
		var $root = $('body.df-theme-magento-rwd');
		if (0 < $root.length) {
			if ($root.hasClass('checkout-cart-index')) {
				rm.dom
					.replaceText(
						$('.or', $root)
						,'-or-'
						,'-или-'
					)
				;
			}
			if ($root.hasClass('review-product-list')) {
				rm.dom
					.replaceHtmlPartial(
						$('.review-heading > h2 > span', $root)
						,'item(s)'
						,''
					)
				;
			}
		}
	});
}); })(jQuery);