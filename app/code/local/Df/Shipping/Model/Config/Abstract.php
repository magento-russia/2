<?php
abstract class Df_Shipping_Model_Config_Abstract extends Df_Core_Model_Abstract {
	/**
	 * @param string $key
	 * @return bool
	 */
	public function canProcessStandardKey($key) {
		df_param_string($key, 0);
		/** @var bool $result */
		$result = in_array($key, $this->getStandardKeys());
		return $result;
	}

	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getConst(
		$key
		,$canBeTest = true
		,$defaultValue = ''
	) {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		df_param_string($defaultValue, 2);
		/** @var string $result */
		$result = $this->getConstManager()->getValue($key, $canBeTest, $defaultValue);
		df_result_string($result);
		return $result;
	}

	/** @return Df_Shipping_Model_ConfigManager_Const */
	public function getConstManager() {
		return $this->cfg(self::P__CONST_MANAGER);
	}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки платёжного способа
	 *
	 * @param string $key
	 * @param mixed $defaultValue[optional]
	 * @return mixed
	 */
	public function getVar($key, $defaultValue = null) {
		df_param_string($key, 0);
		/** @var mixed $result */
		$result = $this->getVarManager()->getValue($key, $defaultValue);
		return $result;
	}

	/** @return Df_Shipping_Model_ConfigManager_Var */
	public function getVarManager() {
		return $this->cfg(self::P__VAR_MANAGER);
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
	 * @param string $standardKey
	 * @return string
	 */
	public function translateStandardKey($standardKey) {
		df_param_string($standardKey, 0);
		/** @var string $result */
		$result = df_a(array_flip($this->getStandardKeys()) ,$standardKey);
		if (is_numeric($result) || is_null($result)) {
			$result = $standardKey;
		}
		df_result_string($result);
		return $result;
	}

	/**
	 * @param string $value
	 * @return string
	 */
	protected function decrypt($value) {
		return df_mage()->coreHelper()->decrypt($value);
	}

	/**
	 * Стандартные параметры, которые ядро Magento запрашивает через getConfigData.
	 * Например: «sort_order».
	 * У наших модулей все свойства имеют приставку в соответствии с областью настроек.
	 * Например, «frontend__sort_order».
	 * Посредством метода getStandardKeys область настроек указывает стандартные ключи,
	 * которые она в состоянии обрабатывать.
	 * @return array(string|int => string)
	 */
	protected function getStandardKeys() {
		return array();
	}

	/** @return Mage_Core_Model_Store */
	protected function getStore() {
		return $this->getVarManager()->getStore();
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CONST_MANAGER, Df_Shipping_Model_ConfigManager_Const::_CLASS)
			->_prop(self::P__VAR_MANAGER, Df_Shipping_Model_ConfigManager_Var::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__CONST_MANAGER = 'const_manager';
	const P__VAR_MANAGER = 'var_manager';
}