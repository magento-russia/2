<?php
/** @method Df_Shipping_Carrier main() */
class Df_Shipping_Model_Bridge extends Df_Checkout_Module_Bridge {
	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, Df_Shipping_Carrier::_C);
	}
}