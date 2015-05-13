<?php
class Df_Garantpost_Model_Request_Countries_ForRate
	extends Df_Garantpost_Model_Request_Countries {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(),array(
			'Referer' => 'http://www.garantpost.ru/tools/calc'
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getOptionsSelector() {return 'select[name="i_to_1"] option';}

	/** @return array(string => string|int|float|bool) */
	protected function getPostParameters() {
		return array_merge(parent::getPostParameters(), array(
			'calc_type' => 'world'
		));
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

	const _CLASS = __CLASS__;
	/** @return Df_Garantpost_Model_Request_Countries_ForRate */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}