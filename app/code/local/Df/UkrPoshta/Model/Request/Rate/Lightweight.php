<?php
class Df_UkrPoshta_Model_Request_Rate_Lightweight extends Df_UkrPoshta_Model_Request_Rate {
	/**
	 * @override
	 * @return array(string => string)
	 */
	protected function getHeaders() {
		return array_merge(parent::getHeaders(), array(
			'Referer' => 'http://services.ukrposhta.com/CalcUtil/CourierDelivery31.aspx'
		));
	}
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_UkrPoshta_Model_Request_Rate_Lightweight
	 */
	public static function i(array $parameters = array()) {
		return new self(array(self::P__QUERY_PARAMS => $parameters));
	}
}