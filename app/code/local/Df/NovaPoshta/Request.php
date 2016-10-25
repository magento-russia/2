<?php
class Df_NovaPoshta_Request extends \Df\Shipping\Request {
	/**
	 * @override
	 * @see \Df\Shipping\Request::adjustHttpClient()
	 * @used-by \Df\Shipping\Request::getHttpClient()
	 * @param Zend_Http_Client $httpClient
	 * @return void
	 */
	protected function adjustHttpClient(Zend_Http_Client $httpClient) {
		$httpClient->setCookie(new Zend_Http_Cookie('language', 'ru', 'novaposhta.ua'));
	}

	/**
	 * @override
	 * @see \Df\Shipping\Request::getQueryHost()
	 * @used-by \Df\Shipping\Request::getUri()
	 * @return string
	 */
	protected function getQueryHost() {return 'novaposhta.ua';}

	/**
	 * 2016-10-24
	 * @override
	 * @see \Df\Shipping\Request::scheme()
	 * @used-by \Df\Shipping\Request::getUri()
	 * @return string
	 */
	protected function scheme() {return 'https';}
}