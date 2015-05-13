<?php
class Df_Psbank_Model_Action_Confirm_Void extends Df_Psbank_Model_Action_Confirm {
	/**
	 * @override
	 * @return string[]
	 */
	protected function getParamsForSignature() {
		return array(
			'ORDER', 'AMOUNT', 'CURRENCY', 'ORG_AMOUNT', 'RRN', 'INT_REF', 'TRTYPE', 'TERMINAL'
			, 'BACKREF', 'EMAIL', 'TIMESTAMP', 'NONCE', 'RESULT', 'RC', 'RCTEXT'
		);
	}
	/**
	 * @override
	 * @return bool
	 */
	protected function needCapture() {return false;}
	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {return false;}

	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Psbank_ConfirmController $controller
	 * @return Df_Psbank_Model_Action_Confirm_Void
	 */
	public static function i(Df_Psbank_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}