<?php
namespace Df\Checkout\Module;
/** @method Main main() */
class Bridge extends \Df_Core_Model_Bridge {
	/**
	 * @override
	 * @see Df_Core_Model_Bridge::_construct()
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, Main::class);
	}

	/**
	 * Использование потомками:
	 * @used-by \Df\Checkout\Module\Config\Manager::getTemplates()
	 * @used-by \Df\Payment\Config\Manager::_getValue()
	 * @used-by \Df\Shipping\Config\Manager::_getValue()
	 * @used-by \Df\Shipping\Config\Area\Admin::getProcessingBeforeShippingDays()
	 * @used-by \Df\Shipping\Config\Area\Admin::isTodayOff()
	 * @return \Df_Core_Model_StoreM
	 */
	protected function store() {return df_store($this->main()->getStore());}

	/**
	 * @used-by \Df\Payment\Method::getCheckoutModuleType()
	 * @used-by \Df\Shipping\Carrier::getCheckoutModuleType()
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
	 * @used-by \Df\Checkout\Module\Config\Area::s()
	 * @param Main $main
	 * @param string $suffix
	 * @return self
	 */
	protected static function convention(Main $main, $suffix) {
		/** @var string $default */
		$default = df_cc_class('Df', $main->getCheckoutModuleType(), $suffix);
		/** @var string $resultClass */
		$resultClass = df_con($main, $suffix, $default);
		return self::ic($resultClass, $main);
	}
}