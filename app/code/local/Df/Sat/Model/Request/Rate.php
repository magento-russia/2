<?php
class Df_Sat_Model_Request_Rate extends Df_Sat_Model_Request {
	/** @return float */
	public function getRate() {
		if (!isset($this->{__METHOD__})) {
			$this->{__METHOD__} =
				rm_float($this->response()->match("#будет стоить \<strong\> ([\d,]+) грн\.\<\/strong\>#ui"))
			;
		}
		return $this->{__METHOD__};
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}

	/**
	 * @override
	 * @return bool
	 */
	protected function needConvertResponseFrom1251ToUtf8() {return true;}

	const _C = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Sat_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}