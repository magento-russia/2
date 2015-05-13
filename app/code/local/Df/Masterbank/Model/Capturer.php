<?php
class Df_Masterbank_Model_Capturer extends Df_Payment_Model_Handler {
	/**
	 * @override
	 * @return void
	 */
	public function handleInternal() {
		Df_Masterbank_Model_Request_Capture::i(array(
			Df_Masterbank_Model_Request_Capture::P__PAYMENT_METHOD => $this->getMethod()
			,Df_Masterbank_Model_Request_Capture::P__ORDER_PAYMENT => $this->getOrderPayment()
		))->process();
	}
}