<?php
class Df_Masterbank_Model_Response_Payment extends Df_Masterbank_Model_Response {
	/**
	 * @override
	 * @return string
	 */
	public function getTransactionType() {
		/**
		 * Тип должен быть именно таким!
		 * Если вернуть Mage_Sales_Model_Order_Payment_Transaction::TYPE_PAYMENT,
		 * то функция разблокировки средств из административного интерфейса не будет доступна.
		 * @see Mage_Sales_Model_Order_Payment::getAuthorizationTransaction()
		 */
		return Mage_Sales_Model_Order_Payment_Transaction::TYPE_AUTH;
	}

	/**
	 * @override
	 * @return bool
	 */
	public function isTransactionClosed() {
		return false;
	}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param array(string => mixed) $parameters [optional]
	 * @return Df_Masterbank_Model_Response_Payment
	 */
	public static function i(array $parameters = array()) {return new self($parameters);}
}