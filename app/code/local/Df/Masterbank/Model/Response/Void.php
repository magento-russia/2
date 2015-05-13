<?php
class Df_Masterbank_Model_Response_Void extends Df_Masterbank_Model_Response {
	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		return Mage_Sales_Model_Order_Payment_Transaction::TYPE_VOID;
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isTransactionClosed() {
		return true;
	}

	/**
	 * @override
	 * @return string
	 */
	protected function getResponseObjectClass() {
		return Df_Masterbank_Model_Response_Capture::_CLASS;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Masterbank_Model_Response_Void
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}