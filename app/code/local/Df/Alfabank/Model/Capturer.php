<?php
class Df_Alfabank_Model_Capturer extends Df_Payment_Model_Handler {
	/**
	 * @override
	 * @return void
	 */
	public function handleInternal() {
		Df_Alfabank_Model_Request_Capture::i(array(
			Df_Alfabank_Model_Request_Capture::P__AMOUNT => $this->getAmount()
			,Df_Alfabank_Model_Request_Capture::P__PAYMENT_METHOD => $this->getMethod()
			,Df_Alfabank_Model_Request_Capture::P__ORDER_PAYMENT => $this->getOrderPayment()
		))->getResponse();
	}
}