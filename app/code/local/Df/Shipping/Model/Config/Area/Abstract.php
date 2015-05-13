<?php
abstract class Df_Shipping_Model_Config_Area_Abstract extends Df_Shipping_Model_Config_Abstract {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getAreaPrefix();

	/**
	 * Ключи, значения которых хранятся по стандартному для Magento пути,
	 * в отличие от стандартного для Российской сборки пути.
	 *
	 * Например, ключ title хранится по пути carriers/df-ems/title,
	 * а не по пути df_shipping/ems/title
	 * @return array
	 */
	protected function getLegacyKeys() {
		return array();
	}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки способа доставки
	 * @override
	 * @param string $key
	 * @param mixed $defaultValue[optional]
	 * @return mixed
	 */
	public function getVar($key, $defaultValue = null) {
		df_param_string($key, 0);
		/**
		 * Иногда возникает потребность давать ключу другое имя,
		 * нежели стандартное для Magento CE.
		 * Например, такая потребность возникает
		 * для стандартного ключа «showmethod»,
		 * потому что для ключа с этим именем ядро Magento
		 * выполняет нежелательную для нас обработку на JavaScript
		 * (а именно: скрывает данное поле,
		 * если в качестве значения опции
		 * «Ограничить область доставки конкретными странами?»
		 * указано «нет»).
		 */
		$key = $this->translateStandardKey($key);
		/** @var mixed $result */
		$result =
			in_array($key, $this->getLegacyKeys())
			? $this->getVarManager()->getValueLegacy($key, $defaultValue)
			: parent::getVar($this->preprocessKey($key), $defaultValue)
		;
		return $result;
	}

	/**
	 * @param string $key
	 * @param mixed $defaultValue[optional]
	 * @return bool
	 */
	public function getVarFlag($key, $defaultValue = false) {
		return rm_bool($this->getVar($key, $defaultValue));
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function preprocessKey($key) {
		df_param_string($key, 0);
		return implode('__', df_clean(array($this->getAreaPrefix(), $key)));
	}

	const _CLASS = __CLASS__;
}