<?php
class Df_Masterbank_Model_Response_Capture extends Df_Masterbank_Model_Response {
	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		return Mage_Sales_Model_Order_Payment_Transaction::TYPE_CAPTURE;
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isTransactionClosed() {
		return true;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Masterbank_Model_Response_Capture
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}