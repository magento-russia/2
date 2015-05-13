<?php
class Df_NovaPoshta_Request extends Df_Shipping_Model_Request {
	/**
	 * @override
	 * @see Df_Shipping_Model_Request::adjustHttpClient()
	 * @used-by Df_Shipping_Model_Request::getHttpClient()
	 * @param Zend_Http_Client $httpClient
	 * @return Df_RussianPost_Model_Official_Request_International
	 */
	protected function adjustHttpClient(Zend_Http_Client $httpClient) {
		$httpClient->setCookie(new Zend_Http_Cookie('language', 'ru', 'novaposhta.ua'));
	}

	/**
	 * @override
	 * @see Df_Shipping_Model_Request::getQueryHost()
	 * @used-by Df_Shipping_Model_Request::getUri()
	 * @return string
	 */
	protected function getQueryHost() {return 'novaposhta.ua';}
}