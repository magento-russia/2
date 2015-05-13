;(function($) { $(function() {
	/** @type {jQuery} HTMLLIElement[] */
	var $products = $('.df .df-sales-admin-widget-grid-column-renderer-products .df-product');
	$products.filter(':odd').addClass('df-product-odd');
	$products.filter(':even').addClass('df-product-even');

}); })(jQuery);