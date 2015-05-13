<?php
class Df_Shipping_Model_Config_Area_Frontend extends Df_Shipping_Model_Config_Area_Abstract {
	/** @return string */
	public function getDescription() {
		/** @var string $result */
		$result = $this->getVar(self::KEY__VAR__DESCRIPTION, '');
		df_result_string($result);
		return $result;
	}

	/** @return string */
	public function getTitle() {
		/** @var string $result */
		$result = $this->getVar(self::KEY__VAR__TITLE, '');
		df_result_string($result);
		return $result;
	}

	/** @return bool */
	public function needDisableForShopCity() {
		return $this->getVarFlag(self::KEY__VAR__DISABLE_FOR_SHOP_CITY);
	}

	/** @return bool */
	public function needDisplayDiagnosticMessages() {
		return $this->getVarFlag(self::KEY__VAR__DISPLAY_DIAGNOSTIC_MESSAGES, true);
	}

	/** @return bool */
	public function needShowMethodName() {
		return $this->getVarFlag(self::KEY__VAR__SHOW_METHOD_NAME, false);
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getAreaPrefix() {
		return self::AREA_PREFIX;
	}

	/**
	 * Ключи, значения которых хранятся по стандартному для Magento пути,
	 * в отличие от стандартного для Российской сборки пути.
	 * Например, ключ title хранится по пути carriers/df-ems/title,
	 * а не по пути df_shipping/ems/title
	 * @override
	 * @return array
	 */
	protected function getLegacyKeys() {
		return array('active', 'title');
	}

	/**
	 * @override
	 * @return array(string|int => string)
	 */
	protected function getStandardKeys() {
		/** @var array $result */
		$result =
			array_merge(
				parent::getStandardKeys()
				,array(
					'active'
					,'sallowspecific'
					,/**
					 * Иногда возникает потребность давать ключу другое имя,
					 * нежели стандартное для Magento CE.
					 *
					 * Например, такая потребность возникает
					 * для стандартного ключа «showmethod»,
					 * потому что для ключа с этим именем ядро Magento
					 * выполняет нежелательную для нас обработку на JavaScript
					 * (а именно: скрывает данное поле,
					 * если в качестве значения опции
					 * «Ограничить область доставки конкретными странами?»
					 * указано «нет»).
					 */
					self::KEY__VAR__DISPLAY_DIAGNOSTIC_MESSAGES => 'showmethod'
					,'sort_order'
					,'specificcountry'
					,self::KEY__VAR__TITLE
				)
			)
		;
		return $result;
	}

	const _CLASS = __CLASS__;
	const AREA_PREFIX = 'frontend';
	const KEY__VAR__DESCRIPTION = 'description';
	const KEY__VAR__DISABLE_FOR_SHOP_CITY = 'disable_for_shop_city';
	const KEY__VAR__DISPLAY_DIAGNOSTIC_MESSAGES = 'display_diagnostic_messages';
	const KEY__VAR__SHOW_METHOD_NAME = 'show_method_name';
	const KEY__VAR__TITLE = 'title';
}