<?php
class Df_Avangard_Model_Refunder extends Df_Payment_Model_Handler {
	/**
	 * @override
	 * @return void
	 */
	public function handleInternal() {
		Df_Avangard_Model_Request_Refund::i(array(
			Df_Avangard_Model_Request_Refund::P__PAYMENT_METHOD => $this->getMethod()
			,Df_Avangard_Model_Request_Refund::P__ORDER_PAYMENT => $this->getOrderPayment()
		))->getResponse();
	}
}