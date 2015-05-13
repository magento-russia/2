<?php
class Df_Shipping_Model_Config_Facade extends Df_Shipping_Model_Config_Abstract {
	/** @return Df_Shipping_Model_Config_Area_Admin */
	public function admin() {
		return $this->getConfig($this->getAdminConfigClass());
	}

	/** @return Df_Shipping_Model_Config_Area_Frontend */
	public function frontend() {
		return $this->getConfig($this->getFrontendConfigClass());
	}

	/** @return Df_Shipping_Model_Config_Area_No */
	public function noArea() {
		return $this->getConfig(Df_Shipping_Model_Config_Area_No::_CLASS);
	}

	/** @return Df_Shipping_Model_Config_Area_Service */
	public function service() {
		return $this->getConfig($this->getServiceConfigClass());
	}

	/**
	 * Получаем заданное ранее администратором
	 * значение конкретной настройки платёжного способа
	 * @override
	 * @param string $key
	 * @param mixed $defaultValue[optional]
	 * @return mixed
	 */
	public function getVar($key, $defaultValue = null) {
		df_param_string($key, 0);
		/** @var Df_Shipping_Model_Config_Abstract $processor */
		$processor = $this->getConcreteConfigForKey($key);
		/** @var mixed $result */
		$result = $processor->getVar($key, $defaultValue);
		return $result;
	}

	/** @return string */
	protected function getAdminConfigClass() {
		return $this->cfg(self::P__CONFIG_CLASS__ADMIN);
	}

	/** @return string */
	protected function getFrontendConfigClass() {
		return $this->cfg(self::P__CONFIG_CLASS__FRONTEND);
	}

	/** @return string */
	protected function getServiceConfigClass() {
		return $this->cfg(self::P__CONFIG_CLASS__SERVICE);
	}

	/** @return array */
	protected function getStandardKeyProcessors() {
		/** @var array $result */
		$result =
			array(
				$this->frontend()
				,$this->admin()
				,$this->service()
			)
		;
		return $result;
	}

	/**
	 * @param string $standardKey
	 * @return Df_Shipping_Model_Config_Abstract
	 */
	private function getConcreteConfigForKey($standardKey) {
		df_param_string($standardKey, 0);
		/** @var Df_Shipping_Model_Config_Abstract $result */
		$result = $this->noArea();
		foreach ($this->getStandardKeyProcessors() as $processor) {
			/** @var Df_Shipping_Model_Config_Abstract $processor */
			if ($processor->canProcessStandardKey($standardKey)) {
				$result = $processor;
				break;
			}
		}
		df_assert($result instanceof Df_Shipping_Model_Config_Abstract);
		return $result;
	}

	/**
	 * @param string $configClass
	 * @return Df_Shipping_Model_Config_Abstract
	 */
	private function getConfig($configClass) {
		df_param_string($configClass, 0);
		if (!isset($this->{__METHOD__}[$configClass])) {
			/** @var Df_Shipping_Model_Config_Abstract $result */
			$result =
				df_model($configClass, array(
					Df_Shipping_Model_Config_Abstract::P__CONST_MANAGER => $this->getConstManager()
					,Df_Shipping_Model_Config_Abstract::P__VAR_MANAGER => $this->getVarManager()
				))
			;
			df_assert($result instanceof Df_Shipping_Model_Config_Abstract);
			$this->{__METHOD__}[$configClass] = $result;
		}
		return $this->{__METHOD__}[$configClass];
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__CONFIG_CLASS__ADMIN, self::V_STRING_NE)
			->_prop(self::P__CONFIG_CLASS__FRONTEND, self::V_STRING_NE)
			->_prop(self::P__CONFIG_CLASS__SERVICE, self::V_STRING_NE)
		;
	}
	const _CLASS = __CLASS__;
	const P__CONFIG_CLASS__ADMIN = 'config_class__admin';
	const P__CONFIG_CLASS__FRONTEND = 'config_class__frontend';
	const P__CONFIG_CLASS__SERVICE = 'config_class__shipping_service';
}