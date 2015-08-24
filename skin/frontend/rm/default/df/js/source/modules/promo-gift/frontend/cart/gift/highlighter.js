;(function($) { $(function() {
	/**
	 * Наша задача: выделить в корзине товары-подарки.
	 */
	if (
			window.df
		&&
			window.rm.promo_gift
		&&
			window.rm.promo_gift.giftingQuoteItems
	) {
		var giftingQuoteItems = window.rm.promo_gift.giftingQuoteItems;
		if (giftingQuoteItems instanceof Array) {
			/**
			 * Итак, надо найти в корзине строки заказа giftingQuoteItems и выделить их.
			 */
			/** @type {jQuery} HTMLAnchorElement[] */
			var $quoteItems = $('#shopping-cart-table a.btn-remove');
			if (1 > $quoteItems.length) {
				$quoteItems = $('#shopping-cart-table a.btn-remove2');
			}
			$quoteItems.each(function(item) {
				var url = item.href;
				if ('string' === typeof(url)) {
					var quoteItemIdExp = /id\/(\d+)\//;
					var matches = url.match(quoteItemIdExp);
					if (matches instanceof Array) {
						if (1 < matches.length) {
							var quoteItemId = parseInt(matches [1]);
							if (!isNaN(quoteItemId)) {
								/**
								 * Нашли идентификатор текущего товара в корзине
								 */
								/**
								 *  Используем jQuery.inArray вместо Array.indexOf,
								 *  потому что Array.indexOf отсутствует в IE 8
								 *  http://www.w3schools.com/jsref/jsref_indexof_array.asp
								 */
								if (-1 < $.inArray(quoteItemId, giftingQuoteItems)) {
									/**
									 * Эта строка заказа — подарок. Выделяем её
									 */
									/** @type {jQuery} HTMLTableRowElement */
									var $tr = $(item).closest('tr');
									$tr.addClass('df-free-quote-item');
									/**
									 * Подписываем подарок
									 */
									/** @type {jQuery} HTMLElement[] */
									var $elements = $('.product-name', $tr);
									if (0 < elements.length) {
										/** @type {jQuery} HTMLDivElement */
										var $giftLabel = $('<div/>');
										$giftLabel.addClass('df-gift-label');
										$giftLabel
											.html(
												window.rm.promo_gift.giftingQuoteItemTitle
											)
										;
										$elements.first()
											.after($giftLabel)
										;
									}
								}
							}
						}
					}
				}
			});
		}
	}
}); })(jQuery);