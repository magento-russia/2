<?php
class Df_Pec_Model_Request_Rate extends Df_Shipping_Model_Request {
	/**
	 * 2016-09-08
	 * «pecom.ru» => «calc.pecom.ru»
	 * http://magento-forum.ru/topic/5473/
	 * «ПЭК перенаправил запрос с адреса pecom.ru на неожиданный адрес calc.pecom.ru»
	 * @override
	 * @return string
	 */
	protected function getQueryHost() {return 'calc.pecom.ru';}

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