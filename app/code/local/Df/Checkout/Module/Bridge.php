<?php
/** @method Df_Checkout_Module_Main main() */
class Df_Checkout_Module_Bridge extends Df_Core_Model_Bridge {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, Df_Checkout_Module_Main::_INTERFACE);
	}

	/**
	 * Использование потомками:
	 * @used-by Df_Checkout_Module_Config_Manager::getTemplates()
	 * @used-by Df_Payment_Config_Manager::_getValue()
	 * @used-by Df_Shipping_Config_Manager::_getValue()
	 * @used-by Df_Shipping_Config_Area_Admin::getProcessingBeforeShippingDays()
	 * @used-by Df_Shipping_Config_Area_Admin::isTodayOff()
	 * @return Df_Core_Model_StoreM
	 */
	protected function store() {return rm_store($this->main()->getStore());}

	/**
	 * @used-by Df_Payment_Model_Method::getCheckoutModuleType()
	 * @used-by Df_Shipping_Carrier::getCheckoutModuleType()
	 * @param string $mainBaseClass
	 * @return string
	 */
	public static function _type($mainBaseClass) {
		/** @var array(string => string) $cache */
		static $cache;
		if (!isset($cache[$mainBaseClass])) {
			$cache[$mainBaseClass] = df_a(rm_explode_class($mainBaseClass), 1);
		}
		return $cache[$mainBaseClass];
	}

	/**
	 * @used-by Df_Checkout_Module_Config_Area::s()
	 * @static
	 * @param Df_Checkout_Module_Main $main
	 * @param string $suffix
	 * @return Df_Checkout_Module_Bridge
	 */
	protected static function convention(Df_Checkout_Module_Main $main, $suffix) {
		/** @var string $default */
		$default = rm_concat_class('Df', $main->getCheckoutModuleType(), $suffix);
		/** @var string $resultClass */
		$resultClass = rm_convention($main, $suffix, $default);
		return self::ic($resultClass, $main);
	}
}