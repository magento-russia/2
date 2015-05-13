<?php
class Df_Masterbank_Model_Action_Confirm_Capture extends Df_Masterbank_Model_Action_Confirm {
	/**
	 * @override
	 * @return string
	 */
	protected function getResponseObjectClass() {return Df_Masterbank_Model_Response_Capture::_CLASS;}
	/**
	 * @override
	 * @return bool
	 */
	protected function needInvoice() {return false;}
	const _CLASS = __CLASS__;
	/**
	 * @static
	 * @param Df_Masterbank_ConfirmController $controller
	 * @return Df_Masterbank_Model_Action_Confirm_Capture
	 */
	public static function i(Df_Masterbank_ConfirmController $controller) {
		return new self(array(self::P__CONTROLLER => $controller));
	}
}