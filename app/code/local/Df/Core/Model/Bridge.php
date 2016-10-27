<?php
abstract class Df_Core_Model_Bridge extends Df_Core_Model {
	/** @return Varien_Object */
	protected function main() {return $this[self::$P__MAIN];}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, Varien_Object::class);
	}
	/**
	 * @used-by \Df\Checkout\Module\Bridge::_construct()
	 * @used-by \Df\Checkout\Module\Config\Facade::_construct()
	 * @used-by \Df\Payment\Config\ManagerBase::_construct()
	 * @used-by \Df\Shipping\Bridge::_construct()
	 * @used-by \Df\Shipping\Config\Manager::_construct()
	 * @var string
	 */
	protected static $P__MAIN = 'main';

	/**
	 * @used-by \Df\Checkout\Module\Bridge::convention()
	 * @used-by \Df\Checkout\Module\Config\Facade::s()
	 * @used-by \Df\Checkout\Module\Config\Manager::s()
	 * @used-by \Df\Checkout\Module\Config\Manager::sc()
	 * @used-by \Df\Checkout\Module\Config\Area_No::s()
	 * @static
	 * @param string $class
	 * @param Varien_Object|object $main
	 * @return Df_Core_Model_Bridge
	 */
	protected static function ic($class, Varien_Object $main) {return
		df_ic($class, __CLASS__, [self::$P__MAIN => $main])
	;}
}