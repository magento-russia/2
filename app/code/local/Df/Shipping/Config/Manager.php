<?php
/** @method Df_Shipping_Carrier main() */
class Df_Shipping_Config_Manager extends Df_Checkout_Module_Config_Manager {
	/**
	 * @used-by Df_Shipping_Config_Area::getVar()
	 * @param string $key
	 * @param mixed $default [optional]
	 * @return mixed
	 */
	public function getValueLegacy($key, $default = null) {
		return $this->legacy()->getValue($key, $default);
	}

	/**
	 * @override
	 * @param string $key
	 * @return string|null
	 */
	protected function _getValue($key) {return $this->store()->getConfig($key);}

	/**
	 * @override
	 * @return string
	 */
	protected function getKeyBase() {return 'df_shipping';}

	/** @return Df_Shipping_Config_Manager_Legacy */
	private function legacy() {return Df_Shipping_Config_Manager_Legacy::s($this->main());}
	
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, Df_Shipping_Carrier::class);
	}
}