<?php
abstract class Df_Shipping_Model_ConfigManager extends Df_Core_Model {
	/**
	 * @abstract
	 * @return string
	 */
	abstract protected function getKeyBase();

	/** @return Mage_Core_Model_Store */
	public function getStore() {
		return $this->cfg(self::P__STORE);
	}

	/** @return Df_Shipping_Model_Carrier */
	protected function getCarrier() {
		return $this->cfg(self::P__CARRIER);
	}

	/**
	 * @param string $key
	 * @return string
	 */
	protected function preprocessKey($key) {
		df_param_string($key, 0);
		return rm_config_key($this->getKeyBase(), $this->getCarrier()->getRmId(), $key);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__CARRIER, Df_Shipping_Model_Carrier::_CLASS);
		$this->_prop(self::P__STORE, 'Mage_Core_Model_Store');
	}
	const _CLASS = __CLASS__;
	const P__CARRIER = 'carrier';
	const P__STORE = 'store';
}