<?php
class Df_Kkb_Model_Voider extends Df_Payment_Model_Handler {
	/**
	 * @override
	 * @return void
	 */
	public function handleInternal() {
		Df_Kkb_Model_Request_Void::i(array(
			Df_Kkb_Model_Request_Void::P__PAYMENT_METHOD => $this->getMethod()
			,Df_Kkb_Model_Request_Capture::P__ORDER_PAYMENT => $this->getOrderPayment()
		))->getResponse();
	}
}