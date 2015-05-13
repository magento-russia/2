;(function($) { $(function() {
	if ('ru' === $('html').attr('lang')) {
		$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
			/** @type {jQuery} HTMLBodyElement */
			var $root = $('body.df-theme-koolthememaster-caramel');
			if (0 < $root.length) {
				rm.dom
					.replaceText(
						$('li.brands a', $root)
						,'Brands'
						,'производители'
					)
					.replaceText(
						$('.quickllook', $root)
						,'Quick look'
						,'открыть'
					)
					.replaceText(
						$('.quick_view', $root)
						,'quick view'
						,'открыть'
					)
					.replaceText(
						$('a.prod-prev', $root)
						,'PREV'
						,'предыдущий'
					)
					.replaceText(
						$('a.prod-next', $root)
						,'NEXT'
						,'следующий'
					)
				;
			}
		});
	}
}); })(jQuery);