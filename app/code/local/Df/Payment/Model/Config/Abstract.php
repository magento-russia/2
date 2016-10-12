<?php
abstract class Df_Payment_Model_Config_Abstract extends Df_Core_Model {
	/**
	 * @param string $key
	 * @return bool
	 */
	public function canProcessStandardKey($key) {return in_array($key, $this->getStandardKeys());}

	/**
	 * @param string $key
	 * @param bool $canBeTest[optional]
	 * @param string $defaultValue[optional]
	 * @return string
	 */
	public function getConst($key, $canBeTest = true, $defaultValue = '') {
		df_param_string($key, 0);
		df_param_boolean($canBeTest, 1);
		df_param_string($defaultValue, 2);
		/** @var string $result */
		$result = $this->getConstManager()->getValue($key, $canBeTest, $defaultValue);
		df_result_string($result);
		return $result;
	}

	/** @return Df_Payment_Model_ConfigManager_Const */
	public function getConstManager() {return $this->cfg(self::P__CONST_MANAGER);}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки платёжного способа
	 * @param string $key
	 * @param mixed $defaultValue[optional]
	 * @return mixed
	 */
	public function getVar($key, $defaultValue = null) {
		return $this->getVarManager()->getValue($key, $defaultValue);
	}

	/** @return Df_Payment_Model_ConfigManager_Var */
	public function getVarManager() {return $this->cfg(self::P__VAR_MANAGER);}

	/**
	 * @param string $value
	 * @return string
	 */
	protected function decrypt($value) {return df_mage()->coreHelper()->decrypt($value);}

	/**
	 * Стандартные параметры, которые ядро Magento запрашивает через getConfigData.
	 * Например: «sort_order».
	 * У наших модулей все свойства имеют приставку в соответствии с областью настроек.
	 * Например, «frontend__sort_order».
	 * Посредством метода getStandardKeys область настроек указывает стандартные ключи,
	 * которые она в состоянии обрабатывать.
	 * @return array
	 */
	protected function getStandardKeys() {return array();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CONST_MANAGER, Df_Payment_Model_ConfigManager_Const::_CLASS)
			->_prop(self::P__VAR_MANAGER, Df_Payment_Model_ConfigManager_Var::_CLASS)
		;
	}
	const _CLASS = __CLASS__;
	const P__CONST_MANAGER = 'const_manager';
	const P__VAR_MANAGER = 'var_manager';
}