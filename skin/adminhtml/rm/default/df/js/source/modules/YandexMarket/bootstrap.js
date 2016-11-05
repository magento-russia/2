;(function($) { $(function() {
	var $categoryField = $('#rm_yandex_market_category');
	if (0 < $categoryField.length) {
		$($categoryField.autocomplete({
			serviceUrl: '/index.php/df-yandex-market/category/suggest/'
			,width: 400
			,maxHeight:250
			,deferRequestBy: 300
		}));
	}
}); })(jQuery);