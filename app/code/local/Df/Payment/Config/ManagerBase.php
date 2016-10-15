<?php
/** @method Df_Payment_Model_Method main() */
abstract class Df_Payment_Config_ManagerBase extends Df_Checkout_Module_Config_Manager {
	/**
	 * @override
	 * @param string $key
	 * @return string
	 */
	protected function adaptKey($key) {
		return df_cc_path($this->getKeyBase(), $this->main()->getRmId(), $key);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__MAIN, Df_Payment_Model_Method::class);
	}
}