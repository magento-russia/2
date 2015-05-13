<?php
/**
 * @method Df_Psbank_Model_Payment getMethod()
 */
class Df_Psbank_Model_Capturer extends Df_Payment_Model_Handler {
	/**
	 * @override
	 * @return void
	 */
	public function handleInternal() {
		Df_Psbank_Model_Request_Capture::i($this->getMethod(), $this->getOrderPayment())->process();
	}
}