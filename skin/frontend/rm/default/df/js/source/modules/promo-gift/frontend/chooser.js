;(function($) { $(function() {
	/**
	 * Наша задача:
	 * 		[*]	назначить чётным подаркам класс df-even
	 * 		[*] назначить нечётным подаркам класс df-odd
	 * 		[*] назначить первому подарку класс df-first
	 * 		[*] назначить последнему подарку класс df-last
	 */
	/** @type {Boolean} */
	var odd = true;
	/** @type {jQuery} HTMLLIElement[] */
	var $products = $('.df-promo-gift .df-gift-chooser .df-side li.df-product');
	$products.first().addClass('df-first');
	$products.last().addClass('df-last');
	$products.filter(':odd').addClass('df-odd');
	$products.filter(':even').addClass('df-even');
}); })(jQuery);