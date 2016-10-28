<?php
namespace Df\Psbank\Action\Confirm;
class Payment extends \Df\Psbank\Action\Confirm {
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
}