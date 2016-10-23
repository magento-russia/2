<?php
abstract class Df_Avangard_Response extends Df_Payment_Response {
	/** @return int */
	public function getRequestExternalId() {return $this->cfg(self::$_ID);}
	/** @return int */
	public function getResponseCode() {return $this->cfg(self::$_RESPONSE_CODE);}
	/**
	 * @override
	 * @return string
	 */
	protected function getErrorMessage() {return $this->cfg(self::$_RESPONSE_MESSAGE);}
	/**
	 * @override
	 * @return bool
	 */
	protected function isSuccessful() {return 0 === $this->getResponseCode();}

	/**
	 * @override
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		$this
			->_prop(self::$_ID, DF_V_NAT)
			->_prop(self::$_RESPONSE_CODE, DF_V_INT)
			->_prop(self::$_RESPONSE_MESSAGE, DF_V_STRING)
		;
	}
	/** @var string */
	private static $_ID = 'id';
	/** @var string */
	private static $_RESPONSE_CODE = 'response_code';
	/** @var string */
	private static $_RESPONSE_MESSAGE = 'response_message';
}