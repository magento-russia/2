<?php
class Df_Shipping_Model_Rate_Result_Error extends Mage_Shipping_Model_Rate_Result_Error {
	const P__CARRIER = 'carrier';
	const P__CARRIER_TITLE = 'carrier_title';
	const P__ERROR = 'error';
	const P__ERROR_MESSAGE = 'error_message';
	/**
	 * @static
	 * @param Df_Shipping_Model_Carrier $carrier
	 * @param $message
	 * @return Df_Shipping_Model_Rate_Result_Error
	 */
	public static function i(Df_Shipping_Model_Carrier $carrier, $message) {
		return new self(array(
			self::P__CARRIER => $carrier->getCarrierCode()
			,self::P__CARRIER_TITLE => $carrier->getTitle()
			,self::P__ERROR => true
			,self::P__ERROR_MESSAGE => $message
		));
	}
}