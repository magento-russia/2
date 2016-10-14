<?php
class Df_Garantpost_Model_Request_Countries_ForDeliveryTime
	extends Df_Garantpost_Model_Request_Countries {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array('Referer' => 'http://www.garantpost.ru/tools/transint') + parent::getHeaders();
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

	/** @return Df_Garantpost_Model_Request_Countries_ForDeliveryTime */
	public static function s() {static $r; return $r ? $r : $r = new self;}
}