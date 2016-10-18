<?php
abstract class Df_Checkout_Module_Config_Manager extends Df_Checkout_Module_Bridge {
	/**
	 * @param mixed $key
	 * @return mixed
	 */
	abstract protected function _getValue($key);

	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getKeyBase();

	/**
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	public function getValue($key, $default = null) {
		/** @var string $result */
		$result = $this->_getValue($this->adaptKey($key));
		return $this->adaptValue($this->isValueEmpty($result) ? $default : $result);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {return
		df_cc_path($this->getKeyBase(), $this->main()->getRmId(), $key)
	;}

	/**
	 * @param mixed $value
	 * @return mixed
	 * @throws Exception
	 */
	private function adaptValue($value) {
		/** @var mixed $result */
		$result = $value;
		/** @var bool $doing */
		static $doing = false;
		if (!$doing && is_string($result) && df_contains($result, '{')) {
			// чтобы не попадать в рекурсию
			$doing = true;
			try {
				$result = strtr($result, $this->getTemplates());
			}
			catch (Exception $e) {
				$doing = false;
				throw $e;
			}
			$doing = false;
		}
		return $result;
	}

	/** @return array(string => string) */
	private function getTemplates() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} = array_merge(
				array('{телефон магазина}' => df_cfg()->base()->getStorePhone($this->store()))
				,$this->main()->getConfigTemplates()
			);
		}
		return $this->{__METHOD__};
	}

	/**
	 * Нельзя называть этот метод isEmpty(),
	 * потому что метод с таким именем уже имеется: @see Varien_Object::isEmpty()
	 * @used-by getValue()
	 * @param mixed $value
	 * @return bool
	 */
	private function isValueEmpty($value) {return is_null($value) || df_empty_string($value);}

	/**
	 * @used-by Df_Checkout_Module_Config_Area::manager()
	 * @static
	 * @param Df_Checkout_Module_Main $main
	 * @return Df_Checkout_Module_Config_Manager
	 */
	public static function s(Df_Checkout_Module_Main $main) {
		/** @var array(string => Df_Checkout_Module_Config_Manager) */
		static $cache;
		/** @var string $key */
		$key = get_class($main);
		if (!isset($cache[$key])) {
			/**
			 * @var string $class
			 * @see Df_Payment_Config_Manager
			 * @see Df_Shipping_Config_Manager
			 */
			/**
			 * 2015-04-05
			 * Менеджер настроек конкретного модуля должен иметь имя по шаблону
			 * <Имя модуля>Config_Manager
			 * Аналогичное соглашение действует и для классов с настройками конкретных областей:
			 * @see Df_Checkout_Module_Config_Area::sa()
			 */
			$class = df_cc_class_('Df', $main->getCheckoutModuleType(), 'Config_Manager');
			$cache[$key] = self::ic($class, $main);
			df_assert($cache[$key] instanceof Df_Checkout_Module_Config_Manager);
		}
		return $cache[$key];
	}

	/**
	 * @used-by Df_Shipping_Config_Manager::s()
	 * @used-by Df_Shipping_Config_Manager_Legacy::s()
	 * @used-by Df_Payment_Config_Manager_Const::s()
	 * @used-by Df_Payment_Config_Manager_Const_Default::s()
	 * @used-by Df_Payment_Config_Manager_Const_ModeSpecific::s()
	 * @static
	 * @param string $class
	 * @param Df_Checkout_Module_Main $main
	 * @return Df_Checkout_Module_Config_Manager
	 */
	protected static function sc($class, Df_Checkout_Module_Main $main) {
		/** @var string $key */
		$key = get_class($main) . '::' . $class;
		/** @var array(string => Df_Checkout_Module_Config_Manager) */
		static $cache;
		if (!isset($cache[$key])) {
			$cache[$key] = self::ic($class, $main);
		}
		return $cache[$key];
	}
}