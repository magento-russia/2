<?php
class Df_Psbank_Model_Action_Confirm_Capture extends Df_Psbank_Model_Action_Confirm {
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

	/** @used-by Df_Psbank_ConfirmController::getActionMap() */


	/**
	 * @param Df_Psbank_ConfirmController $c
	 * @return Df_Psbank_Model_Action_Confirm_Capture
	 */
	public static function i(Df_Psbank_ConfirmController $c) {return self::ic(__CLASS__, $c);}
}