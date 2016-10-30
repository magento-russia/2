<?php
namespace Df\NovaPoshta;
class Request extends \Df\Shipping\Request {
	/**
	 * @override
	 * @see \Df\Shipping\Request::adjust()
	 * @used-by \Df\Shipping\Request::client()
	 * @param \Zend_Http_Client $client
	 * @return void
	 */
	protected function adjust(\Zend_Http_Client $client) {
		$client->setCookie(new \Zend_Http_Cookie('language', 'ru', 'novaposhta.ua'));
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