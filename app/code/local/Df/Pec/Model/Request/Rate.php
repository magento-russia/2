<?php
class Df_Pec_Model_Request_Rate extends Df_Shipping_Model_Request {
	/**
	 * 2015-02-20
	 * Перекрываем родительский метод, чтобы сделать доступ к нему публичным.
	 * Не делаем родительский метод публичным,
	 * потому что его публичность нужна только в данной точке программы
	 * (видимо, в данной точке программы архитектура неправильна).
	 * @override
	 * @used-by Df_Pec_Model_Api_Calculator::getRates()
	 * @return Df_Shipping_Model_Response
	 */
	public function response() {return parent::response();}

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

	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Pec_Model_Request_Rate
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__POST_PARAMS => $parameters));
	}
}