<?php
namespace Df\Shipping\Config;
/** @method \Df\Shipping\Carrier main() */
class Manager extends \Df\Checkout\Module\Config\Manager {
	/**
	 * @used-by Area::getVar()
	 * @param string $key
	 * @param mixed $d [optional]
	 * @return mixed
	 */
	public function getValueLegacy($key, $d = null) {return $this->legacy()->getValue($key, $d);}

	/**
	 * @override
	 * @see \Df\Checkout\Module\Config\Manager::_getValue()
	 * @param string $key
	 * @return string|null
	 */
	protected function _getValue($key) {return $this->store()->getConfig($key);}

	/**
	 * @override
	 * @see \Df\Checkout\Module\Config\Manager::getKeyBase()
	 * @return string
	 */
	protected function getKeyBase() {return 'df_shipping';}

	/** @return Manager\Legacy */
	private function legacy() {return Manager\Legacy::s($this->main());}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, \Df\Shipping\Carrier::class);
	}
}