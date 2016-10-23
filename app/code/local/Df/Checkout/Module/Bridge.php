<?php
/** @method Df_Checkout_Module_Main main() */
class Df_Checkout_Module_Bridge extends Df_Core_Model_Bridge {
	/**
	 * @override
	 * @see Df_Core_Model_Bridge::_construct()
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, Df_Checkout_Module_Main::class);
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
	protected function store() {return df_store($this->main()->getStore());}

	/**
	 * @used-by Df_Payment_Method::getCheckoutModuleType()
	 * @used-by Df_Shipping_Carrier::getCheckoutModuleType()
	 * @param string $mainBaseClass
	 * @return string
	 */
	public static function _type($mainBaseClass) {
		/** @var array(string => string) $cache */
		static $cache;
		if (!isset($cache[$mainBaseClass])) {
			$cache[$mainBaseClass] = df_class_second($mainBaseClass);
		}
		return $cache[$mainBaseClass];
	}

	/**
	 * @used-by Df_Checkout_Module_Config_Area::s()
	 * @param Df_Checkout_Module_Main $main
	 * @param string $suffix
	 * @return Df_Checkout_Module_Bridge
	 */
	protected static function convention(Df_Checkout_Module_Main $main, $suffix) {
		/** @var string $default */
		$default = df_cc_class_('Df', $main->getCheckoutModuleType(), $suffix);
		/** @var string $resultClass */
		$resultClass = df_con($main, $suffix, $default);
		return self::ic($resultClass, $main);
	}
}