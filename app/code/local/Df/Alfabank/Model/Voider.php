<?php
class Df_Alfabank_Model_Voider extends Df_Payment_Model_Handler {
	/**
	 * @override
	 * @return void
	 */
	public function handleInternal() {
		Df_Alfabank_Model_Request_Void::i(array(
			Df_Alfabank_Model_Request_Void::P__PAYMENT_METHOD => $this->getMethod()
			,Df_Alfabank_Model_Request_Refund::P__ORDER_PAYMENT => $this->getOrderPayment()
		))->getResponse();
	}
}