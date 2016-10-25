<?php
class Df_NovaPoshta_Request extends Df_Shipping_Request {
	/**
	 * @override
	 * @see Df_Shipping_Request::adjustHttpClient()
	 * @used-by Df_Shipping_Request::getHttpClient()
	 * @param Zend_Http_Client $httpClient
	 * @return void
	 */
	protected function adjustHttpClient(Zend_Http_Client $httpClient) {
		$httpClient->setCookie(new Zend_Http_Cookie('language', 'ru', 'novaposhta.ua'));
	}

	/**
	 * @override
	 * @see Df_Shipping_Request::getQueryHost()
	 * @used-by Df_Shipping_Request::getUri()
	 * @return string
	 */
	protected function getQueryHost() {return 'novaposhta.ua';}

	/**
	 * 2016-10-24
	 * @override
	 * @see Df_Shipping_Request::scheme()
	 * @used-by Df_Shipping_Request::getUri()
	 * @return string
	 */
	protected function scheme() {return 'https';}
}