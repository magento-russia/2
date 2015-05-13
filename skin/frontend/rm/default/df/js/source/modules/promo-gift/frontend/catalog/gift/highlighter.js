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
		var eligibleProductIds = window.rm.promo_gift.eligibleProductIds;
		if (eligibleProductIds instanceof Array) {
			/**
			 * Итак, если покупатель смотрит карточку товара,
			 * * и данный товар он вправе получить в подарок(выполнил условия акции),
			 * * то надо внешне отразить сей факт на карточке товара
			 */
			/** @type {jQuery} HTMLElement */
			var $addToCartForm = $('#product_addtocart_form');
			if (0 < $addToCartForm.length) {
				/** @type {jQuery} HTMLInputElement[] */
				var $productIdInputs = $("input[name='product']", $addToCartForm);
				if (0 < $productIdInputs.length) {
					/** @type {Number} */
					var productId = parseInt($productIdInputs.first().val());
					/**
					 *  Используем jQuery.inArray вместо Array.indexOf,
					 *  потому что Array.indexOf отсутствует в IE 8
					 *  @link http://www.w3schools.com/jsref/jsref_indexof_array.asp
					 */
					if (-1 < $.inArray(productId, eligibleProductIds)) {
						$addToCartForm.closest('.product-view').addClass('df-gift-product');
						/** @type {String} */
						var labelText = window.rm.promo_gift.eligibleProductLabel;
						if ('string' === typeof(labelText)) {
							var $giftLabel = $('<div/>');
							$giftLabel.addClass('df-gift-label');
							$giftLabel
								.html(
									labelText
								)
							;
							/** @type {jQuery} HTMLElement[] */
							var $priceBoxes = $('.price-box', $addToCartForm);
							if (0 < $priceBoxes.length) {
								var $priceBox = $priceBoxes.first();
								$priceBox
									.after($giftLabel)
								;
							}
						}
					}
				}
			}
		}
	}
}); })(jQuery);