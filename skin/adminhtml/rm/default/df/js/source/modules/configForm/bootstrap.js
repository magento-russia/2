;(function($) { $(function() {
	(function(){
		/**
		 * Если у кнопки отсутствует метка (а она обычно кнопке не нужна),
		 * то сдвигаем кнопку влево (удаляем табличную ячейку с меткой).
		 */
		/** @type {jQuery} HTMLBodyElement */
		var $root = $('body.adminhtml-system-config-edit');
		if (0 < $root.length) {
			/** {jQuery} HTMLTableRowElement[] */
			var $rows = $('fieldset.config > table > tbody > tr', $root);
			$rows.each(function() {
				/** {jQuery} HTMLTableRowElement */
				var $row = $(this);
				/** {jQuery} HTMLLabelElement */
				var $label = $('td:first label', $row);
				if (
						(0 === $.trim($label.text()).length)
					&&
						(0 < $('button.rm-action', $row).length)
				) {
					$label.closest('td').remove();
				}
			});
		}
	})();
}); })(jQuery);