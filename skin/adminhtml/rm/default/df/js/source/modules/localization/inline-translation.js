;(function($) {
	'use strict';
	$(function() {
		/** @type {jQuery} HTMLSelectElement */
		var $locale = $('#interface_locale');
		/**
		 * Применяем заплатки перевода только для русскоязычного административного интерфейса.
		 *
		 * Вызов $locale.val() приведёт к предупреждению в консоли Firefox:
		 * «Use of attributes' specified attribute is deprecated. It always returns true.»
		 * Видимо, это дефект jQuery
		 * @link http://stackoverflow.com/questions/8389841/using-jquery-to-determine-selected-option-causes-specified-attribute-is-depreca
		 */
		if ('ru_RU' === $locale.attr('value')) {
			$(window).bind('bundle.product.edit.bundle.option.selection', function() {
				if (rm.defined(window.bSelection)) {
					if (rm.defined(bSelection.templateBox)) {
						/** @type {jQuery} */
						var $template = $('<div/>').append($(bSelection.templateBox));
						(function() {
							/** @type {jQuery} HTMLElement[] */
							var $headers = $('th.type-price', $template);
							$headers.each(function() {
								/** @type {jQuery} HTMLElement*/
								var $this = $(this);
								if ('Цена' === $this.text()) {
									$this.text('наценка');
								}
							});
						})();
						(function() {
							/** @type {jQuery} HTMLElement[] */
							var $headers = $('th', $template);
							$headers.each(function() {
								/** @type {jQuery} HTMLElement*/
								var $this = $(this);
								$this.text($this.text().toLowerCase());
							});
						})();
						bSelection.templateBox = $template.html();
					}
					if (rm.defined(bSelection.templateRow)) {
						bSelection.templateRow =
							bSelection.templateRow.replace('конкретно указанный', 'абсолютная')
						;
					}
				}
			});
		}
	});
})(jQuery);