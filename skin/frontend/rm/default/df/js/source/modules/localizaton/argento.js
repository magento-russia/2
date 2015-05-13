;(function($) { $(function() {
	$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
		/** @type {jQuery} HTMLBodyElement */
		var $root = $('body.df-theme-argento');
		if (0 < $root.length) {
			rm.dom
				.replaceText(
					$('.price-label', $root)
					,'Special Price'
					,'Со скидкой:'
				)
				.replaceText(
					$('.highlight-popular .bottom-links a', $root)
					,'See all popular products »'
					,'другие ходовые товары »'
				)
				.replaceText(
					$('.bottom-links a', $root)
					,'See all new products »'
					,'все новинки »'
				)
				.replaceText(
					$('.brands-home .block-title span', $root)
					,'Featured Brands'
					,'Ведущие бренды'
				)
				.replaceText(
					$('.footer-social .label', $root)
					,'Join our community'
					,'мы в социальных сетях'
				)
				.replaceText(
					$('#product_tabs_review_tabbed a', $root)
					,"Product's Review"
					,'Отзывы'
				)
				.replaceText(
					$('#product_tabs_tags_tabbed a', $root)
					,"Product Tags"
					,'Метки'
				)
				.replaceText(
					$('#product_tabs_description_tabbed a', $root)
					,"Product Description"
					,'Описание'
				)
			;
		}
	});
}); })(jQuery);