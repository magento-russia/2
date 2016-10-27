;(function($) { $(function() {
	/**
	 * 2015-04-03
	 * Сегодня заметил, что при изменении в настройках модулей доставки
	 * значения опции «Ограничить область доставки конкретными странами?» с «нет» на «да»
	 * сбрасывается значение поля «Перечень стран, куда разрешена доставка данным способом»,
	 * причём Magento Community Edition делает это преднамеренно:
	 * @see app/design/adminhtml/default/default/template/system/shipping/applicable_country.phtml
	 * @see CountryModel.prototype.initSpecificCountry()
	 * @see CountryModel.prototype.checkSpecificCountry()
	 * Отключаем данное поведение.
	 *
	 * Обратите внимание, что тот же класс @see CountryModel
	 * при смене значения той же опции «Ограничить область доставки конкретными странами?» с «да» на «нет»
	 * зачем-то скрывает поле «Показывать ли способ доставки на витрине в том случае,
	 * когда он по каким-либо причинам неприменим к текущему заказу?».
	 * Этот эффект, в отличие от описанного выше, было замечен мной уже давно,
	 * и поэтому устранён по-другому: @see \Df\Shipping\Config\Area\Frontend::getStandardKeys()
	 *
	 * Обратите внимание, что заплатку надо применять именно после загрузки DOM,
	 * потому что @see CountryModel инициализируется через шаблон:
	 * @see app/design/adminhtml/default/default/template/system/shipping/applicable_country.phtml
	 */
	if (rm.defined(window.CountryModel)) {
		CountryModel.prototype.unselectSpecificCountry = function(){};
	}
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