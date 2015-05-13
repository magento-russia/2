;(function($) { $(function() {
	$(window).bind(rm.tweaks.ThemeDetector.initialized, function() {
		/** @type {jQuery} HTMLBodyElement */
		var $root = $('body.df-theme-8theme-mercado');
		if (0 < $root.length) {
			$('#nav ul.level0', $root).each(function(){
				/** @type {jQuery} HTMLUListElement */
				var $this = $(this);
				$this
					// Обратите внимание, что в названии класса нет опечатки,
					// просто так его назвал безграмотный разработчик темы Mercado
					.addClass('chield')
					.addClass('chield' + $this.children('li').length)
				;
			});
		}
	});
}); })(jQuery);