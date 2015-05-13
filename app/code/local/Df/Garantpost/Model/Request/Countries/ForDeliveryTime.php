<?php
class Df_Garantpost_Model_Request_Countries_ForDeliveryTime
	extends Df_Garantpost_Model_Request_Countries {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(),array(
			'Referer' => 'http://www.garantpost.ru/tools/transint'
		));
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getOptionsSelector() {return '.tarif .frm option';}

	/**
	 * @override
	 * @return string
	 */
	protected function getQueryPath() {return '/tools/transint';}

	const _CLASS = __CLASS__;
	/** @return Df_Garantpost_Model_Request_Countries_ForDeliveryTime */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}