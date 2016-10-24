<?php
/**
 * @method Df_Shipping_Carrier main()
 * @method Df_Shipping_Config_Manager manager()
 */
abstract class Df_Shipping_Config_Area extends Df_Checkout_Module_Config_Area {
	/**
	 * Ключи, значения которых хранятся по стандартному для Magento пути,
	 * в отличие от стандартного для Российской сборки пути.
	 *
	 * Например, ключ title хранится по пути carriers/df-ems/title,
	 * а не по пути df_shipping/ems/title
	 * @return array
	 */
	protected function getLegacyKeys() {return array();}

	/**
	 * @override
	 * @see Df_Checkout_Module_Config_Area::_getVar()
	 * @used-by Df_Checkout_Module_Config_Area::getVar()
	 * Иногда возникает потребность давать ключу другое имя, нежели стандартное для Magento CE.
	 * Например, такая потребность возникает для стандартного ключа «showmethod»,
	 * потому что для ключа с этим именем ядро Magento
	 * выполняет нежелательную для нас обработку на JavaScript
	 * (а именно: скрывает данное поле, если в качестве значения опции
	 * «Ограничить область доставки конкретными странами?» указано «нет»).
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	protected function _getVar($key, $default = null) {
		$key = $this->translateStandardKey($key);
		return
			in_array($key, $this->getLegacyKeys())
			? $this->manager()->getValueLegacy($key, $default)
			: parent::_getVar($key, $default)
		;
	}

	/**
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
	 *
	 * @param string $key
	 * @return string
	 */
	private function translateStandardKey($key) {
		/** @var int|string|null $alias */
		$alias = dfa($this->_standardKeysFlipped, $key);
		return is_string($alias) ? $alias : $key;
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, Df_Shipping_Carrier::class);
	}
}