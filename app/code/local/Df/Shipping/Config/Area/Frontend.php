<?php
class Df_Shipping_Config_Area_Frontend extends Df_Shipping_Config_Area {
	/** @return string */
	public function getDescription() {return $this->getVar('description', '');}

	/** @return string */
	public function getTitle() {return $this->getVar(self::$V__TITLE, '');}

	/** @return bool */
	public function needDisableForShopCity() {return $this->getVarFlag('disable_for_shop_city');}

	/** @return bool */
	public function needDisplayDiagnosticMessages() {
		return $this->getVarFlag(self::$V__DISPLAY_DIAGNOSTIC_MESSAGES, true);
	}

	/** @return bool */
	public function needShowMethodName() {return $this->getVarFlag('show_method_name', true);}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {return 'frontend';}

	/**
	 * Ключи, значения которых хранятся по стандартному для Magento пути,
	 * в отличие от стандартного для Российской сборки пути.
	 * Например, ключ title хранится по пути carriers/df-ems/title,
	 * а не по пути df_shipping/ems/title
	 * @override
	 * @return array
	 */
	protected function getLegacyKeys() {return array('active', 'title');}

	/**
	 * @override
	 * @return array(string|int => string)
	 */
	protected function getStandardKeys() {
		return array(
			'active'
			,'sallowspecific'
			/**
			 * Иногда возникает потребность давать ключу другое имя, нежели стандартное для Magento CE.
			 * Например, такая потребность возникает для стандартного ключа «showmethod»,
			 * потому что для ключа с этим именем ядро Magento
			 * выполняет нежелательную для нас обработку на JavaScript
			 * (а именно: скрывает данное поле, если в качестве значения опции
			 * «Ограничить область доставки конкретными странами?» указано «нет»).
			 *
			 * 2015-04-03
			 * Сегодня заметил другой аналогичный странный эффект:
			 * при изменении в настройках модулей доставки
			 * значения опции «Ограничить область доставки конкретными странами?» с «нет» на «да»
			 * сбрасывается значение поля «Перечень стран, куда разрешена доставка данным способом»,
			 * причём Magento Community Edition делает это преднамеренно:
			 * @see app/design/adminhtml/default/default/template/system/shipping/applicable_country.phtml
			 * @see CountryModel.prototype.initSpecificCountry()
			 * @see CountryModel.prototype.checkSpecificCountry()
			 * Это поведение я отключил в
			 * @see skin/adminhtml/rm/default/df/js/source/modules/configForm/bootstrap.js
			 */
			,self::$V__DISPLAY_DIAGNOSTIC_MESSAGES => 'showmethod'
			,'sort_order'
			,'specificcountry'
			, self::$V__TITLE
		);
	}

	/** @var string */
	private static $V__DISPLAY_DIAGNOSTIC_MESSAGES = 'display_diagnostic_messages';
	/** @var string */
	private static $V__TITLE = 'title';
}