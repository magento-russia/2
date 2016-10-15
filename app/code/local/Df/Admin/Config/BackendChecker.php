<?php
abstract class Df_Admin_Config_BackendChecker extends Df_Core_Model {
	/**
	 * @abstract
	 * @return void
	 */
	abstract protected function checkInternal();

	/** @return void */
	public function check() {
		try {
			$this->checkInternal();
		}
		catch (Exception $e) {
			$this->getBackend()->handleCheckerException($e);
			df_exception_to_session($e);
		}
	}

	/** @return Df_Admin_Config_Backend */
	protected function getBackend() {return $this->cfg(self::$P__BACKEND);}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this->_prop(self::$P__BACKEND, Df_Admin_Config_Backend::_C);
	}
	/**
	 * @used-by Df_Admin_Config_BackendChecker_CurrencyIsSupported::_check()
	 * @var string
	 */
	protected static $P__BACKEND = 'backend';
}