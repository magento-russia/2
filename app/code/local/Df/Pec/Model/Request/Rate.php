<?php
class Df_Pec_Model_Request_Rate extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'pecom.ru';}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/bitrix/components/pecom/calc/ajax.php';}

	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Pec_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}