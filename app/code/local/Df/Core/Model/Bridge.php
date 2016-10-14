<?php
class Df_Core_Model_Bridge extends Df_Core_Model {
	/** @return Varien_Object */
	protected function main() {return $this->cfg(self::$P__MAIN);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, 'Varien_Object');
	}
	/**
	 * @used-by Df_Checkout_Module_Bridge::_construct()
	 * @used-by Df_Checkout_Module_Config_Facade::_construct()
	 * @used-by Df_Payment_Config_ManagerBase::_construct()
	 * @used-by Df_Shipping_Model_Bridge::_construct()
	 * @used-by Df_Shipping_Config_Manager::_construct()
	 * @var string
	 */
	protected static $P__MAIN = 'main';

	/**
	 * @used-by Df_Checkout_Module_Bridge::convention()
	 * @used-by Df_Checkout_Module_Config_Facade::s()
	 * @used-by Df_Checkout_Module_Config_Manager::s()
	 * @used-by Df_Checkout_Module_Config_Manager::sc()
	 * @used-by Df_Checkout_Module_Config_Area_No::s()
	 * @static
	 * @param string $class
	 * @param Varien_Object|object $main
	 * @return Df_Core_Model_Bridge
	 */
	protected static function ic($class, Varien_Object $main) {
		return rm_ic($class, __CLASS__, array(self::$P__MAIN => $main));
	}
}