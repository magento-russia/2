<?php
class Df_Psbank_Model_Action_Confirm_Payment extends Df_Psbank_Model_Action_Confirm {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getParamsForSignature() {
		return array(
			'AMOUNT', 'CURRENCY', 'ORDER', 'MERCH_NAME', 'MERCHANT', 'TERMINAL', 'EMAIL', 'TRTYPE'
			, 'TIMESTAMP', 'NONCE', 'BACKREF', 'RESULT', 'RC', 'RCTEXT', 'AUTHCODE', 'RRN', 'INT_REF'
		);
	}
	/**
	 * @override
	 * @return bool
	 */
	protected function needCapture() {return true;}
	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {return true;}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Psbank_ConfirmController $controller
	 * @return Df_Psbank_Model_Action_Confirm_Payment
	 */
	public static function i(Df_Psbank_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}