<?php
abstract class Df_Admin_Model_Config_Backend_Validator_Strategy extends Df_Core_Model_Abstract {
	/**
	 * @abstract
	 * @return bool
	 */
	abstract public function validate();

	/** @return Df_Admin_Model_Config_Backend_Validator */
	protected function getBackend() {
		return $this->cfg(self::P__BACKEND);
	}

	/** @return Mage_Core_Model_Store */
	protected function getStore() {
		return $this->cfg(self::P__STORE);
	}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::P__BACKEND, Df_Admin_Model_Config_Backend::_CLASS)
			->_prop(self::P__STORE, Df_Core_Const::STORE_CLASS)
		;
	}

	const P__BACKEND = 'backend';
	const P__STORE = 'store';
	const _CLASS = __CLASS__;

}