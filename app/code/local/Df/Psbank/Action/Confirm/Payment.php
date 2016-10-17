<?php
class Df_Psbank_Action_Confirm_Payment extends Df_Psbank_Action_Confirm {
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

	/** @used-by Df_Psbank_ConfirmController::getActionMap() */

}