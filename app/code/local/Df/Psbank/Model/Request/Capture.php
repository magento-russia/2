<?php
class Df_Psbank_Model_Request_Capture extends Df_Psbank_Model_Request_Secondary {
	/**
	 * @override
	 * @return string
	 */
	protected function getGenericFailureMessageUniquePart() {
		return 'снятии ранее зарезервированных средств с карты покупателя';
	}
	/**
	 * @override
	 * @return int
	 */
	protected function getTransactionType() {return 21;}
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Psbank_Model_Payment $paymentMethod
	 * @param Mage_Sales_Model_Order_Payment $payment
	 * @return Df_Psbank_Model_Request_Capture
	 */
	public static function i(
		Df_Psbank_Model_Payment $paymentMethod, Mage_Sales_Model_Order_Payment $payment
	) {
		return new self(array(
			self::P__PAYMENT_METHOD => $paymentMethod
			, self::P__ORDER_PAYMENT => $payment
		));
	}
}


