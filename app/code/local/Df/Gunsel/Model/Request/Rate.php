<?php
class Df_Gunsel_Model_Request_Rate extends Df_Gunsel_Model_Request {
	/** @return float */
	public function getRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_float($this->response()->match('#Сумма к оплате:[^=]+= ([\d\.]+) грн#'))
			;
		}
		return $this->{__METHOD__};
	}

	/** @return string */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Gunsel_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {return new self(array(
		self::P__POST_PARAMS => $parameters
	));}
}