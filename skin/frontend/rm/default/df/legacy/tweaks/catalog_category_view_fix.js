(function ($) {
	$(function () {
		/**
		 * Удаляем пустое место, которое образовывается на карточке товара в списке товаров
		 * при удалении любой из ссылок «Add to Wishlist» и «Add to Compare»
		 */
		(function () {
			var $productCards = $(".products-grid li.item");
			$productCards.each (function () {
				var $productCard = $(this);
				var $addToLinks = $(".add-to-links", $productCard).children ();
				var productCardPaddingBottomOriginal = 80;
				var productCardPaddingBottom =
					productCardPaddingBottomOriginal - 20 * (2 - $addToLinks.size ())
				;
				/**
				 * Если снесли кнопку «Add to Wishlist» - то сей факт тоже учитываем в расчётах
				 */
				if (1 > $(".btn-cart", $productCard).size ()) {
					productCardPaddingBottom -= 30;
				}
				if (productCardPaddingBottom != productCardPaddingBottomOriginal) {
					$productCard
						.css ({
							"padding-bottom": productCardPaddingBottom + "px"
						})
					;
				}
			});
		})();
	});
})(jQuery);