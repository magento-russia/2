<?php
namespace Df\Shipping\Config;
/**
 * @method \Df\Shipping\Carrier main()
 * @method Manager manager()
 */
abstract class Area extends \Df\Checkout\Module\Config\Area {
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
	 * @see \Df\Checkout\Module\Config\Area::_getVar()
	 * @used-by \Df\Checkout\Module\Config\Area::getVar()
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
		$this->_prop(self::$P__MAIN, \Df\Shipping\Carrier::class);
	}
}