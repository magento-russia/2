<?php
abstract class Df_Admin_Model_Config_BackendChecker extends Df_Core_Model_Abstract {
	/**
	 * @abstract
	 * @return void
	 */
	abstract protected function checkInternal();

	/** @return Df_Admin_Model_Config_BackendChecker */
	public function check() {
		try {
			$this->checkInternal();
		}
		catch(Exception $e) {
			$this->getBackend()->handleCheckerException($e);
			rm_exception_to_session($e);
		}
		return $this;
	}

	/** @return Df_Admin_Model_Config_Backend */
	protected function getBackend() {return $this->cfg(self::P__BACKEND);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::P__BACKEND, Df_Admin_Model_Config_Backend::_CLASS);
	}
	const _CLASS = __CLASS__;
	const P__BACKEND = 'backend';
}