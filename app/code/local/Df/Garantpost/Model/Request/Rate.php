<?php
class Df_Garantpost_Model_Request_Rate extends Df_Garantpost_Model_Request {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array('Referer' => 'http://www.garantpost.ru/tools/calc') + parent::getHeaders();
	}
	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/tools/calc';}
	/**
	 * @override
	 * @return string
	 */
	protected function getRequestMethod() {return Zend_Http_Client::POST;}
	const _C = __CLASS__;
}