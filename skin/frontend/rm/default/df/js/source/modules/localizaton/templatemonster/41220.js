;(function($) { $(function() {
	$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
		/** @type {jQuery} HTMLBodyElement */
		var $root = $('body.df-theme-templatemonster-41220');
		if (0 < $root.length) {
			rm.dom
				.replaceText(
					$('.header .cart-title', $root)
					,'Cart:'
					,'товаров в корзине:'
				)
				.replaceText(
					$('.header .block-cart-header p.empty', $root)
					,'0 item(s)'
					,'0'
				)
				.replaceText(
					$('.header .block-cart-header .amount-2 strong', $root)
					,'1 item'
					,'1'
				)
				.replaceText(
					$('.cart-price strong', $root)
					,'Unit Price: '
					,'цена за штуку: '
				)
				.replaceText(
					$('.cart-price strong', $root)
					,'Subtotal: '
					,'стоимость: '
				)
				.replaceText(
					$('.cart-qty span', $root)
					,'Qty:'
					,'штук: '
				)
				.replaceText(
					$('.footer-col h4', $root)
					,'Newsletter'
					,'Рассылка'
				)
				.replaceHtmlPartial(
					$('.header .block-cart-header .amount-2 strong', $root)
					,' items'
					,''
				)
				.replaceHtmlPartial(
					$('.footer-col-content', $root)
					,'Follow us on:'
					,'Мы в социальных сетях:'
				)
			;
		}
	});
}); })(jQuery);