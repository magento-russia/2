<?php
namespace Df\NovaPoshta;
class Request extends \Df\Shipping\Request {
	/**
	 * @override
	 * @see \Df\Shipping\Request::adjustHttpClient()
	 * @used-by \Df\Shipping\Request::getHttpClient()
	 * @param \Zend_Http_Client $httpClient
	 * @return void
	 */
	protected function adjustHttpClient(\Zend_Http_Client $httpClient) {
		$httpClient->setCookie(new \Zend_Http_Cookie('language', 'ru', 'novaposhta.ua'));
	}

	/**
	 * 2016-10-29
	 * @override
	 * @see \Df\Shipping\Request::uri()
	 * @used-by \Df\Shipping\Request::zuri()
	 * @return string
	 */
	protected function uri() {return 'https://novaposhta.ua/';}
}